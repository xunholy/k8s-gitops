#!/bin/bash

set -eu

YQ_VERSION="v4.6.1"
KUSTOMIZE_VERSION="4.1.3"
KUBEVAL_VERSION="0.15.0"
SOPS_VERSION="v3.7.1"

mkdir -p $GITHUB_WORKSPACE/bin

curl -sL https://github.com/mikefarah/yq/releases/download/${YQ_VERSION}/yq_linux_amd64 -o yq

cp ./yq $GITHUB_WORKSPACE/bin
chmod +x $GITHUB_WORKSPACE/bin/yq

kustomize_url=https://github.com/kubernetes-sigs/kustomize/releases/download && \
curl -sL ${kustomize_url}/kustomize%2Fv${KUSTOMIZE_VERSION}/kustomize_v${KUSTOMIZE_VERSION}_linux_amd64.tar.gz | \
tar xz

cp ./kustomize $GITHUB_WORKSPACE/bin
chmod +x $GITHUB_WORKSPACE/bin/kustomize

curl -sL https://github.com/instrumenta/kubeval/releases/download/${KUBEVAL_VERSION}/kubeval-linux-amd64.tar.gz | \
tar xz

cp ./kubeval $GITHUB_WORKSPACE/bin
chmod +x $GITHUB_WORKSPACE/bin/kubeval

curl -sL -o sops https://github.com/mozilla/sops/releases/download/${SOPS_VERSION}/sops-${SOPS_VERSION}.linux

cp ./sops $GITHUB_WORKSPACE/bin
chmod +x $GITHUB_WORKSPACE/bin/sops

echo "$GITHUB_WORKSPACE/bin" >> $GITHUB_PATH
echo "$RUNNER_WORKSPACE/$(basename $GITHUB_REPOSITORY)/bin" >> $GITHUB_PATH
