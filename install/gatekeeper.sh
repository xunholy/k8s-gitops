#!/usr/bin/env bash

set -eou pipefail

# kubectl apply -f https://raw.githubusercontent.com/open-policy-agent/gatekeeper/master/deploy/gatekeeper.yaml \
# 	--dry-run=none -o yaml > namespaces/gatekeeper-system/gatekeeper.yaml
curl -o namespaces/gatekeeper-system/gatekeeper.yaml https://raw.githubusercontent.com/open-policy-agent/gatekeeper/master/deploy/gatekeeper.yaml
sed -i'.bak' "s/quay.io\/open-policy-agent/docker.io\/raspbernetes/g" namespaces/gatekeeper-system/gatekeeper.yaml
BACKUP=namespaces/gatekeeper-system/gatekeeper.yaml.bak
[[ -f $BACKUP ]] && rm -f $BACKUP
