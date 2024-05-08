#!/bin/bash

# Get all namespaces in the cluster
NAMESPACES=$(kubectl get namespaces -o name)

# Loop over each namespace and force update the finalizer for all Pods
for NAMESPACE_WITH_PREFIX in $NAMESPACES; do
  # Remove the prefix "namespace/" from the namespace name
  NAMESPACE=${NAMESPACE_WITH_PREFIX#namespace/}
  echo "Updating Pods in namespace $NAMESPACE"
  # Get all Pods in the namespace
  POD_NAMES=$(kubectl get pods -n $NAMESPACE -o name)
  # Update the finalizer for each Pod in parallel using xargs
  echo "$POD_NAMES" | xargs -n1 -P4 -I{} kubectl patch {} -n $NAMESPACE -p '{"metadata":{"finalizers":null}}' --type=merge
done




flux-system   namespaces    10m   False
kustomize build failed: accumulating resources: accumulation
err='merging resources from '../../base/kube-system/cilium/ks.yaml': may not add resource with an already registered id: Kustomization.v1.kustomize.toolkit.fluxcd.io/cilium.flux-system':
must build at directory: '/tmp/kustomization-548993612/namespaces/base/kube-system/cilium/ks.yaml': file is not directory
