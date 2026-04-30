#!/usr/bin/env bash
# Wraps the upstream AzerothCore entrypoint to expose a named pipe at
# /tmp/worldserver-stdin for the worldserver component. Lets preStop hooks
# inject console commands like "server restart 900" for graceful,
# player-notified shutdowns. Non-worldserver components are unaffected.
set -euo pipefail

# AC's upstream entrypoint runs `cp -rnv ref/etc/* dist/etc/` and then copies
# the COMPONENT's .conf.dist to .conf — but only for the main component,
# never for modules. Module configs end up as dist/etc/modules/*.conf.dist
# with no .conf companion, so the loader falls back to defaults and emits
# ~100 "Missing property" warnings per startup.
#
# Fix: pre-populate dist/etc/modules/*.conf from ref/etc/modules/*.conf.dist
# BEFORE the upstream entrypoint runs. Upstream's `cp -rnv` is no-clobber,
# so our pre-created .conf files survive.
normalize_module_confs() {
    local ref_modules=/azerothcore/env/ref/etc/modules
    local dist_modules=/azerothcore/env/dist/etc/modules
    [ -d "$ref_modules" ] || return 0
    mkdir -p "$dist_modules"
    local src target
    for src in "$ref_modules"/*.conf.dist; do
        [ -f "$src" ] || continue
        target="$dist_modules/$(basename "${src%.dist}")"
        [ -f "$target" ] || cp "$src" "$target"
    done
}

normalize_module_confs

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
