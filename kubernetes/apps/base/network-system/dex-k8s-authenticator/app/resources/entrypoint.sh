#!/bin/sh

if [ -n "$(ls -A /certs)" ]; then
  cp -L /certs/*.crt /usr/local/share/ca-certificates/ 2>/dev/null
  update-ca-certificates
fi

exec /app/bin/dex-k8s-authenticator "$@"
