# Odoo

Production Odoo 18 deployment with baked-in addons.

## Image Pipeline

```
odoo-deployment-development repo          Cluster
┌─────────────────────────────┐    ┌──────────────────────┐
│ push to master              │    │ HelmRelease uses     │
│   ↓                         │    │ 18.0-latest tag      │
│ GitHub Actions builds       │───►│   ↓                  │
│   ↓                         │    │ Flux reconciles      │
│ ghcr.io/hayden-agencies/odoo│    │   ↓                  │
│ - 18.0.YYYYMMDD-<sha>       │    │ Pod pulls new image  │
│ - 18.0-latest               │    └──────────────────────┘
└─────────────────────────────┘
         ↑
   Renovate PRs when odoo:18 base updates
```

## Ports

| Port | Purpose |
|------|---------|
| 8069 | http_port (unused in worker mode) |
| 8071 | xmlrpc_port (main HTTP traffic) |
| 8072 | gevent/longpolling |

## Force Image Update

```bash
kubectl rollout restart deployment/odoo -n business-system
```
