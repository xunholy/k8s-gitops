#!/bin/bash

# Set the target namespace
NAMESPACE="flux-system"

# # Get all resource types in the namespace and delete them
RESOURCES=$(kubectl api-resources --verbs=list --namespaced -o name)
# echo $RESOURCES | xargs -n1 -P10 kubectl delete --all --namespace=$NAMESPACE --ignore-not-found=true

# For each resource type, wait for all resources to be deleted, and then remove finalizers
for RESOURCE in $RESOURCES; do
  # Wait for all resources of this type to be deleted
  while [[ $(kubectl get $RESOURCE --namespace=$NAMESPACE | wc -l) -gt 1 ]]
  do
    echo "Waiting for $RESOURCE resources to be deleted..."
    sleep 1
  done

  # Remove finalizers from all resources of this type
  kubectl get $RESOURCE --namespace=$NAMESPACE -o name | xargs -I{} kubectl patch {} --type json -p='[{"op": "remove", "path": "/metadata/finalizers"}]'
done
