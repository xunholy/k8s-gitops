# Kubernetes Cluster Management

## Install FluxCD

To install FluxCD use the following installation [script](install/flux.sh).

This will install all required custom resource definitions (CRDs) and deploy the manifests to run flux, it will also use the unofficial raspbernetes image as this supports multi-archiectures.

> Note: If you've forked this repository you will need to update the following to match your own personal account `export GHUSER="raspbernetes"`.

## Cluster Components

- security
- logging
- monitoring
- alerting
- tracing
- backups
- recovery
- certificate
- secrets
- networking