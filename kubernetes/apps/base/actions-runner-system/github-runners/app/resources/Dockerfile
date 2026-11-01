# The upstream runner image plus the libc headers and startup objects it leaves out. It has
# gcc and binutils but not libc6-dev, so nothing can link a host binary: Rust build scripts,
# proc macros and test binaries, cgo. bedrock-broadcaster's runners have no sudo, so their
# jobs can't install it themselves. Built by .github/workflows/actions-runner.yaml and tagged
# with the runner version below.
FROM ghcr.io/home-operations/actions-runner:2.338.2@sha256:2dd7caa094a425c041539447cb0e6ae63cda10b5242773764683b695b8fa5613

USER root

RUN \
    apt-get -qq update \
    && \
    apt-get -qq install -y --no-install-recommends --no-install-suggests \
        build-essential \
        pkg-config \
        xz-utils \
        zip \
    && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

USER runner
