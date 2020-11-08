#!/usr/bin/env bash

set -eou pipefail

# TODO: automatically update the ~/.kube/config with required context generated.
KUBECONFIG=~/.kube/config:~/projects/k8s-cluster-installation/ansible/playbooks/output/k8s-config.yaml kubectl config view --flatten > ~/.kube/config

if [[ ! $(flux) ]]; then
  echo "flux needs to be installed - https://toolkit.fluxcd.io/get-started/#install-the-toolkit-cli"
  exit 1
fi

# Untaint master nodes
# TODO: Enable Ansible to allow configuring the taints to be added/removed.
[[ ! $(kubectl taint nodes --all node-role.kubernetes.io/master-) ]] && echo "Masters untainted"

# Check the cluster meets the fluxv2 prerequisites
flux check --pre
[[ $? -ne 0 ]] && echo "Prerequisites were not satisfied" && exit 1

flux install \
  --version=latest \
  --components=source-controller,kustomize-controller,helm-controller,notification-controller \
  --namespace=flux-system \
  --network-policy=false \
  --arch=arm64

if [[ -f .secrets/k8s-secret-sealed-secret-private-key.yaml ]]; then
  echo "Applying existing sealed-secret key"
  kubectl apply -f .secrets/k8s-secret-sealed-secret-private-key.yaml
fi

if [[ -f bootstrap/repo.yaml ]]; then
  echo "Applying Repo Sync"
  kubectl apply -f bootstrap/repo.yaml
fi
