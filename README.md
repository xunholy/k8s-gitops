<div align="center">

# [owncloud.ai](https://owncloud.ai)

My own cloud — a homelab running Talos Linux, FluxCD, and the CNCF ecosystem.
GitOps, observability, and security — all from home.

</div>

<div align="center">

[![Discord](https://img.shields.io/discord/673534664354430999?style=for-the-badge&label=discord&logo=discord&logoColor=white&color=blue)](https://discord.gg/home-operations)&nbsp;&nbsp;
[![Kubernetes](https://img.shields.io/endpoint?url=https%3A%2F%2Fkromgo.owncloud.ai%2Fkubernetes_version&style=for-the-badge&logo=kubernetes&logoColor=white&color=blue)](https://kubernetes.io/)&nbsp;&nbsp;
[![Talos](https://img.shields.io/endpoint?url=https%3A%2F%2Fkromgo.owncloud.ai%2Ftalos_version&style=for-the-badge&logo=talos&logoColor=white&color=blue)](https://talos.dev)&nbsp;&nbsp;
[![FluxCD](https://img.shields.io/endpoint?url=https%3A%2F%2Fkromgo.owncloud.ai%2Fflux_version&style=for-the-badge&logo=flux&logoColor=white&color=blue)](https://fluxcd.io/)&nbsp;&nbsp;

</div>

<div align="center">

[![Age-Days](https://img.shields.io/endpoint?url=https%3A%2F%2Fkromgo.owncloud.ai%2Fcluster_age_days&style=flat-square&label=Age)](https://github.com/kashalls/kromgo)&nbsp;&nbsp;
[![Uptime-Days](https://img.shields.io/endpoint?url=https%3A%2F%2Fkromgo.owncloud.ai%2Fcluster_uptime_days&style=flat-square&label=Uptime)](https://github.com/kashalls/kromgo)&nbsp;&nbsp;
[![Node-Count](https://img.shields.io/endpoint?url=https%3A%2F%2Fkromgo.owncloud.ai%2Fcluster_node_count&style=flat-square&label=Nodes)](https://github.com/kashalls/kromgo)&nbsp;&nbsp;
[![Pod-Count](https://img.shields.io/endpoint?url=https%3A%2F%2Fkromgo.owncloud.ai%2Fcluster_pod_count&style=flat-square&label=Pods)](https://github.com/kashalls/kromgo)&nbsp;&nbsp;
[![CPU-Usage](https://img.shields.io/endpoint?url=https%3A%2F%2Fkromgo.owncloud.ai%2Fcluster_cpu_usage&style=flat-square&label=CPU)](https://github.com/kashalls/kromgo)&nbsp;&nbsp;
[![Memory-Usage](https://img.shields.io/endpoint?url=https%3A%2F%2Fkromgo.owncloud.ai%2Fcluster_memory_usage&style=flat-square&label=Memory)](https://github.com/kashalls/kromgo)&nbsp;&nbsp;

</div>

## Overview

A Kubernetes homelab managed declaratively with GitOps. Everything in this repo — from cluster configuration to application deployments — is version-controlled and automatically reconciled by FluxCD.

Built on open-source tools from the [CNCF ecosystem](https://landscape.cncf.io/).

## Hardware

| Device | Description | Quantity | CPU | RAM | Storage | Operating System |
| --- | --- | --- | --- | --- | --- | --- |
| [Asus NUC 14 Pro](https://www.asus.com/displays-desktops/nucs/nuc-mini-pcs/asus-nuc-14-pro/) | Kubernetes Nodes | 3 | 14 cores | 48GB | 1TB NVMe + 1TB SSD | [Talos Linux](https://www.talos.dev/) |
| [Ubiquiti UDM-Pro-Max](https://ui.com/us/en/cloud-gateways/dream-machine-pro-max) | Router/Gateway | 1 | - | - | 8TB | UniFi OS |
| [Ubiquiti USW-Pro-Max-48-PoE](https://ui.com/switching/pro-max-48-poe) | Network Switch | 1 | - | - | - | UniFi OS |
| NAS | Storage | 1 | 8 cores | 16GB | 48TB | [TrueNAS](https://www.truenas.com/) |
| [JetKVM](https://jetkvm.com/) | Remote KVM | 3 | - | - | - | - |

<details>
<summary>Decommissioned Hardware</summary>

| Device | Description | Quantity | CPU | RAM | Architecture | Operating System |
| --- | --- | --- | --- | --- | --- | --- |
| [Protectli FW6E](https://protectli.com/product/fw6e/) | Router | 1 | 4 Cores | 16GB | AMD64 | [VyOs](https://vyos.io/) |
| [Protectli VP2410](https://protectli.com/product/vp2410/) | Kubernetes Node(s) | 3 | 4 Cores | 8GB | AMD64 | [Talos Linux](https://www.talos.dev/) |
| [Protectli FW2B](https://protectli.com/product/fw2b/) | Kubernetes Node(s) | 3 | 2 Cores | 8GB | AMD64 | [Talos Linux](https://www.talos.dev/) |
| [Raspberry Pi 4 Model B](https://www.raspberrypi.org/products/raspberry-pi-4-model-b/) | Kubernetes Node(s) | 4 | 4 Cores | 8GB | ARM64 | [Talos Linux](https://www.talos.dev/) |
| [Rock Pi 4 Model C](https://rockpi.org/rockpi4#) | Kubernetes Node(s) | 6 | 4 Cores | 4GB | ARM64 | [Talos Linux](https://www.talos.dev/) |

</details>

## Technology Stack

|  | Name | Description |
| --- | --- | --- |
| <img width="32" src="https://github.com/cncf/artwork/raw/main/projects/kubernetes/icon/color/kubernetes-icon-color.svg"> | [Kubernetes](https://kubernetes.io/) | Container orchestration |
| <img width="32" src="https://www.talos.dev/favicon.svg"> | [Talos Linux](https://www.talos.dev/) | Minimal, immutable Kubernetes OS |
| <img width="32" src="https://github.com/cncf/artwork/raw/main/projects/flux/icon/color/flux-icon-color.svg"> | [FluxCD](https://fluxcd.io/) | GitOps continuous delivery |
| <img width="32" src="https://github.com/cncf/artwork/raw/main/projects/cilium/icon/color/cilium_icon-color.svg"> | [Cilium](https://cilium.io/) | eBPF-based networking and security |
| <img width="32" src="https://github.com/cncf/artwork/raw/main/projects/envoy/icon/color/envoy-icon-color.svg"> | [Envoy Gateway](https://gateway.envoyproxy.io/) | Kubernetes Gateway API |
| <img width="32" src="https://github.com/cncf/artwork/raw/main/projects/rook/icon/color/rook-icon-color.svg"> | [Rook-Ceph](https://rook.io/) | Distributed storage |
| <img width="32" src="https://github.com/cncf/artwork/raw/main/projects/prometheus/icon/color/prometheus-icon-color.svg"> | [Prometheus](https://prometheus.io) | Monitoring and metrics |
| <img width="32" src="https://grafana.com/static/img/menu/grafana2.svg"> | [Grafana](https://grafana.com) | Dashboards and observability |
| <img width="32" src="https://github.com/cncf/artwork/raw/main/projects/cert-manager/icon/color/cert-manager-icon-color.svg"> | [cert-manager](https://cert-manager.io/) | Certificate management |
| <img width="32" src="https://raw.githubusercontent.com/external-secrets/external-secrets/main/assets/eso-logo-large.png"> | [External Secrets](https://external-secrets.io/) | Secret synchronization |
| <img width="32" src="https://raw.githubusercontent.com/kubernetes-sigs/external-dns/master/docs/img/external-dns.png"> | [ExternalDNS](https://github.com/kubernetes-sigs/external-dns) | Automated DNS management |
| <img width="32" src="https://github.com/cncf/artwork/raw/main/projects/dex/icon/color/dex-icon-color.svg"> | [Dex](https://github.com/dexidp/dex) | OpenID Connect identity provider |
| <img width="32" src="https://raw.githubusercontent.com/backube/volsync/main/docs/media/volsync.svg"> | [VolSync](https://volsync.readthedocs.io/) | Persistent volume replication |
| <img width="32" src="https://avatars.githubusercontent.com/u/99631794"> | [Spegel](https://github.com/spegel-org/spegel) | Cluster-local OCI registry mirror |
| <img width="32" src="https://avatars.githubusercontent.com/u/314135"> | [Cloudflare Tunnel](https://developers.cloudflare.com/cloudflare-one/connections/connect-networks/) | Secure ingress without public IPs |

## Cloud Services

| Service | Use | Cost (AUD) |
| --- | --- | --- |
| [Cloudflare](https://www.cloudflare.com/) | DNS, CDN, tunnel | ~$69/yr |
| [GCP](https://cloud.google.com/) | Backups (GCS), OAuth | ~$20/yr |
| [GitHub](https://github.com/) | Source control, OAuth | Free |
| [Let's Encrypt](https://letsencrypt.org/) | TLS certificates | Free |
| | **Total** | **~$35/mo** |

## Automation

Dependencies and container images are kept up to date by [Renovate](https://renovatebot.com/).

## Acknowledgments

Thanks to the [Home Operations Discord](https://discord.com/invite/home-operations) community for the inspiration and shared knowledge. Check out [kubesearch.dev](https://kubesearch.dev/) to see what others are running.

## Contributing

Contributions welcome — see the [Contributor Guide](.github/CONTRIBUTING.md) and [Code of Conduct](.github/CODE_OF_CONDUCT.md).

## License

[Apache 2.0](./LICENSE)
