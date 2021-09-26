#!/usr/bin/env bash

set -eou pipefail

export CLUSTER="${CLUSTER:-production}"

if [[ ! $(flux) ]]; then
  echo "flux needs to be installed - https://toolkit.fluxcd.io/get-started/#install-the-toolkit-cli"
  exit 1
fi

flux install \
  --version=latest \
  --components=source-controller,kustomize-controller,helm-controller,notification-controller \
  --namespace=flux-system \
  --network-policy=false \
  --log-level=info \
  --export > "./k8s/clusters/${CLUSTER}/flux-system/gotk-components.yaml"
