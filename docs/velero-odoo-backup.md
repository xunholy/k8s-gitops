# Velero Odoo Backup Implementation

Tiered backup strategy for Odoo: PITR to GCS (CNPG/Barman) plus daily Velero snapshots to GCS.

## Architecture

```
┌─────────────────────────────────────────────────────────────────┐
│                    Odoo Backup Strategy                         │
├─────────────────────────────────────────────────────────────────┤
│                                                                 │
│  Tier 1: CNPG/Barman → GCS (PITR)                               │
│  ├── Continuous WAL archiving + base backups                    │
│  ├── RPO: ~5 minutes                                            │
│  ├── Use case: Point-in-time recovery                           │
│  └── Destination: gs://hayden-odoo-backups/base                 │
│                                                                 │
│  Tier 2: Velero → GCS (offsite)                                 │
│  ├── Daily snapshots (PostgreSQL PVC + filestore PVC)           │
│  ├── RPO: 24 hours                                              │
│  ├── Use case: Disaster recovery if site is lost                │
│  └── Destination: gs://hayden-velero-backups                    │
│                                                                 │
└─────────────────────────────────────────────────────────────────┘
```

## Prerequisites

- [x] GCS bucket: `hayden-velero-backups` (terraform/gcp/velero.tf)
- [x] Service account with Storage Object Admin
- [x] Ceph CSI VolumeSnapshotClass
- [ ] Velero SA key in 1Password (`velero-gcs` item)
- [x] CNPG backup to GCS running (`gs://hayden-odoo-backups/base`)
- [x] VolSync filestore backup to GCS running (`gs://hayden-odoo-backups/filestore`)
  - Velero adds a namespace-level daily snapshot to a separate bucket

---

## Step 1: GCS Credentials

Velero requires its own SA key (not `odoo-objstore`) because IAM bindings are bucket-specific.

```bash
cd terraform/gcp
terraform output -raw velero_sa_key | base64 -d > /tmp/velero-credentials.json

# Create 1Password item:
# - Vault: Kubernetes
# - Item name: velero-gcs
# - Field name: credentials
# - Field value: contents of /tmp/velero-credentials.json
```

## Step 2: Create Velero Kubernetes Resources

### 2.1 Directory Structure

```
kubernetes/apps/base/backup-system/velero/
├── app/
│   ├── helmrelease.yaml
│   ├── ocirepository.yaml
│   ├── externalsecret.yaml
│   └── kustomization.yaml
├── ks.yaml
└── schedules/
    └── odoo-backup.yaml
```

### 2.2 Namespace

```yaml
# kubernetes/apps/base/backup-system/namespace.yaml
apiVersion: v1
kind: Namespace
metadata:
  name: velero
  labels:
    pod-security.kubernetes.io/enforce: privileged
```

### 2.3 External Secret (1Password → K8s Secret)

```yaml
# kubernetes/apps/base/backup-system/velero/app/externalsecret.yaml
apiVersion: external-secrets.io/v1
kind: ExternalSecret
metadata:
  name: velero-gcs
  namespace: velero
spec:
  secretStoreRef:
    kind: ClusterSecretStore
    name: onepassword
  target:
    name: velero-gcs
    template:
      data:
        cloud: "{{ .credentials }}"
  dataFrom:
    - extract:
        key: velero-gcs
```

### 2.4 Helm Release

```yaml
# kubernetes/apps/base/backup-system/velero/app/helmrelease.yaml
apiVersion: helm.toolkit.fluxcd.io/v2
kind: HelmRelease
metadata:
  name: velero
  namespace: velero
spec:
  interval: 30m
  chartRef:
    kind: OCIRepository
    name: velero
  values:
    image:
      repository: velero/velero
      tag: v1.15.0

    initContainers:
      - name: velero-plugin-for-gcp
        image: velero/velero-plugin-for-gcp:v1.11.0
        volumeMounts:
          - name: plugins
            mountPath: /target

      - name: velero-plugin-for-csi
        image: velero/velero-plugin-for-csi:v0.8.0
        volumeMounts:
          - name: plugins
            mountPath: /target

    configuration:
      backupStorageLocation:
        - name: gcs
          provider: gcp
          bucket: hayden-velero-backups
          credential:
            name: velero-gcs
            key: cloud

      volumeSnapshotLocation:
        - name: csi-ceph-blockpool
          provider: velero.io/csi

      defaultBackupStorageLocation: gcs
      defaultVolumeSnapshotLocations: velero.io/csi:csi-ceph-blockpool

      features: EnableCSI

    credentials:
      useSecret: true
      existingSecret: velero-gcs

    snapshotsEnabled: true
    deployNodeAgent: true

    nodeAgent:
      podVolumePath: /var/lib/kubelet/pods
      privileged: true

    schedules: {}  # Managed separately via CRDs

    resources:
      requests:
        cpu: 100m
        memory: 256Mi
      limits:
        memory: 512Mi
```

