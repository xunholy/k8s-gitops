#!/usr/bin/env bash
set -eo pipefail
shopt -s extglob

[[ -n $DEBUG ]] && set -x

SEALED_SECRETS_FILES=$(find "k8s" -type f -name '*.encrypted.yaml')

for FILE in $SEALED_SECRETS_FILES; do
  echo "INFO - Validating $FILE"
  yq validate -d'*' "$FILE"
  kubeseal --validate --controller-name=sealed-secrets --kubeconfig ~/.kube/config < "$FILE"
done
