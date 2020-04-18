#!/usr/bin/env bash

set -eou pipefail

MASTER_KEY="../.k8s/sealedsecrets-key.yaml"

kubectl delete secret -l sealedsecrets.bitnami.com/sealed-secrets-key=active -n sealed-secrets
[[ -f ${MASTER_KEY} ]] && kubectl apply -f ${MASTER_KEY} || exit 1
kubectl delete pod -l name=sealed-secrets-controller -n sealed-secrets
