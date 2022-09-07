# Sidero Cilium Installation

Generate a cilium configuration for each cluster to bootstrap with through sidero

Example:

```bash
helm template cilium cilium/cilium \
  --version 1.12.0 \
  --namespace=kube-system \
  --values=k8s/namespaces/base/kube-system/cilium/install/1.12.0.yaml \
  --set=k8sServiceHost=192.168.50.200 > k8s/clusters/sidero-control/integrations/cilium/talos-default.yaml
```
