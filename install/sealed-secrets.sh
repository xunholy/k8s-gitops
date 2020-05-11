#!/usr/bin/env bash

set -eou pipefail

MASTER_KEY="../.secrets/master-sealed-secret.yaml"

kubectl delete secret -l sealedsecrets.bitnami.com/sealed-secrets-key=active -n kube-system
if [[ -f ${MASTER_KEY} ]]; then
  echo "Applying Sealed Secrets master key"
  kubectl apply -f ${MASTER_KEY}
fi

kubectl delete pod -l name=sealed-secrets -n kube-system
