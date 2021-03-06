FROM ubuntu

ARG TARGETARCH
ARG KUBESEAL_VERSION=0.14.1
ARG YQ_VERSION=3.4.1
ARG FLUX_VERSION=0.6.3

WORKDIR /

ENV DEBIAN_FRONTEND=noninteractive

RUN apt-get update && \
  apt-get install --no-install-recommends -y \
  apt-transport-https \
  build-essential \
  ca-certificates \
  openssh-client \
  curl \
  gnupg \
  gnupg2 \
  git \
  jq \
  shellcheck \
  dnsutils \
  git-crypt

# Install kubectl
RUN curl -s https://packages.cloud.google.com/apt/doc/apt-key.gpg | apt-key add - && \
  echo "deb https://apt.kubernetes.io/ kubernetes-xenial main" | tee -a /etc/apt/sources.list.d/kubernetes.list && \
  apt-get update && \
  apt-get install -y kubectl

# Install helm v3
RUN curl https://baltocdn.com/helm/signing.asc | apt-key add - && \
  echo "deb https://baltocdn.com/helm/stable/debian/ all main" | tee /etc/apt/sources.list.d/helm-stable-debian.list && \
  apt-get update && \
  apt-get install -y helm

# Install yq
RUN curl -sL https://github.com/mikefarah/yq/releases/download/3.3.0/yq_linux_${TARGETARCH} -o /usr/bin/yq && \
  chmod +x /usr/bin/yq

# Install kubeseal
RUN curl -sL -o /usr/bin/kubeseal https://github.com/bitnami-labs/sealed-secrets/releases/download/v${KUBESEAL_VERSION}/kubeseal-linux-${TARGETARCH} && \
  chmod +x /usr/bin/kubeseal

# Install flux
RUN curl -sL -o flux.tar.gz https://github.com/fluxcd/flux2/releases/download/v${FLUX_VERSION}/flux_${FLUX_VERSION}_linux_${TARGETARCH}.tar.gz && \
  tar -C /usr/bin -xvzf flux.tar.gz && \
  chmod +x /usr/bin/flux

# Install kustomize
# Note: If arm64 is not support consider compiling from source code.
# https://kubernetes-sigs.github.io/kustomize/installation/source/#install-the-kustomize-cli-from-local-source-with-cloning-the-repo
RUN curl -s "https://raw.githubusercontent.com/kubernetes-sigs/kustomize/master/hack/install_kustomize.sh"  | bash

CMD bash
