# Sidero Cilium Installation

Generate a cilium configuration for each cluster to bootstrap with through sidero

Example:

```bash
 helm template cilium cilium/cilium \
  --version 1.11.0 \
  --namespace=kube-system \
  --value=k8sServiceHost=192.168.1.210 \
  --values=k8s/namespaces/base/kube-system/cilium/install/values.yaml > k8s/clusters/sidero-control/integrations/cilium/cluster-0.yaml-tmp
```
