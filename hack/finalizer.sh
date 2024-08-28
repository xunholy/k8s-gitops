#!/bin/bash

# Set the target namespace
NAMESPACE="flux-system"

# Get all resource types in the cluster
RESOURCES=$(kubectl api-resources --verbs=list --namespaced=false -o name)

# Loop over each resource type and force update the finalizer for all resources of that type
for RESOURCE in $RESOURCES; do
  # Get all resources of this type in the target namespace
  RESOURCE_NAMES=$(kubectl get $RESOURCE -A -o name)
  # Loop over each resource name and force update the finalizer
  for RESOURCE_NAME in $RESOURCE_NAMES; do
  # Update the finalizer for each resource in parallel using xargs
  echo "$RESOURCE_NAMES" | xargs -n1 -P4 -I{} kubectl patch {} --namespace=$NAMESPACE -p '{"metadata":{"finalizers":null}}' --type=merge
  done
done
