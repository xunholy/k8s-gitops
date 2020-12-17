# Cilium

The following installation guide can be followed [here](https://docs.cilium.io/en/v1.9/gettingstarted/kubeproxy-free/#kubeproxy-free)

```bash
kubectl -n kube-system delete ds kube-proxy
```

```bash
helm repo add cilium https://helm.cilium.io/
```

```bash
helm install cilium cilium/cilium \
  --version=1.9.1 \
  --namespace=kube-system \
  --values=cilium/install/values.yaml
```

```bash
kubectl -n kube-system patch deployments.apps cilium-operator --patch '{"spec": {"template": {"spec": {"containers": [{"name": "cilium-operator","image": "cilium/operator-dev:v1.9.1"}]}}}}'
```
