#!/usr/bin/env bash

set -eou pipefail

MASTER_KEY=".secrets/master-sealed-secret.yaml"
echo "Deleting existing secret"
kubectl delete secret -l sealedsecrets.bitnami.com/sealed-secrets-key=active -n kube-system
echo "Applying Sealed Secrets master key"
kubectl apply -f ${MASTER_KEY}
echo "Deleting pod to reinitialize pod with new secret"
kubectl delete pod -l app.kubernetes.io/name=sealed-secrets -n kube-system
