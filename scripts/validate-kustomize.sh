#!/usr/bin/env bash

set -o errexit

# mirror kustomize-controller build options
# v3.9.3 flas
kustomize_flags="--enable_kyaml=false --allow_id_changes=false --load_restrictor=LoadRestrictionsNone"
# v4.x.x flags
# kustomize_flags="--allow-id-changes=false --load-restrictor=LoadRestrictionsNone"
kustomize_config="kustomization.yaml"

find "k8s" -type f -name '*.yaml' -print0 | while IFS= read -r -d $'\0' file; do
  echo "INFO - Validating $file"
  yq validate -d'*' "$file"
done

find "k8s" -type f -name $kustomize_config -print0 | while IFS= read -r -d $'\0' file; do
  echo "INFO - Validating kustomization ${file/%$kustomize_config}"
  kustomize build "${file/%$kustomize_config}" $kustomize_flags | kubeval --ignore-missing-schemas
  if [[ ${PIPESTATUS[0]} != 0 ]]; then
    exit 1
  fi
done
