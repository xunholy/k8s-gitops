FROM ubuntu

RUN apt-get update && \
  apt-get install --no-install-recommends -y \
  apt-transport-https \
  ca-certificates \
  curl \
  gnupg

RUN curl https://baltocdn.com/helm/signing.asc | apt-key add -

RUN echo "deb https://baltocdn.com/helm/stable/debian/ all main" | tee /etc/apt/sources.list.d/helm-stable-debian.list && \
  apt-get update && \
  apt-get install -y helm

RUN curl -sL https://github.com/mikefarah/yq/releases/download/3.3.0/yq_linux_amd64 -o /usr/bin/yq && \
  chmod +x /usr/bin/yq
