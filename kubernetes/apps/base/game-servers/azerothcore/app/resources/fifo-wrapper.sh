#!/usr/bin/env bash
# Wraps the upstream AzerothCore entrypoint to expose a named pipe at
# /tmp/worldserver-stdin for the worldserver component. Lets preStop hooks
# inject console commands like "server restart 900" for graceful,
# player-notified shutdowns. Non-worldserver components are unaffected.
set -euo pipefail

if [[ "${ACORE_COMPONENT:-}" == "worldserver" ]]; then
    FIFO=/tmp/worldserver-stdin
    rm -f "$FIFO"
    mkfifo "$FIFO"
    chmod 666 "$FIFO"
    # Keep a writer attached so the server never sees EOF when a one-shot
    # writer (preStop's echo) closes.
    ( sleep infinity > "$FIFO" ) &
    exec /azerothcore/entrypoint-upstream.sh "$@" < "$FIFO"
else
    exec /azerothcore/entrypoint-upstream.sh "$@"
fi
