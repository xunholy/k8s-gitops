#!/usr/bin/env bash

set -eou pipefail

export GITHUB_USER=xunholy
export GITHUB_TOKEN=${GITHUB_TOKEN}
export GITHUB_REPO=k8s-gitops
export CLUSTER="${CLUSTER:-production}"

## This will deep merge your kube config assuming you used the k8s-cluster-installation to bootstrap your cluster.
## You will need to modify the PATH to the location of the playbooks on your local machine.
# KUBECONFIG=~/projects/install/ansible/playbooks/output/k8s-config.yaml:~/.kube/config kubectl config view --flatten > ~/.kube/config.tmp && \
#   mv ~/.kube/config.tmp ~/.kube/config

flux >/dev/null || \
  ( echo "flux needs to be installed - https://toolkit.fluxcd.io/get-started/#install-the-toolkit-cli" && exit 1 )

## Untaint master nodes if you don't have enough workers in your homelab.
# [[ ! $(kubectl taint nodes --all node-role.kubernetes.io/master-) ]] && echo "Masters untainted"

## Use the existing sealed-secret private key if it exists.
## Currently uses only prod key as this is shared between all clusters currently.
## To decrypt this secret requires you to have IAM permissions to GCP KMS.
if [[ -f k8s/clusters/production/secrets/sealed-secret-private-key.enc.yaml ]]; then
  echo "Applying existing sealed-secret key"
  sops --decrypt "k8s/clusters/production/secrets/sealed-secret-private-key.enc.yaml" | kubectl apply -f -
fi

# Check the cluster meets the fluxv2 prerequisites
flux check --pre || \
  ( echo "Prerequisites were not satisfied" && exit 1 )

echo "Applying cluster: ${CLUSTER}"
flux bootstrap github \
  --owner="${GITHUB_USER}" \
  --repository="${GITHUB_REPO}" \
  --path=k8s/clusters/"${CLUSTER}" \
  --branch=main \
  --network-policy=false \
  --personal
