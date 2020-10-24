#!/usr/bin/env bash

set -eou pipefail

VERSION="0.1.6"

if [[ ! $(gotk) ]]; then
  echo "gotk needs to be installed - https://toolkit.fluxcd.io/get-started/#install-the-toolkit-cli"
  exit 1
fi

gotk install \
  --version="${VERSION}" \
  --components=source-controller,kustomize-controller,helm-controller,notification-controller \
  --namespace=gotk-system \
  --network-policy=false \
  --arch=arm64 \
  --export > "./namespaces/gotk-system/toolkit-components.yaml"

git add -A && git commit -sam "update toolkit version: ${VERSION}" && git push
