#!/usr/bin/env bash
# Run a flate command again when it failed fetching a source from a registry: flate-retry.sh <command...>
#
# flate retries network errors itself, but treats a registry 404 or a 429 that outlasts its few
# seconds of backoff as final. Shared registries do both: ghcr.io answered 429 to two PRs rendering
# at once, and mirror.gcr.io returned "not found" for an envoy-gateway blob it served fine on a
# rerun. Any other failure, such as a chart that won't template, fails on the first attempt.
#
# stdout is held back until an attempt succeeds, so `flate-retry.sh flate build ... > file` never
# writes a failed attempt's partial output into the file.
set -euo pipefail

attempts=${FLATE_RETRY_ATTEMPTS:-3}
delay=${FLATE_RETRY_DELAY:-30}
fetch_error='oras copy|failed to perform "Fetch|response status code (429|5[0-9][0-9])|TLS handshake timeout|i/o timeout|connection reset by peer'

out=$(mktemp)
err=$(mktemp)
trap 'rm -f "$out" "$err"' EXIT

for ((attempt = 1; ; attempt++)); do
  status=0
  "$@" >"$out" 2>"$err" || status=$?
  if ((status == 0)); then
    cat "$err" >&2
    cat "$out"
    exit 0
  fi
  cat "$out" "$err" >&2
  if ((attempt >= attempts)) || ! grep -Eq "$fetch_error" "$out" "$err"; then
    exit "$status"
  fi
  wait_s=$((delay * attempt))
  echo "::warning::flate failed fetching a source (attempt $attempt of $attempts), retrying in ${wait_s}s" >&2
  sleep "$wait_s"
done
