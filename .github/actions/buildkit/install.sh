#!/usr/bin/env bash

set -eou pipefail

DEFAULT_CLUSTER="prod-a"
DEFUALT_GITHUB_REPO="k8s-gitops"
DEFAULT_GITHUB_USER="xunholy"
DEFAULT_GITHUB_BRANCH="main"

export CLUSTER="${CLUSTER:-$DEFAULT_CLUSTER}"
export GITHUB_REPO="${GITHUB_REPO:-$DEFUALT_GITHUB_REPO}"
export GITHUB_USER="${GITHUB_USER:-$DEFAULT_GITHUB_USER}"
export GITHUB_BRANCH="${GITHUB_BRANCH:-$DEFAULT_GITHUB_BRANCH}"

export GITHUB_TOKEN="${GITHUB_TOKEN}"

## This will deep merge your kube config assuming you used the k8s-cluster-installation to bootstrap your cluster.
## You will need to modify the PATH to the location of the playbooks on your local machine.
# KUBECONFIG=~/k8s-cluster-installation/ansible/playbooks/output/k8s-config.yaml:~/.kube/config kubectl config view --flatten > ~/.kube/config.tmp && \
#   mv ~/.kube/config.tmp ~/.kube/config

flux >/dev/null || \
  ( echo "flux needs to be installed - https://toolkit.fluxcd.io/get-started/#install-the-toolkit-cli" && exit 1 )

## Untaint master nodes if you don't have enough workers in your homelab.
# [[ ! $(kubectl taint nodes --all node-role.kubernetes.io/master-) ]] && echo "Masters untainted"

## Use the existing sealed-secret private key if it exists.
## Currently uses only prod key as this is shared between all clusters currently.
## To decrypt this secret requires you to have IAM permissions to GCP KMS.
if [[ -f "k8s/clusters/${CLUSTER}/secrets/sealed-secret-private-key.enc.yaml" ]]; then
  echo "Applying existing sealed-secret key"
  sops --decrypt "k8s/clusters/${CLUSTER}/secrets/sealed-secret-private-key.enc.yaml" | kubectl apply -f -
fi

if [[ -f "k8s/clusters/${CLUSTER}/secrets/sops-gpg.enc.yml" ]]; then
  echo "Applying SOPS key"
  kubectl create namespace flux-system --dry-run=client -oyaml | kubectl apply -f -
  sops --decrypt "k8s/clusters/${CLUSTER}/secrets/sops-gpg.enc.yml" | kubectl apply -f -
fi

if [[ -f "k8s/clusters/${CLUSTER}/secrets/cluster-secrets.enc.yaml" ]]; then
  echo "Applying cluster secrets"
  sops --decrypt "k8s/clusters/${CLUSTER}/secrets/cluster-secrets.enc.yaml" | kubectl apply -f -
fi

if [[ -f "k8s/clusters/${CLUSTER}/secrets/cluster-config.yaml" ]]; then
  echo "Applying cluster secrets"
  kubectl apply -f "k8s/clusters/${CLUSTER}/secrets/cluster-config.yaml"
fi

## TODO: Apply cert-manager CRDs due to lack of CRD support in helm chart
# kubectl apply -f https://github.com/jetstack/cert-manager/releases/download/v1.2.0/cert-manager.crds.yaml

# Check the cluster meets the fluxv2 prerequisites
flux check --pre || \
  ( echo "Prerequisites were not satisfied" && exit 1 )

echo "Applying cluster: ${CLUSTER}"
flux bootstrap github \
  --owner="${GITHUB_USER}" \
  --repository="${GITHUB_REPO}" \
  --path=k8s/clusters/"${CLUSTER}" \
  --branch="${GITHUB_BRANCH}" \
  --network-policy=false \
  --personal=true \
  --private=false
