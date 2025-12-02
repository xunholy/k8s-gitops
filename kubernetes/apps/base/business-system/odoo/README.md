# Odoo

Production Odoo 18 deployment with baked-in addons.

## Image Pipeline

```
odoo-deployment-development repo          Cluster repo
┌─────────────────────────────┐    ┌──────────────────────┐
│ push to master              │    │ Renovate detects new │
│   ↓                         │    │ image tag            │
│ GitHub Actions builds       │───►│   ↓                  │
│   ↓                         │    │ Creates PR           │
│ ghcr.io/hayden-agencies/odoo│    │   ↓                  │
│ 18.0.YYYYMMDD-<sha>@digest  │    │ Merge → Flux deploys │
└─────────────────────────────┘    └──────────────────────┘
```

## Ports

| Port | Purpose |
|------|---------|
| 8069 | http_port (unused in worker mode) |
| 8071 | xmlrpc_port (main HTTP traffic) |
| 8072 | gevent/longpolling |
