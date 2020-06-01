#!/usr/bin/env bash
set -eou pipefail

kubectl apply -f https://raw.githubusercontent.com/fluxcd/helm-operator/v1.0.0/deploy/crds.yaml

helm repo add fluxcd https://charts.fluxcd.io

helm upgrade -i helm-operator fluxcd/helm-operator \
    --namespace flux \
    --values=config/helm-operator/values.yaml

