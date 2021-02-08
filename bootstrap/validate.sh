#!/usr/bin/env bash
set -eo pipefail
shopt -s extglob

[[ -n $DEBUG ]] && set -x

# shellcheck disable=SC2155
REPO_ROOT=$(git rev-parse --show-toplevel)
CLUSTER_ROOT="${REPO_ROOT}/namespaces"
SEALED_SECRETS_FILES=$(find "$CLUSTER_ROOT" -name '*.encrypted.yaml')

for FILE in $SEALED_SECRETS_FILES; do
  echo "Validating $FILE"
  kubeseal --validate --controller-name=sealed-secrets --kubeconfig .kube/config < "$FILE"
done
