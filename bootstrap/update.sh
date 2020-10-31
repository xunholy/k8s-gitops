#!/usr/bin/env bash

set -eou pipefail

VERSION="0.2.1"

if [[ ! $(flux) ]]; then
  echo "flux needs to be installed - https://toolkit.fluxcd.io/get-started/#install-the-toolkit-cli"
  exit 1
fi

flux install \
  --version="${VERSION}" \
  --components=source-controller,kustomize-controller,helm-controller,notification-controller \
  --namespace=flux-system \
  --network-policy=false \
  --arch=arm64 \
  --export > "./namespaces/flux-system/toolkit-components.yaml"

git add -A && git commit -sam "update toolkit version: ${VERSION}" && git push
