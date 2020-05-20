#!/usr/bin/env bash
set -eou pipefail

# kubectl apply -f https://raw.githubusercontent.com/fluxcd/helm-operator/v1.0.0/deploy/crds.yaml \
#     --dry-run=none -o yaml > namespaces/flux/helm-operator.yaml
curl -o namespaces/flux/helm-operator/helm-operator-crd.yaml https://raw.githubusercontent.com/fluxcd/helm-operator/v1.0.0/deploy/crds.yaml

helm repo add fluxcd https://charts.fluxcd.io

# Only Helm 3 support enabled using helm.versions
helm template fluxcd/helm-operator \
    --name-template=default \
    --namespace flux \
    --values=config/helm-operator/values.yaml > namespaces/flux/helm-operator/helm-operator.yaml