### 2.5 OCI Repository

```yaml
# kubernetes/apps/base/backup-system/velero/app/ocirepository.yaml
apiVersion: source.toolkit.fluxcd.io/v1beta2
kind: OCIRepository
metadata:
  name: velero
  namespace: velero
spec:
  interval: 12h
  url: oci://ghcr.io/vmware-tanzu/helm-charts/velero
  ref:
    tag: 8.2.0
```

### 2.6 App Kustomization

```yaml
# kubernetes/apps/base/backup-system/velero/app/kustomization.yaml
apiVersion: kustomize.config.k8s.io/v1beta1
kind: Kustomization
namespace: velero
resources:
  - externalsecret.yaml
  - ocirepository.yaml
  - helmrelease.yaml
```

---

## Step 3: Odoo Backup Schedule with Hooks

### 3.1 Label resources so selection works

- PVCs and CNPG pods currently have no `app.kubernetes.io/name: odoo` label. Either:
  - Add that label to the PVC and CNPG pod template (recommended), **or**
  - Remove the labelSelector from the schedule to back up the whole `business-system` namespace.

Example labels (preferred):

```yaml
# kubernetes/apps/base/business-system/odoo/app/pvc.yaml
metadata:
  name: odoo-filestore
  namespace: business-system
  labels:
    app.kubernetes.io/name: odoo
```

```yaml
# kubernetes/apps/base/business-system/odoo/app/postgres-cluster.yaml
spec:
  instances: 3
  podTemplate:
    metadata:
      labels:
        app.kubernetes.io/name: odoo
```

### 3.2 Annotate Odoo pod for hooks

Add to Odoo HelmRelease values:

```yaml
# kubernetes/apps/base/business-system/odoo/app/helmrelease.yaml
podAnnotations:
  # Pre-backup: Signal PostgreSQL to start backup mode
  backup.velero.io/backup-volumes: filestore
  pre.hook.backup.velero.io/container: odoo
  pre.hook.backup.velero.io/command: '["/bin/sh", "-c", "echo Starting backup"]'
  post.hook.backup.velero.io/container: odoo
  post.hook.backup.velero.io/command: '["/bin/sh", "-c", "echo Backup complete"]'
```

### 3.3 CNPG Pod Hooks (Not Required)

> **Note:** CNPG pods do NOT need Velero hooks. CNPG/Barman already handles PostgreSQL
> consistency via WAL archiving (Tier 1). CSI snapshots are crash-consistent, and CNPG
> can recover from crash-consistent state using WAL replay. Adding `pg_backup_start()`
> hooks would be redundant and may fail due to CNPG's internal connection handling.

If you still want application-level quiescence for the Odoo pod, only the Odoo deployment
needs hooks (covered in 3.2).

### 3.4 Scheduled Backup

```yaml
# kubernetes/apps/base/backup-system/velero/schedules/odoo-backup.yaml
apiVersion: velero.io/v1
kind: Schedule
metadata:
  name: odoo-daily
  namespace: velero
spec:
  # Daily at 03:00 UTC (14:00 AEDT)
  schedule: "0 3 * * *"
  template:
    # Backup business-system namespace
    includedNamespaces:
      - business-system

    # Include specific resources
    includedResources:
      - persistentvolumeclaims
      - persistentvolumes
      - pods
      - deployments
      - statefulsets
      - configmaps
      - secrets

    # Label selector for Odoo resources (remove if you do not add labels)
    labelSelector:
      matchLabels:
        app.kubernetes.io/name: odoo

    # Snapshot PVCs
    snapshotVolumes: true
    storageLocation: gcs
    volumeSnapshotLocations:
      - csi-ceph-blockpool

    # Retention
    ttl: 168h  # 7 days

    # Hooks enabled
    hooks:
      resources: []

  useOwnerReferencesInBackup: false
```

