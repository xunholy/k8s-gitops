# CMaNGOS Classic - Emberstone (1.12.1)

A Vanilla WoW private server running CMaNGOS Classic on Kubernetes, with AI playerbots, hardcore mode, auction house bot, and a level 19 twink vendor system.

## Architecture

The deployment consists of three controllers:

| Controller | Image | Ports | Description |
|---|---|---|---|
| `database` | `mariadb:12.2` | 3306 | MariaDB storing all game databases |
| `realmd` | `ghcr.io/xunholy/cmangos-classic` | 3724 | Authentication/realm server |
| `mangosd` | `ghcr.io/xunholy/cmangos-classic` | 8085, 7878 (SOAP) | World server |

The container image is built from a [custom fork](https://github.com/flekz-games/mangos-classic) (`modules` branch) with the following compiled modules:

- **PlayerBots** - AI bots that auto-create, level, do quests, and join battlegrounds
- **AHBot** - Automated auction house population
- **Hardcore** - Permadeath challenge system via [cmangos-hardcore](https://github.com/flekz-games/cmangos-hardcore)
- **TwinkMaster** - Level 19 twink vendor/XP lock via [cmangos-twinkmaster](https://github.com/xunholy/cmangos-twinkmaster)

## Server Configuration

Configuration files are managed as ConfigMaps mounted into the containers:

| ConfigMap | Mount Path | Purpose |
|---|---|---|
| `mangosd.conf` | `/opt/mangos/conf/mangosd.conf` | World server settings (XP rates, player limits, etc.) |
| `realmd.conf` | `/opt/mangos/conf/realmd.conf` | Auth server settings |
| `aiplayerbot.conf` | `/opt/mangos/conf/aiplayerbot.conf` | Bot count, behavior, leveling |
| `ahbot.conf` | `/opt/mangos/conf/ahbot.conf` | Auction house bot settings |
| `hardcore.conf` | `/opt/mangos/etc/hardcore.conf` | Hardcore challenge rules |
| `twinkmaster.conf` | `/opt/mangos/conf/twinkmaster.conf` | Twink vendor settings |

### Key Server Settings

- **XP Rate**: 3x (kill, quest, explore)
- **Drop Rate**: 3x normal/uncommon, 2x rare, 1x epic
- **Player Limit**: 100
- **Cross-Faction**: Groups, guilds, chat, and channels all enabled
- **Starting Gold**: 1g
- **PlayerBots**: 1500-2000 bots, levels 1-60, auto-questing enabled
- **Hardcore**: Permadeath enabled, graves spawn at death location, revive disabled

## Managing Users

Accounts are managed via the mangosd server console. Attach to the running pod:

```bash
kubectl attach -it -n game-servers deploy/cmangos-mangosd -c app
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

## Account Registration

A web-based registration page is available at `https://emberstone.owncloud.ai`. Players can create accounts without needing console access. The registration page is deployed separately in `cmangos-registration/`.

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

## Networking

External access is provided via Gateway API TCPRoutes through an Envoy gateway:

- **Auth** (port 3724): `cmangos-auth` TCPRoute
- **World** (port 8085): `cmangos-world` TCPRoute
- **DNS**: `wow.owncloud.ai` pointed to the external IP via DNSEndpoint

## Storage

- **Database PVC** (`cmangos-database`): MariaDB data at `/var/lib/mysql`
- **Data PVC** (`cmangos-data`): Extracted game resources (maps, vmaps, mmaps, dbc) at `/var/lib/mangos` (read-only mount on mangosd)
- **VolSync**: Replication configured for backup

## Custom Modules

### Hardcore Mode

Players can talk to **"Masochist" Pete** (NPC spawned in all major cities) to opt into challenges:

- **Hardcore**: One life, permadeath
- **Drop Loot**: Drop gear/gold on death
- **Lose XP**: Lose experience on death
- **Self Found**: No trading with non-challenge players
- **PvP Toggle**: Enable/disable PvP during challenges

Database migrations are applied via the `cmangos-hardcore-migration` Job.

### Twink Master

**Twink Master** NPCs provide free level 19 BiS gear, consumables, honor items, and endgame no-level-req items:

- **Alliance NPC** (190012): Stormwind Trade District
- **Horde NPC** (190013): Orgrimmar

Vendor categories: BiS Gear, Consumables, Honor Gear, Insane (raid items with no level requirement).

Database migrations are applied via the `cmangos-twink-vendor-migration` Job.

## Building the Image

The Dockerfile uses a multi-stage build:

1. **Builder stage**: Clones CMaNGOS, classic-db, and modules, then compiles with CMake
2. **Runner stage**: Minimal Ubuntu 24.04 with only runtime dependencies

```bash
docker build \
  --build-arg THREADS=$(nproc) \
  --build-arg MANGOS_SHA1=<commit> \
  --build-arg DATABASE_SHA1=<commit> \
  -t ghcr.io/xunholy/cmangos-classic:latest \
  kubernetes/apps/base/game-servers/cmangos/app/resources/
```

### Builder Entrypoint Commands

The builder image provides database management tools:

| Command | Description |
|---|---|
| `extract` | Extract game resources (maps, vmaps, mmaps) from WoW client |
| `init-db` | Initialize all databases from scratch (destructive) |
| `backup-db` | Backup databases (`-a` all, `-w` world, `-c` characters, `-l` logs, `-r` realmd) |
| `restore-db` | Restore databases from a backup tar.gz |
| `update-db` | Apply latest DB updates (`-w` to include world DB refresh) |
| `manage-db` | Run the InstallFullDB interactive script |
