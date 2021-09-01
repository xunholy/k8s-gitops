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
  --version=1.10.3 \
  --namespace=kube-system \
  --values=k8s/namespaces/base/kube-system/cilium/install/values.yaml
```
