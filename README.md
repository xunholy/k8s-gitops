<div align="center">

# My Own Cloud

_... managed with Flux, Renovate and GitHub Actions_

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

# 🍼 Overview

This educational project is designed to provide a hands-on learning experience for mastering Kubernetes cluster configurations and best practices. The repository showcases a declarative implementation of a Kubernetes cluster, following GitOps principles that can be utilized with a variety of tools and workflows.

The main goal of this project is to demonstrate best practices for implementing enterprise-grade security, observability, and comprehensive cluster configuration management using GitOps in a Kubernetes environment, while fostering learning and growth in the Kubernetes community.

This repository leverages a range of cutting-edge open-source tools and platforms, forming a comprehensive technology stack that demonstrates the power of the [CNCF ecosystem](https://landscape.cncf.io/).

## 📖 Table of contents

- [🍼 Overview](#-overview)
  - [📖 Table of contents](#-table-of-contents)
  - [🔧 Hardware](#-hardware)
  - [☁️ Cloud Services](#️-cloud-services)
  - [🖥️ Technology Stack](#️-technology-stack)
  - [🤖 Automation](#-automation)
  - [🤝 Acknowledgments](#-acknowledgments)
  - [👥 Contributing](#-contributing)
    - [🚫 Code of Conduct](#-code-of-conduct)
    - [💡 Reporting Issues and Requesting Features](#-reporting-issues-and-requesting-features)
  - [📄 License](#-license)

## 🔧 Hardware

| Device | Description | Quantity | CPU | RAM | Storage | Architecture | Operating System |
| --- | --- | --- | --- | --- | --- | --- | --- |
| [Ubiquiti UDM-Pro-Max](https://ui.com/us/en/cloud-gateways/dream-machine-pro-max) | Router/Gateway | 1 | - | - | 8TB | - | UniFi OS |
| [Ubiquiti USW-Pro-Max-48-PoE](https://ui.com/switching/pro-max-48-poe) | Network Switch | 1 | - | - | - | - | UniFi OS |
| [Asus NUC 14 Pro](https://www.asus.com/displays-desktops/nucs/nuc-mini-pcs/asus-nuc-14-pro/) | Kubernetes Nodes | 3 | 14 cores | 48GB | 1TB NVMe + 1TB SSD | AMD64 | [Talos Linux](https://www.talos.dev/) |
| NAS | Storage | 1 | 8 cores | 16GB | 48TB | AMD64 | [TrueNAS](https://www.truenas.com/) |
| [JetKVM](https://jetkvm.com/) | Remote KVM | 3 | - | - | - | - | - |

<details>
<summary>Decommissioned Hardware</summary>

| Device                                                                                 | Description        | Quantity | CPU      | RAM   | Storage | Architecture | Operating System                      |
| -------------------------------------------------------------------------------------- | ------------------ | -------- | -------- | ----- | ------- | ------------ | ------------------------------------- |
| [Protectli FW6E](https://protectli.com/product/fw6e/)                                  | Router             | 1        | 4 Cores  | 16GB  | -       | AMD64        | [VyOs](https://vyos.io/)              |
| [Protectli VP2410](https://protectli.com/product/vp2410/)                             | Kubernetes Node(s) | 3        | 4 Cores  | 8GB   | -       | AMD64        | [Talos Linux](https://www.talos.dev/) |
| [Protectli FW2B](https://protectli.com/product/fw2b/)                                  | Kubernetes Node(s) | 3        | 2 Cores  | 8GB   | -       | AMD64        | [Talos Linux](https://www.talos.dev/) |
| [Raspberry Pi 4 Model B](https://www.raspberrypi.org/products/raspberry-pi-4-model-b/) | Kubernetes Node(s) | 4        | 4 Cores  | 8GB   | -       | ARM64        | [Talos Linux](https://www.talos.dev/) |
| [Rock Pi 4 Model C](https://rockpi.org/rockpi4#)                                       | Kubernetes Node(s) | 6        | 4 Cores  | 4GB   | -       | ARM64        | [Talos Linux](https://www.talos.dev/) |

</details>

## ☁️ Cloud Services

Although I manage most of my infrastructure and workloads on my own, there are specific components of my setup that rely on cloud services.

| Service                                   | Description                                                                                                                     | Cost (AUD)     |
| ----------------------------------------- | ------------------------------------------------------------------------------------------------------------------------------- | -------------- |
| [Cloudflare](https://www.cloudflare.com/) | I use Cloudflare in my home network for DNS management and to secure my domain with Cloudflare's services.                      | ~$69/yr        |
| [GCP](https://cloud.google.com/)          | I use Google Cloud Platform (GCP) to manage backups using Google Cloud Storage (GCS) and employ GCP's OAuth for authentication. | ~20/yr         |
| [GitHub](https://github.com/)             | I use GitHub for code management and version control, enabling seamless collaboration in addition to OAuth for authentication   | Free
| [Lets Encrypt](https://letsencrypt.org/)  | I use Let's Encrypt to generate certificates for secure communication within my network.                                        | Free           |
|                                           |                                                                                                                                 | Total: ~$35/mo |

## 🖥️ Technology Stack

The below showcases the collection of open-source solutions currently implemented in the cluster. Each of these components has been meticulously documented, and their deployment is managed using FluxCD, which adheres to GitOps principles.

The Cloud Native Computing Foundation (CNCF) has played a crucial role in the development and popularization of many of these tools, driving the adoption of cloud-native technologies and enabling projects like this one to thrive.

|                                                                                                                                | Name                                                            | Description                                                                                                    |
| ------------------------------------------------------------------------------------------------------------------------------ | --------------------------------------------------------------- | -------------------------------------------------------------------------------------------------------------- |
| <img width="32" src="https://github.com/cncf/artwork/raw/main/projects/kubernetes/icon/color/kubernetes-icon-color.svg">       | [Kubernetes](https://kubernetes.io/)                            | An open-source system for automating deployment, scaling, and management of containerized applications          |
| <img width="32" src="https://www.talos.dev/favicon.svg">                                                                      | [Talos Linux](https://www.talos.dev/)                           | Minimal, immutable Linux OS designed for Kubernetes                                                            |
| <img width="32" src="https://github.com/cncf/artwork/raw/main/projects/flux/icon/color/flux-icon-color.svg">                  | [FluxCD](https://fluxcd.io/)                                    | GitOps continuous delivery for Kubernetes                                                                      |
| <img width="32" src="https://github.com/cncf/artwork/raw/main/projects/helm/icon/color/helm-icon-color.svg">                  | [Helm](https://helm.sh)                                         | The Kubernetes package manager                                                                                 |
| <img width="32" src="https://github.com/cncf/artwork/raw/main/projects/cilium/icon/color/cilium_icon-color.svg">              | [Cilium](https://cilium.io/)                                    | eBPF-based CNI providing networking, security, and observability                                               |
| <img width="32" src="https://github.com/cncf/artwork/raw/main/projects/envoy/icon/color/envoy-icon-color.svg">                | [Envoy Gateway](https://gateway.envoyproxy.io/)                 | Kubernetes Gateway API implementation built on Envoy proxy                                                     |
| <img width="32" src="https://github.com/cncf/artwork/raw/main/projects/containerd/icon/color/containerd-icon-color.svg">      | [containerd](https://containerd.io/)                            | Industry-standard container runtime integrated with Talos Linux                                                |
| <img width="32" src="https://github.com/cncf/artwork/raw/main/projects/coredns/icon/color/coredns-icon-color.svg">            | [CoreDNS](https://coredns.io/)                                  | Flexible, plugin-based DNS server for Kubernetes service discovery                                             |
| <img width="32" src="https://github.com/cncf/artwork/raw/main/projects/rook/icon/color/rook-icon-color.svg">                  | [Rook-Ceph](https://rook.io/)                                   | Cloud-native storage orchestration for Kubernetes using Ceph                                                   |
| <img width="32" src="https://raw.githubusercontent.com/backube/volsync/main/docs/media/volsync.svg">                          | [Volsync](https://volsync.readthedocs.io/)                      | Asynchronous data replication for Kubernetes persistent volumes                                                |
| <img width="32" src="https://avatars.githubusercontent.com/u/99631794">                                                       | [Spegel](https://github.com/spegel-org/spegel)                  | Stateless cluster-local OCI registry mirror                                                                    |
| <img width="32" src="https://github.com/cncf/artwork/raw/main/projects/prometheus/icon/color/prometheus-icon-color.svg">      | [Prometheus](https://prometheus.io)                              | Monitoring system and time series database                                                                     |
| <img width="32" src="https://grafana.com/static/img/menu/grafana2.svg">                                                       | [Grafana](https://grafana.com)                                  | Analytics and monitoring dashboards                                                                            |
| <img width="32" src="https://github.com/cncf/artwork/raw/main/projects/cert-manager/icon/color/cert-manager-icon-color.svg">  | [cert-manager](https://cert-manager.io/)                        | X.509 certificate management for Kubernetes                                                                    |
| <img width="32" src="https://raw.githubusercontent.com/external-secrets/external-secrets/main/assets/eso-logo-large.png">      | [External Secrets](https://external-secrets.io/)                 | Synchronize secrets from external APIs (1Password) into Kubernetes                                             |
| <img width="32" src="https://raw.githubusercontent.com/kubernetes-sigs/external-dns/master/docs/img/external-dns.png">         | [ExternalDNS](https://github.com/kubernetes-sigs/external-dns)  | Automatically manage DNS records from Kubernetes resources                                                     |
| <img width="32" src="https://github.com/cncf/artwork/raw/main/projects/dex/icon/color/dex-icon-color.svg">                    | [Dex](https://github.com/dexidp/dex)                            | OpenID Connect identity provider for authentication                                                            |
| <img width="32" src="https://raw.githubusercontent.com/oauth2-proxy/oauth2-proxy/master/docs/static/img/logos/OAuth2_Proxy_icon.svg"> | [oauth2-proxy](https://oauth2-proxy.github.io/oauth2-proxy/)    | Reverse proxy providing authentication with external OAuth2 providers                                          |
| <img width="32" src="https://avatars.githubusercontent.com/u/314135">                                                         | [Cloudflare Tunnel](https://developers.cloudflare.com/cloudflare-one/connections/connect-networks/) | Secure outbound-only tunnel for exposing services without public IPs             |

## 🤖 Automation

This repository is automatically managed by [Renovate](https://renovatebot.com/). Renovate will keep all of the container images within this repository up to date automatically. It can also be configured to keep Helm chart dependencies up to date as well.

## 🤝 Acknowledgments

A special thank you to everyone in the [Home Operation Discord](https://discord.com/invite/home-operations) community for their valuable contributions and time. Much of the inspiration for my cluster comes from fellow enthusiasts who have shared their own clusters under the k8s-at-home GitHub topic.

Also I extend heartfelt thanks to all CNCF contributors for their dedication and expertise, as their collective efforts have been vital in driving innovation and success within the cloud-native ecosystem.

For more ideas on deploying applications or discovering new possibilities, be sure to explore the [kubesearch.dev](https://kubesearch.dev/) search.

## 👥 Contributing

Our project welcomes contributions from any member of our community. To get started contributing, please see our [Contributor Guide](.github/CONTRIBUTING.md).

### 🚫 Code of Conduct

By participating in this project, you are expected to uphold the project's [**Code of Conduct**](.github/CODE_OF_CONDUCT.md). Please report any unacceptable behavior to the repository maintainer.

### 💡 Reporting Issues and Requesting Features

If you encounter any issues or would like to request new features, please create an issue on the repository's issue tracker. When reporting issues, include as much information as possible, such as error messages, logs, and steps to reproduce the issue.

Thank you for your interest in contributing to this project! Your contributions help make it better for everyone.

## 📄 License

This repository is [Apache 2.0 licensed](./LICENSE)
