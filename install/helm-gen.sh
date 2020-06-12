#!/usr/bin/env bash
set -eou pipefail

HELM_OPERATOR_VERSION=v1.1.0

mkdir -p output

helm repo add fluxcd https://charts.fluxcd.io
helm template fluxcd/helm-operator \
    --version="${HELM_OPERATOR_VERSION}" \
    --name-template=default \
    --namespace flux \
    --values=config/helm-operator/values.yaml > output/helm-operator.yaml

kustomize build -o namespaces/flux/helm-operator/helm-operator.yaml

rm -rf output