---

## Step 4: Flux Kustomization

```yaml
# kubernetes/apps/base/backup-system/velero/ks.yaml
apiVersion: kustomize.toolkit.fluxcd.io/v1
kind: Kustomization
metadata:
  name: velero
  namespace: flux-system
spec:
  targetNamespace: velero
  path: ./apps/base/backup-system/velero/app
  sourceRef:
    kind: GitRepository
    name: flux-system
  interval: 30m
  prune: true
  wait: true
  dependsOn:
    - name: external-secrets
    - name: rook-ceph-cluster
  decryption:
    provider: sops
    secretRef:
      name: sops-gpg
```

---

## Step 5: Manual Backup Test

### Install Velero CLI (if not installed)

```bash
# macOS
brew install velero

# Linux
curl -LO https://github.com/vmware-tanzu/velero/releases/download/v1.15.0/velero-v1.15.0-linux-amd64.tar.gz
tar -xzf velero-v1.15.0-linux-amd64.tar.gz
sudo mv velero-v1.15.0-linux-amd64/velero /usr/local/bin/
```

### Run Test Backup

```bash
# Create on-demand backup
velero backup create odoo-test-$(date +%Y%m%d-%H%M) \
  --include-namespaces business-system \
  --snapshot-volumes \
  --wait

# Check backup status
velero backup describe odoo-test-XXXXXX --details

# List backups
velero backup get
```

---

## Step 6: Restore Procedure

```bash
# List available backups
velero backup get

# Restore to same namespace
velero restore create --from-backup odoo-daily-XXXXXX

# Restore to different namespace (DR)
velero restore create --from-backup odoo-daily-XXXXXX \
  --namespace-mappings business-system:business-system-restore
```

---

## Backup Strategy Summary

| Tier | Component | Method | Destination | Schedule | Retention | RPO |
|------|-----------|--------|-------------|----------|-----------|-----|
| 1 | PostgreSQL | CNPG WAL + base backup | `gs://hayden-odoo-backups/base` | WAL continuous, weekly base | 7 days | ~5 min |
| 2 | Namespace (DB + filestore PVCs) | Velero CSI snapshot | `gs://hayden-velero-backups` | Daily 03:00 UTC | 7 days | 24h |
| 2 (existing) | Filestore PVC | VolSync/restic | `gs://hayden-odoo-backups/filestore` | Daily 04:00 UTC | 7/4/3 (d/w/m) | 24h |

---

## Notes

- TrueNAS/MinIO tier is deferred; keep CNPG pointed at GCS until hardware is ready.
- Flux wiring: add `velero` to `kubernetes/apps/overlays/cluster-00/kustomization.yaml` resources to deploy.
- The Schedule's `hooks.resources: []` is intentional - pod-level hooks are configured via annotations (section 3.2).

---

## Implementation Status

- [ ] Create 1Password item `velero-gcs` with Velero SA key (terraform output)
- [x] Create `kubernetes/apps/base/backup-system/` directory structure
- [x] Create namespace.yaml
- [x] Create velero app resources (externalsecret, ocirepository, helmrelease, kustomization)
- [x] Create velero ks.yaml (Flux Kustomization)
- [x] Create odoo-backup schedule
- [x] Add labels to Odoo PVC
- [x] Add Velero annotations to Odoo HelmRelease
- [x] Wire up in cluster-00 overlay
- [ ] Commit and deploy
- [ ] Test manual backup
- [ ] Verify scheduled backup runs

---

## Troubleshooting

```bash
# Check Velero logs
kubectl logs -n velero deployment/velero

# Check backup logs
velero backup logs odoo-daily-XXXXXX

# Check node-agent (for file-level backup)
kubectl logs -n velero daemonset/node-agent

# Verify VolumeSnapshotClass
kubectl get volumesnapshotclass

# Check CSI snapshots
kubectl get volumesnapshot -n business-system
```

---

## References

- [Velero GCP Plugin](https://github.com/vmware-tanzu/velero-plugin-for-gcp)
- [Velero Backup Hooks](https://velero.io/docs/v1.15/backup-hooks/)
- [CloudNativePG Backup](https://cloudnative-pg.io/documentation/current/backup/)
