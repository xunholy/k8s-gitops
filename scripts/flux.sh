#!/usr/bin/env bash

set -eou pipefail

CLEAN=${CLEAN:-true}

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

if [[ -f .secrets/k8s-secret-fluxcd-ssh.yaml ]]; then
    echo "Applying existing SSH key pair"
    kubectl apply -f .secrets/k8s-secret-fluxcd-ssh.yaml
fi

if [[ -f .secrets/k8s-secret-fluxcd-ssh.yaml ]]; then
    echo "Applying existing sealed-secret key"
    kubectl apply -f .secrets/k8s-secret-sealed-secret-private-key.yaml
fi

helm repo add fluxcd https://charts.fluxcd.io

helm template fluxcd/flux \
    --name-template=default \
    --namespace flux \
    --values=config/flux/values.yaml > flux.yaml

[[ -f flux.yaml ]] && kubectl apply -f flux.yaml

echo -e "\nCompleted..."
echo "Note: Follow these instructions to setup SSH keys if this is your first time: https://docs.fluxcd.io/en/latest/tutorials/get-started/#giving-write-access"

if [[ -f "flux.yaml" && $CLEAN == true ]]; then
    echo -e "\nCleaning up manifests."
    echo "Set CLEAN=false if you wish for this not to occur."
    rm -rf flux.yaml
fi
