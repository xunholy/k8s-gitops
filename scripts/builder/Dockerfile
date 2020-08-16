FROM ubuntu

ARG TARGETPLATFORM
ARG KUBESEAL_VERSION=0.12.5
ARG YQ_VERSION=3.3.2
ARG FLUXCTL_VERSION=1.20.1

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
RUN export ARCH=$(echo ${TARGETPLATFORM} | cut -d / -f2) && \
  curl -sL https://github.com/mikefarah/yq/releases/download/3.3.0/yq_linux_${ARCH} -o /usr/bin/yq && \
  chmod +x /usr/bin/yq

# Install kubeseal
RUN export ARCH=$(echo ${TARGETPLATFORM} | cut -d / -f2) && \
  curl -sL -o /usr/local/bin/kubeseal https://github.com/bitnami-labs/sealed-secrets/releases/download/v${KUBESEAL_VERSION}/kubeseal-linux-${ARCH} && \
  chmod +x /usr/local/bin/kubeseal

# Install fluxctl
RUN export ARCH=$(echo ${TARGETPLATFORM} | cut -d / -f2) && \
  curl -sL -o /usr/local/bin/fluxctl https://github.com/fluxcd/flux/releases/download/${FLUXCTL_VERSION}/fluxctl_linux_${ARCH} && \
  sudo chmod +x /usr/local/bin/fluxctl

CMD bash
