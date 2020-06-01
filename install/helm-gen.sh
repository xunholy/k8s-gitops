#!/usr/bin/env bash
set -eou pipefail

curl https://raw.githubusercontent.com/fluxcd/helm-operator/v1.0.0/deploy/crds.yaml -o namespaces/flux/helm-operator/000-crd.yaml

mkdir -p output

helm repo add fluxcd https://charts.fluxcd.io
helm template fluxcd/helm-operator \
    --name-template=default \
    --namespace flux \
    --values=config/helm-operator/values.yaml > output/helm-operator.yaml

kustomize build -o namespaces/flux/helm-operator/helm-operator.yaml

rm -rf output
