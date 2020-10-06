#!/usr/bin/env bash

set -eou pipefail
shopt -s globstar nullglob

for FILE in **/*.encrypted.yaml; do
  echo "Validating $FILE"
  kubeseal --validate < "$FILE" --controller-name=sealed-secrets --kubeconfig .kube/config
done
