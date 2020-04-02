#!/usr/bin/env bash

set -eou pipefail

export GHUSER="raspbernetes"

# Ignore if namespace already exists
[[ ! $(kubectl get ns flux) ]] && kubectl create ns flux

fluxctl install \
    --git-user=${GHUSER} \
    --git-email=${GHUSER}@users.noreply.github.com \
    --git-url=git@github.com:${GHUSER}/k8s-cluster.git \
    --git-path=namespaces \
    --registry-disable-scanning \
    --git-readonly \
    --namespace=flux > flux.yaml

sed -i'.bak' "s/docker.io\/fluxcd\/flux/docker.io\/raspbernetes\/flux/g" flux.yaml

[[ -f flux.yaml ]] && kubectl apply -f flux.yaml

echo ""
echo "Completed..."
echo "Follow these instructions to setup SSH keys: https://docs.fluxcd.io/en/latest/tutorials/get-started/#giving-write-access" 
# TODO: https://docs.fluxcd.io/en/latest/guides/provide-own-ssh-key/