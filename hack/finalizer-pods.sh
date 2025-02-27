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
