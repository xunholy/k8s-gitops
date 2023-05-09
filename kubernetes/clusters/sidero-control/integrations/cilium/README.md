# Sidero Cilium Installation

Generate a cilium configuration for each cluster to bootstrap with through sidero

Example:

```bash
helm template cilium cilium/cilium \
  --version 1.12.0 \
  --namespace=kube-system \
  --values=kubernetes/namespaces/base/kube-system/cilium/install/1.12.0.yaml \
  --set=k8sServiceHost=192.168.50.200 > kubernetes/clusters/sidero-control/integrations/cilium/talos-default.yaml
```
