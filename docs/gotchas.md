# Gotchas

Unique issues we ran into forking and setting up this repo.

## Kustomize Mutating Webhook Caches Substitution Values

**Symptom:** Updated a secret (e.g., `CLOUDFLARED_TUNNEL_ID` in `cluster-secrets`), but Kustomizations still use the old value.

**Cause:** The `kustomize-mutating-webhook` bakes substitution values into `postBuild.substitute` at Kustomization creation time. Updating the source Secret doesn't automatically update existing Kustomizations.

**Fix:**
```bash
# 1. Restart the webhook
kubectl rollout restart deployment/kustomize-mutating-webhook -n flux-system

# 2. Delete the affected Kustomization
kubectl delete kustomization <name> -n <namespace>

# 3. Trigger parent to recreate it
kubectl annotate kustomization cluster -n flux-system reconcile.fluxcd.io/requestedAt="$(date +%s)" --overwrite
```
