# Odoo Filestore Backup Implementation Guide

This guide sets up VolSync with restic to backup the `odoo-filestore` PVC to GCS.

## Architecture

```
odoo-filestore PVC (Ceph RBD)
        │
        ▼
┌──────────────────┐
│   VolSync        │
│ ReplicationSource│
└────────┬─────────┘
         │ restic + rclone
         ▼
┌──────────────────┐
│  GCS Bucket      │
│ hayden-odoo-     │
│ backups/filestore│
│ (CMEK encrypted) │
└──────────────────┘
```

## Encryption

Both PostgreSQL and filestore backups are protected:

| Backup | Client-side | Server-side (CMEK) |
|--------|-------------|-------------------|
| PostgreSQL (Barman) | - | GCP KMS `backup-encryption` key |
| Filestore (Restic) | Restic password | GCP KMS `backup-encryption` key |

Filestore is double-encrypted: restic encrypts before upload, then GCS CMEK encrypts at rest.

## Prerequisites

- [x] GCS bucket exists (`hayden-odoo-backups`) with CMEK encryption
- [x] Service account with storage access (`odoo-pg-backup`)
- [x] VolSync deployed (ks.yaml added to cluster overlay)
- [x] ReplicationSource configured
- [ ] **Encrypt the restic secret with SOPS** (see below)

---

## SOPS Secret Setup (Required)

The restic credentials are stored in a SOPS-encrypted secret (GitOps pattern), not 1Password.
This ensures backup recovery only needs: **git repo + SOPS decryption key**

### Edit and encrypt the secret:

```bash
# 1. Generate a restic password
RESTIC_PW=$(openssl rand -base64 32)
echo "RESTIC_PASSWORD: $RESTIC_PW"

# 2. Get GCS service account JSON (choose one method):
# From 1Password:
#   Copy "serviceAccount" field from "odoo-objstore" item
# From Terraform:
terraform -chdir=terraform/gcp output -raw odoo_backup_sa_key | base64 -d > /tmp/gcs-sa.json
cat /tmp/gcs-sa.json

# 3. Edit the secret with SOPS (will auto-encrypt on save):
sops kubernetes/apps/base/business-system/odoo/app/secret-filestore-restic.enc.yaml

# 4. Replace the placeholder values:
#    - RESTIC_PASSWORD: paste the generated password
#    - RCLONE_CONFIG_GCS_SERVICE_ACCOUNT_CREDENTIALS: paste the full JSON
```

### Verify encryption:

```bash
# Should show ENC[AES256_GCM,...] for sensitive values
cat kubernetes/apps/base/business-system/odoo/app/secret-filestore-restic.enc.yaml
```

---

## Implementation Details

### SOPS Secret

Located at `kubernetes/apps/base/business-system/odoo/app/secret-filestore-restic.enc.yaml`.

Uses rclone backend for GCS (avoids credential file issues):
- `RESTIC_REPOSITORY`: `rclone:gcs:hayden-odoo-backups/filestore`
- `RESTIC_PASSWORD`: Encrypted with SOPS
- `RCLONE_CONFIG_GCS_*`: GCS config via environment variables

### ReplicationSource

Located at `kubernetes/apps/base/business-system/odoo/app/replicationsource.yaml`.

- **Schedule**: Daily at 4:00 UTC (15:00 AEDT)
- **Retention**: 7 daily, 4 weekly, 3 monthly snapshots
- **Method**: Ceph CSI snapshots for consistency
- **Cache**: 2Gi for faster incremental backups

---

## Deploy and Verify

1. **Commit and push changes**
   ```bash
   git add -A
   git commit -m "feat(odoo): add filestore backup with VolSync"
   git push
   ```

2. **Wait for Flux reconciliation**
   ```bash
   flux reconcile kustomization flux-system --with-source
   kubectl get kustomization -n business-system odoo
   ```

3. **Verify VolSync deployment**
   ```bash
   kubectl get pods -n volsync-system
   kubectl get replicationsource -n business-system
   ```

4. **Check initial sync status**
   ```bash
   kubectl describe replicationsource odoo-filestore -n business-system
   ```

5. **Trigger manual sync (optional)**
   ```bash
   kubectl annotate replicationsource odoo-filestore -n business-system \
     volsync.backube/trigger="$(date +%s)" --overwrite
   ```

---

## Monitoring

### Check backup status
```bash
kubectl get replicationsource -n business-system -o wide
```

### View backup logs
```bash
kubectl logs -n business-system -l volsync.backube/source=odoo-filestore -f
```

### List snapshots in GCS
```bash
# Get password from SOPS-encrypted secret
export RESTIC_PASSWORD=$(sops -d --extract '["stringData"]["RESTIC_PASSWORD"]' \
  kubernetes/apps/base/business-system/odoo/app/secret-filestore-restic.enc.yaml)

# List snapshots (requires rclone configured with GCS)
restic -r rclone:gcs:hayden-odoo-backups/filestore snapshots
```

---

## Restore Procedure

To restore the filestore:

1. **Create ReplicationDestination**
   ```yaml
   apiVersion: volsync.backube/v1alpha1
   kind: ReplicationDestination
   metadata:
     name: odoo-filestore-restore
     namespace: business-system
   spec:
     trigger:
       manual: restore-once
     restic:
       repository: odoo-filestore-restic
       destinationPVC: odoo-filestore
       copyMethod: Direct
   ```

2. **Trigger restore**
   ```bash
   kubectl annotate replicationdestination odoo-filestore-restore -n business-system \
     volsync.backube/trigger="$(date +%s)" --overwrite
   ```

---

## Complete Odoo Backup Summary

| Component | Method | Destination | Schedule |
|-----------|--------|-------------|----------|
| PostgreSQL DB | CNPG/Barman | `gs://hayden-odoo-backups/base` | Weekly (Sunday 3am UTC) |
| PostgreSQL WAL | CNPG/Barman | `gs://hayden-odoo-backups/base` | Continuous |
| Filestore | VolSync/restic | `gs://hayden-odoo-backups/filestore` | Daily (4am UTC) |

### Recovery Point Objectives
- **Database**: Point-in-time recovery to any moment (via WAL)
- **Filestore**: Daily snapshots (up to 24h data loss possible)

### Disaster Recovery Requirements

To restore from complete loss, you need:
1. **Git repository** (contains all manifests + SOPS-encrypted secrets)
2. **SOPS decryption key** (PGP key `67AF5B5A73800481D8E41667C87721FBF6BBF30C` or age key or GCP KMS access)
3. **GCS bucket access** (service account credentials are in SOPS secret)

All backup credentials are in git - no 1Password dependency for restoration.
