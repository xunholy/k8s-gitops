#!/usr/bin/env bash

set -eou pipefail

for FILE in $(find . -name '*.encrypted.yaml'); do
  echo "Validating $FILE"
  kubeseal --validate < "$FILE" --controller-name=sealed-secrets --kubeconfig .kube/config
done
