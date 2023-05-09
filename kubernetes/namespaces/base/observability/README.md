# Jaeger

TODO: Currently not supporting ARM64

# Prometheus-Operator

```bash
kubectl create secret generic grafana-admin-creds \
  --from-literal=admin-user="" \
  --from-literal=admin-password="" \
  --namespace observability --dry-run -o yaml > .secrets/k8s-secret-grafana-admin-creds.yaml
```
