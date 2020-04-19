#!/usr/bin/env bash

set -eou pipefail

# kubectl apply -f https://raw.githubusercontent.com/fluxcd/helm-operator/v1.0.0/deploy/crds.yaml \
#     --dry-run=none -o yaml > namespaces/flux/helm-operator.yaml
curl -o namespaces/flux/helm-operator.yaml https://raw.githubusercontent.com/fluxcd/helm-operator/v1.0.0/deploy/crds.yaml

# Only Helm 3 support enabled using helm.versions
helm upgrade -i helm-operator fluxcd/helm-operator \
    --namespace flux \
    --values=config/charts/helm-operator/values.yaml
