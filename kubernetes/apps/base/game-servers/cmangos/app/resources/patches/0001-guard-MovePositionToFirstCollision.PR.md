# PR description

**Title:** `fix(Object): guard MovePositionToFirstCollision against invalid src pos`

**Suggested upstream:** [`cmangos/mangos-classic`](https://github.com/cmangos/mangos-classic) (canonical; `flekz-games/mangos-classic` auto-merges from it).

---

`WorldObject::MovePositionToFirstCollision` calls `GetMap()->GetHitPosition(pos.x, pos.y, ...)` without validating that `pos` is on the map. A stale `Position` whose coords fall outside `MAP_HALFSIZE` underflows the grid-cell index inside the vmap tile lookup and segfaults the world server on a NULL deref.

Reproduced in production with a bot resuming a long-unloaded BG instance. gdb on the captured core shows `SIGSEGV` at `MovePositionToFirstCollision+1424` with `RAX=0`, immediately after a log line of the form `Map::MessageBroadcast: Player (GUID: X) have invalid coordinates X:-18504.80 Y:-10635.10 grid cell [4294967253:192]` (`4294967253 = UINT32_MAX - 42`, classic unsigned underflow).

Fix: the same `MaNGOS::IsValidMapCoord(pos.x, pos.y, pos.z)` guard the codebase already uses elsewhere (cf. `WorldObject::IsPositionValid` further down the same file). Early-return with a log line if the source position is invalid — one hunk, 7 added lines.
