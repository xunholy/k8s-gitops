# OCI Migration Guide

This guide documents the migration of Helm charts from HelmRepository to OCIRepository following oneDr0p's patterns.

## Status Overview

### ✅ Already Migrated to OCI (Tier 1)

These charts are already using OCIRepositories:

| Chart | Namespace | OCI Registry | Status |
|-------|-----------|--------------|--------|
| cert-manager | network-system | `oci://quay.io/jetstack/charts/cert-manager` | ✅ Official |
| kube-prometheus-stack | observability | `oci://ghcr.io/prometheus-community/charts/kube-prometheus-stack` | ✅ Official |
| grafana-operator | observability | `oci://ghcr.io/grafana/helm-charts/grafana-operator` | ✅ Official |
| external-secrets | external-secrets | `oci://ghcr.io/external-secrets/charts/external-secrets` | ✅ Official |
| external-dns | network-system | `oci://ghcr.io/home-operations/charts-mirror/external-dns` | ⚠️ Mirror |
| gha-runner-scale-set-controller | actions-runner-system | `oci://ghcr.io/actions/actions-runner-controller-charts/gha-runner-scale-set-controller` | ✅ Official |
| gha-runner-scale-set | actions-runner-system | `oci://ghcr.io/actions/actions-runner-controller-charts/gha-runner-scale-set` | ✅ Official |

### 🆕 New OCIRepository Manifests Created

The following OCIRepository manifests have been generated and are ready for migration:

| # | Chart | Location | OCI Registry |
|---|-------|----------|--------------|
| 1 | **kyverno** | `security-system/kyverno/app/ocirepository.yaml` | `oci://ghcr.io/kyverno/kyverno` |
| 2 | **falco** | `security-system/falco/app/ocirepository.yaml` | `oci://ghcr.io/falcosecurity/charts/falco` |
| 3 | **falco-exporter** | `security-system/falco-exporter/app/ocirepository.yaml` | `oci://ghcr.io/falcosecurity/charts/falco-exporter` |
| 4 | **tetragon** | `kube-system/tetragon/app/ocirepository.yaml` | `oci://ghcr.io/cilium/charts/tetragon` |
| 5 | **crossplane** | `crossplane-system/crossplane/app/ocirepository.yaml` | `oci://xpkg.upbound.io/upbound/crossplane` |
| 6 | **oauth2-proxy** | `network-system/oauth2-proxy/app/ocirepository.yaml` | `oci://ghcr.io/oauth2-proxy/charts/oauth2-proxy` ✅ |
| 7 | **opentelemetry-operator** | `observability/otel/app/ocirepository.yaml` | `oci://ghcr.io/open-telemetry/opentelemetry-helm-charts/opentelemetry-operator` |

### ❌ Charts That Cannot Use OCI

These charts do NOT have OCI registry support and must continue using HelmRepository:

| Chart | Reason | Current Source |
|-------|--------|----------------|
| **ingress-nginx** | charts-mirror lags behind official releases (4.13.0 vs 4.14.1) | `ingress-nginx-chart` HelmRepository |
| **vpa** | Fairwinds only publishes to traditional Helm repos | `fairwinds-charts` HelmRepository |

---

## Migration Steps

For each chart, follow these steps:

### Step 1: Update HelmRelease

Replace the `chart.spec` section with `chartRef`:

**Before:**
```yaml
spec:
  chart:
    spec:
      chart: kyverno
      version: 3.x.x
      sourceRef:
        kind: HelmRepository
        name: kyverno-charts
        namespace: flux-system
```

**After:**
```yaml
spec:
  chartRef:
    kind: OCIRepository
    name: kyverno  # Must match OCIRepository metadata.name
```

### Step 2: Update kustomization.yaml

Add the OCIRepository to resources:

```yaml
resources:
  - helmrelease.yaml
  - ocirepository.yaml  # Add this line
```

### Step 3: Test Reconciliation

```bash
# Force reconcile the OCIRepository
flux reconcile source oci <name> -n <namespace>

# Force reconcile the HelmRelease
flux reconcile helmrelease <name> -n <namespace>

# Watch for success
flux get helmrelease -n <namespace> <name>
```

### Step 4: Verify Chart Version

```bash
# Check the OCI tag/version
flux get source oci -n <namespace> <name>

# Verify HelmRelease is using the OCI chart
kubectl get hr -n <namespace> <name> -o yaml | grep -A 5 "chartRef:"
```

---

## Priority Migration Order

### Phase 1 - Security (Week 1)
Priority: **CRITICAL**

1. **kyverno** - Policy engine
   - Update: `kubernetes/apps/base/security-system/kyverno/app/helmrelease.yaml`
   - Update: `kubernetes/apps/base/security-system/kyverno/app/kustomization.yaml`

2. **falco** - Runtime security
   - Update: `kubernetes/apps/base/security-system/falco/app/helmrelease.yaml`
   - Update: `kubernetes/apps/base/security-system/falco/app/kustomization.yaml`

3. **falco-exporter** - Falco metrics
   - Update: `kubernetes/apps/base/security-system/falco-exporter/app/helmrelease.yaml`
   - Update: `kubernetes/apps/base/security-system/falco-exporter/app/kustomization.yaml`

4. **tetragon** - eBPF security
   - Update: `kubernetes/apps/base/kube-system/tetragon/app/helmrelease.yaml`
   - Update: `kubernetes/apps/base/kube-system/tetragon/app/kustomization.yaml`

### Phase 2 - Networking & Infrastructure (Week 2)
Priority: **HIGH**

5. **oauth2-proxy** - Authentication proxy ✅ ALREADY MIGRATED
   - Update: `kubernetes/apps/base/network-system/oauth2-proxy/app/helmrelease.yaml`
   - Update: `kubernetes/apps/base/network-system/oauth2-proxy/app/kustomization.yaml`

