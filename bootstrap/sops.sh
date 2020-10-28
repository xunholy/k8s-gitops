#!/usr/bin/env bash

set -eou pipefail

shopt -s extglob

for FILE in .secrets/*.yaml; do
  echo "$FILE"
  sops --encrypt --gcp-kms projects/raspbernetes/locations/global/keyRings/sops/cryptoKeys/sops-key "$FILE" > "$FILE.enc.yaml"
  break
done




