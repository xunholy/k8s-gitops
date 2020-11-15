#!/usr/bin/env bash

set -eou pipefail

if [[ ! $(flux) ]]; then
  echo "flux needs to be installed - https://toolkit.fluxcd.io/get-started/#install-the-toolkit-cli"
  exit 1
fi

flux install \
  --version=latest \
  --components=source-controller,kustomize-controller,helm-controller,notification-controller \
  --namespace=flux-system \
  --network-policy=false \
  --arch=arm64 \
  --log-level=info \
  --export > "./cluster/flux-system/toolkit-components.yaml"

# Uncomment for local useage
# git add -A && git commit -sam "update toolkit version to latest" && git push