6. **crossplane** - Infrastructure provisioning
   - Update: `kubernetes/apps/base/crossplane-system/crossplane/app/helmrelease.yaml`
   - Update: `kubernetes/apps/base/crossplane-system/crossplane/app/kustomization.yaml`

### Phase 3 - Observability (Week 3)
Priority: **MEDIUM**

7. **opentelemetry-operator** - Observability
   - Update: `kubernetes/apps/base/observability/otel/app/helmrelease.yaml`
   - Update: `kubernetes/apps/base/observability/otel/app/kustomization.yaml`

**Notes**:
- VPA and ingress-nginx removed from migration - no suitable OCI registries available
- oauth2-proxy already successfully migrated and running in production

---

## Example Migration: kyverno

### 1. Check Current HelmRelease

```bash
kubectl get hr -n security-system kyverno -o yaml
```

### 2. Update HelmRelease

Edit `kubernetes/apps/base/security-system/kyverno/app/helmrelease.yaml`:

```yaml
---
apiVersion: helm.toolkit.fluxcd.io/v2
kind: HelmRelease
metadata:
  name: kyverno
  namespace: security-system
spec:
  interval: 1h  # Match oneDr0p pattern
  chartRef:
    kind: OCIRepository
    name: kyverno
  # ... rest of spec unchanged
```

### 3. Update Kustomization

Edit `kubernetes/apps/base/security-system/kyverno/app/kustomization.yaml`:

```yaml
apiVersion: kustomize.config.k8s.io/v1beta1
kind: Kustomization
resources:
  - helmrelease.yaml
  - ocirepository.yaml  # Add this
  # ... other resources
```

### 4. Commit and Push

```bash
git add kubernetes/apps/base/security-system/kyverno/
git commit -m "feat(kyverno): migrate to OCI registry"
git push
```

### 5. Monitor Reconciliation

```bash
# Watch Flux reconcile
flux get all -A | grep kyverno

# Check for errors
flux logs --all-namespaces --since=5m | grep kyverno
```

---

## Verification Checklist

For each migrated chart:

- [ ] OCIRepository created in app directory
- [ ] HelmRelease updated to use `chartRef`
- [ ] kustomization.yaml includes ocirepository.yaml
- [ ] OCIRepository reconciles successfully
- [ ] HelmRelease reconciles successfully
- [ ] Chart version matches expected
- [ ] Application pods running normally
- [ ] Monitor for 24 hours for stability
- [ ] Document any issues encountered

---

## Rollback Procedure

If migration fails:

### Option 1: Revert HelmRelease

```yaml
# Restore chart.spec pattern
spec:
  chart:
    spec:
      chart: <chart-name>
      version: <version>
      sourceRef:
        kind: HelmRepository
        name: <repo-name>
        namespace: flux-system
```

### Option 2: Git Revert

```bash
git revert <commit-hash>
git push
```

---

## Common Issues & Solutions

### Issue: OCIRepository fails to reconcile

**Symptom:**
```
OCIRepository/kyverno.security-system - reconciliation failed: failed to pull artifact from oci://...
```

**Solutions:**
1. Verify OCI registry URL is correct
2. Check internet connectivity
3. Verify namespace exists
4. Check for rate limiting

### Issue: HelmRelease shows "chart not found"

**Symptom:**
```
HelmRelease/kyverno.security-system - Helm install failed: chart "kyverno" not found
```

**Solutions:**
1. Ensure OCIRepository name matches chartRef.name
2. Verify OCIRepository is in same namespace
3. Wait for OCIRepository to reconcile first

### Issue: Version mismatch

**Symptom:**
Chart deploys but with unexpected version

**Solutions:**
1. Check OCIRepository ref.tag or ref.semver
2. Use exact tag instead of semver for critical apps
3. Verify renovate automation is updating tags correctly

---

## Post-Migration

### Cleanup Old HelmRepositories

After successful migration, remove unused HelmRepository resources:

```bash
# List HelmRepositories
kubectl get helmrepository -n flux-system

# Delete unused ones
kubectl delete helmrepository -n flux-system <repo-name>
```

**Note:** Only delete if no other charts reference them!

### Update Renovate Configuration

Ensure Renovate is configured to update OCI tags:

```yaml
# renovate.json
{
  "kubernetes": {
    "fileMatch": ["kubernetes/.+\\.yaml$"],
    "ignorePaths": [
      "kubernetes/flux/"
    ]
  },
  "regexManagers": [
    {
      "fileMatch": ["kubernetes/.+ocirepository\\.yaml$"],
      "matchStrings": [
        "tag:\\s+(?<currentValue>.*?)\\n"
      ],
      "datasourceTemplate": "docker",
      "depNameTemplate": "{{{depName}}}"
    }
  ]
}
```

---

## Benefits Realized

After migration, you'll gain:

✅ **Unified Storage** - Charts stored alongside container images
✅ **Better Caching** - OCI registries have superior caching
✅ **Signed Artifacts** - Cosign/Notation support for chart signing
✅ **Renovate Integration** - Better automation for OCI-based charts
✅ **Rate Limiting** - Avoid HelmRepository rate limits
✅ **Air-Gapped Ready** - Easier to mirror for offline environments
✅ **Consistency** - All charts use same pattern (100% OCI)

---

## References

- [oneDr0p home-ops Repository](https://github.com/onedr0p/home-ops)
- [Flux OCI Documentation](https://fluxcd.io/flux/components/source/ocirepositories/)
- [Helm OCI Registries](https://helm.sh/docs/topics/registries/)
- Repository CLAUDE.md - GitOps patterns and conventions
