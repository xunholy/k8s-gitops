#!/usr/bin/env bash

set -eou pipefail

CLEAN=${CLEAN:-true}

export GHUSER="raspbernetes"

if [[ ! $(fluxctl) ]]; then
    LOCAL_OS=$(uname)
    if [[ $LOCAL_OS == Darwin ]]; then
        brew install fluxctl
    elif [[ $LOCAL_OS == Linux ]]; then
        sudo snap install fluxctl
    else
        echo "Fluxctl needs to be manually installed - https://docs.fluxcd.io/en/latest/references/fluxctl/"
        exit 1
    fi
fi

# Ignore if namespace already exists
[[ ! $(kubectl get ns flux) ]] && kubectl create ns flux

fluxctl install \
    --git-user=${GHUSER} \
    --git-email=${GHUSER}@users.noreply.github.com \
    --git-url=git@github.com:${GHUSER}/k8s-gitops.git \
    --git-path=namespaces \
    --registry-disable-scanning \
    --git-readonly \
    --namespace=flux > flux.yaml

sed -i'.bak' "s/docker.io\/fluxcd\/flux/docker.io\/raspbernetes\/flux/g" flux.yaml

[[ -f flux.yaml ]] && kubectl apply -f flux.yaml

echo -e "\nCompleted..."
echo "Follow these instructions to setup SSH keys: https://docs.fluxcd.io/en/latest/tutorials/get-started/#giving-write-access"
# TODO: https://docs.fluxcd.io/en/latest/guides/provide-own-ssh-key/

if [[ -f "flux.yaml" && $CLEAN == true ]]; then
    echo -e "\nCleaning up manifests."
    echo "Set CLEAN=false if you wish for this not to occur."
    rm -rf flux.yaml flux.yaml.bak
fi