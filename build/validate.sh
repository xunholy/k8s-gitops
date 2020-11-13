#!/usr/bin/env bash

set -exou pipefail
shopt -s globstar nullglob

for FILE in **/*.encrypted.yaml; do
  echo "Validating $FILE"
  kubeseal --validate --controller-name=sealed-secrets --kubeconfig .kube/config < "$FILE"
done
