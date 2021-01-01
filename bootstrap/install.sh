#!/usr/bin/env bash

set -eou pipefail

# TODO: automatically update the ~/.kube/config with required context generated.
KUBECONFIG=~/.kube/config:~/projects/k8s-cluster-installation/ansible/playbooks/output/k8s-config.yaml kubectl config view --flatten > ~/.kube/config.tmp && \
  mv ~/.kube/config.tmp ~/.kube/config

flux >/dev/null || \
  echo "flux needs to be installed - https://toolkit.fluxcd.io/get-started/#install-the-toolkit-cli" && exit 1

# Untaint master nodes
# TODO: Enable Ansible to allow configuring the taints to be added/removed.
[[ ! $(kubectl taint nodes --all node-role.kubernetes.io/master-) ]] && echo "Masters untainted"

# Check the cluster meets the fluxv2 prerequisites
flux check --pre || \
  echo "Prerequisites were not satisfied" && exit 1

flux install \
  --version=latest \
  --components=source-controller,kustomize-controller,helm-controller,notification-controller \
  --namespace=flux-system \
  --network-policy=false \
  --arch=arm64

if [[ -f .secrets/git-crypt/k8s-secret-sealed-secret-private-key.yaml ]]; then
  echo "Applying existing sealed-secret key"
  kubectl apply -f .secrets/git-crypt/k8s-secret-sealed-secret-private-key.yaml
fi

if [[ -f cluster/flux-system/repo.yaml ]]; then
  echo "Applying Repo Sync"
  kubectl apply -f cluster/flux-system/repo.yaml
fi
