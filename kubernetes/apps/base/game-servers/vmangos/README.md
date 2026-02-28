# VMaNGOS - Vanilla WoW Server (1.12.1)

## Server Configuration

- `mangosd.conf` and `realmd.conf` are managed as ConfigMaps in the HelmRelease
- The realm address is set via `VMANGOS_REALMLIST_ADDRESS` env var on the database container
- Game settings (player limit, max level, etc.) are in `mangosd.conf`

## Managing Users

Accounts are managed via the mangosd server console. Attach to the running pod:

```bash
kubectl attach -it -n game-servers deploy/vmangos-mangosd -c app
```

### Creating an Account

In the mangos console:

```
account create <username> <password>
```

### Granting GM Privileges

```
account set gmlevel <username> 3
```

To detach from the console, press `Ctrl+P` then `Ctrl+Q`.

## Connecting

### Requirements

- World of Warcraft **1.12.1** client (patch 5875)
- An account on the server

### Client Setup

1. Edit `WoW/Data/enUS/realmlist.wtf` (or `enGB`):
   ```
   set realmlist wow.owncloud.ai
   ```
2. Launch WoW and log in
