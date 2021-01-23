#!/usr/bin/env bash

set -eou pipefail

KUBECONFIG=~/projects/k8s-cluster-installation/ansible/playbooks/output/k8s-config.yaml:~/.kube/config kubectl config view --flatten > ~/.kube/config.tmp && \
  mv ~/.kube/config.tmp ~/.kube/config

if [[ ! $(flux) ]]; then
  echo "flux needs to be installed - https://toolkit.fluxcd.io/get-started/#install-the-toolkit-cli"
  exit 1
fi

# Untaint master nodes if you don't have enough workers in your homelab
# [[ ! $(kubectl taint nodes --all node-role.kubernetes.io/master-) ]] && echo "Masters untainted"

# Check the cluster meets the fluxv2 prerequisites
flux check --pre
[[ $? -ne 0 ]] && echo "Prerequisites were not satisfied" && exit 1

flux install \
  --version=latest \
  --components=source-controller,kustomize-controller,helm-controller,notification-controller \
  --namespace=flux-system \
  --network-policy=false

if [[ -f .secrets/git-crypt/k8s-secret-sealed-secret-private-key.yaml ]]; then
  echo "Applying existing sealed-secret key"
  kubectl apply -f .secrets/git-crypt/k8s-secret-sealed-secret-private-key.yaml
fi

# TODO: add cluster ARG to script
if [[ -f clusters/production/repo.yaml ]]; then
  echo "Applying Repo Sync"
  kubectl apply -f clusters/production/repo.yaml
fi
