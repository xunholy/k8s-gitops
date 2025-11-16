<div align="center">

<img src="./docs/assets/banner.png" alt="Raspbernetes">

My _Personal_ Homelab Repository

_... managed with Flux, Renovate and GitHub Actions_

</div>

<div align="center">

[![Discord](https://img.shields.io/discord/673534664354430999?style=for-the-badge&label=discord&logo=discord&logoColor=white&color=blue)](https://discord.gg/home-operations)&nbsp;&nbsp;
[![Kubernetes](https://img.shields.io/endpoint?url=https%3A%2F%2Fkromgo.raspbernetes.com%2Fkubernetes_version&style=for-the-badge&logo=kubernetes&logoColor=white&color=blue)](https://kubernetes.io/)&nbsp;&nbsp;
[![Talos](https://img.shields.io/endpoint?url=https%3A%2F%2Fkromgo.raspbernetes.com%2Ftalos_version&style=for-the-badge&logo=talos&logoColor=white&color=blue)](https://talos.dev)&nbsp;&nbsp;
[![FluxCD](https://img.shields.io/endpoint?url=https%3A%2F%2Fkromgo.raspbernetes.com%2Fflux_version&style=for-the-badge&logo=flux&logoColor=white&color=blue)](https://fluxcd.io/)&nbsp;&nbsp;

</div>

<div align="center">

[![Age-Days](https://img.shields.io/endpoint?url=https%3A%2F%2Fkromgo.raspbernetes.com%2Fcluster_age_days&style=flat-square&label=Age)](https://github.com/kashalls/kromgo)&nbsp;&nbsp;
[![Uptime-Days](https://img.shields.io/endpoint?url=https%3A%2F%2Fkromgo.raspbernetes.com%2Fcluster_uptime_days&style=flat-square&label=Uptime)](https://github.com/kashalls/kromgo)&nbsp;&nbsp;
[![Node-Count](https://img.shields.io/endpoint?url=https%3A%2F%2Fkromgo.raspbernetes.com%2Fcluster_node_count&style=flat-square&label=Nodes)](https://github.com/kashalls/kromgo)&nbsp;&nbsp;
[![Pod-Count](https://img.shields.io/endpoint?url=https%3A%2F%2Fkromgo.raspbernetes.com%2Fcluster_pod_count&style=flat-square&label=Pods)](https://github.com/kashalls/kromgo)&nbsp;&nbsp;
[![CPU-Usage](https://img.shields.io/endpoint?url=https%3A%2F%2Fkromgo.raspbernetes.com%2Fcluster_cpu_usage&style=flat-square&label=CPU)](https://github.com/kashalls/kromgo)&nbsp;&nbsp;
[![Memory-Usage](https://img.shields.io/endpoint?url=https%3A%2F%2Fkromgo.raspbernetes.com%2Fcluster_memory_usage&style=flat-square&label=Memory)](https://github.com/kashalls/kromgo)&nbsp;&nbsp;

</div>

# 🍼 Overview

This educational project is designed to provide a hands-on learning experience for mastering Kubernetes cluster configurations and best practices. The repository showcases a declarative implementation of a Kubernetes cluster, following GitOps principles that can be utilized with a variety of tools and workflows.

The main goal of this project is to demonstrate best practices for implementing enterprise-grade security, observability, and comprehensive cluster configuration management using GitOps in a Kubernetes environment, while fostering learning and growth in the Kubernetes community.

This repository leverages a range of cutting-edge open-source tools and platforms, forming a comprehensive technology stack that demonstrates the power of the [CNCF ecosystem](https://landscape.cncf.io/).

## 📖 Table of contents
## 🌐 Cloud Services BOM & 3rd Party Integrations

This repository integrates a variety of third-party cloud and open-source services to provide a robust, production-grade Kubernetes platform. Key integrations include:

### Talos Integrations
- **Cilium**: Advanced networking and security (see `talos/integrations/cilium/`)
- **Kubelet CSR Approver**: Automated certificate signing for Talos nodes (see `talos/integrations/cert-approver/`)

### Terraform Cloud Modules
- **Cloudflare**: DNS, Access, and security controls (see `terraform/cloudflare/`)
- **Google Cloud Platform (GCP)**: Infrastructure provisioning and cloud resource management (see `terraform/gcp/`)

### Kubernetes Apps & Operators
- **Rook Ceph**: Distributed storage (see `kubernetes/apps/base/rook-ceph/`)
- **Istio**: Service mesh and ingress (see `kubernetes/apps/base/istio-ingress/`)
- **Cilium**: CNI and network observability (see `kubernetes/apps/base/kube-system/cilium/`)
- **Cert-Manager**: Automated certificate management (see `kubernetes/apps/base/network-system/cert-manager/`)
- **ExternalDNS**: DNS automation (see `kubernetes/apps/base/network-system/external-dns/`)
- **OAuth2 Proxy, Dex**: Authentication and SSO (see `kubernetes/apps/base/network-system/oauth2-proxy/`, `kubernetes/apps/base/network-system/dex/`)
- **Multus**: Multi-network support (see `kubernetes/apps/base/network-system/multus/`)
- **Velero**: Backup and disaster recovery (see `terraform/gcp/velero.tf`)
- **Thanos**: Scalable metrics storage (see `terraform/gcp/thanos.tf`)
- **Prometheus & Grafana**: Monitoring and dashboards
- **Sealed Secrets & SOPS**: Secure secret management
- **OpenEBS, Longhorn**: Additional storage options
- **Kyverno, Keda, Kubecost, Linkerd, Rook, Vault, Crossplane, ArgoCD**: Various cloud-native enhancements

For details on configuration and usage, see the respective directories and documentation files.

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

| Device                                                                                 | Description        | Quantity | CPU      | RAM   | Storage                     | Architecture | Operating System                      | Notes          |
| -------------------------------------------------------------------------------------- | ------------------ | -------- | -------- | ----- | --------------------------- | ------------ | ------------------------------------- | -------------- |
| [Ubiquiti UDM-Pro-Max](https://ui.com/us/en/cloud-gateways/dream-machine-pro-max)     | Router/Gateway     | 1        | -  | -   | 8TB                           | -        | UniFi OS                              |                |
| [Ubiquiti USW-Pro-Max-48-PoE](https://ui.com/switching/pro-max-48-poe)                | Network Switch     | 1        | -        | -     | -                           | -            | UniFi OS                              | 48-port PoE    |
| [Asus NUC 14 Pro](https://www.asus.com/displays-desktops/nucs/nuc-mini-pcs/asus-nuc-14-pro/) | Kubernetes Cluster | 3        | 14 Cores | 48GB   | 1TB NVMe + 1TB SSD          | AMD64        | [Talos Linux](https://www.talos.dev/) | Ultra 5-125H   |

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

|                                                                                                                             | Name                                             | Description                                                                                                                   |
| --------------------------------------------------------------------------------------------------------------------------- | ------------------------------------------------ |-------------------------------------------------------------------------------------------------------------------------------|
| <img width="32" src="https://github.com/cncf/artwork/raw/main/projects/kubernetes/icon/color/kubernetes-icon-color.svg">    | [Kubernetes](https://kubernetes.io/)             | An open-source system for automating deployment, scaling, and management of containerized applications                        |
| <img width="32" src="https://github.com/cncf/artwork/raw/main/projects/flux/icon/color/flux-icon-color.svg">                | [FluxCD](https://fluxcd.io/)                     | GitOps tool for deploying applications to Kubernetes                                                                          |
| <img width="32" src="https://www.talos.dev/images/logo.svg">                                                                | [Talos Linux](https://www.talos.dev/)            | Talos Linux is Linux designed for Kubernetes                                                                                  |
| <img width="62" src="https://github.com/cncf/artwork/raw/main/projects/cilium/icon/color/cilium_icon-color.svg">            | [Cilium](https://cilium.io/)                     | Cilium is an open source, cloud native solution for providing, securing, and observing network connectivity between workloads |
| <img width="62" src="https://github.com/cncf/artwork/raw/main/projects/istio/icon/color/istio-icon-color.svg">              | [Istio](https://istio.io/)                       | Istio extends Kubernetes to establish a programmable, application-aware network using the powerful Envoy service proxy.       |
| <img width="32" src="https://github.com/cncf/artwork/raw/main/projects/containerd/icon/color/containerd-icon-color.svg">    | [containerd](https://containerd.io/)             | Container runtime integrated with Talos Linux                                                                                 |
| <img width="32" src="https://github.com/cncf/artwork/raw/main/projects/coredns/icon/color/coredns-icon-color.svg">          | [CoreDNS](https://coredns.io/)                   | A DNS server that operates via chained plugins                                                                                |
| <img width="32" src="https://metallb.universe.tf/images/logo/metallb-blue.png">                                             | [MetalLB](https://metallb.universe.tf/)          | Load-balancer implementation for bare metal Kubernetes clusters, using standard routing protocols.                            |
| <img width="32" src="https://github.com/cncf/artwork/raw/main/projects/prometheus/icon/color/prometheus-icon-color.svg">    | [Prometheus](https://prometheus.io)              | Monitoring system and time series database                                                                                    |
| <img width="32" src="https://github.com/cncf/artwork/raw/main/projects/jaeger/icon/color/jaeger-icon-color.svg">            | [Jaeger](https://jaegertracing.io)               | Open-source, end-to-end distributed tracing for monitoring and troubleshooting transactions in complex distributed systems    |
| <img width="32" src="https://github.com/cncf/artwork/raw/main/projects/helm/icon/color/helm-icon-color.svg">                | [Helm](https://helm.sh)                          | The Kubernetes package manager                                                                                                |
| <img width="32" src="https://github.com/cncf/artwork/raw/main/projects/falco/icon/color/falco-icon-color.svg">              | [Falco](https://falco.org)                       | Container-native runtime security                                                                                             |
| <img width="32" src="https://github.com/cncf/artwork/raw/main/projects/flux/flagger/icon/color/flagger-icon-color.svg">     | [Flagger](https://flagger.app/)                  | Progressive delivery Kubernetes operator (Canary, A/B Testing and Blue/Green deployments)                                     |
| <img width="32" src="https://github.com/cncf/artwork/raw/main/projects/opa/icon/color/opa-icon-color.svg">                  | [Open Policy Agent](https://openpolicyagent.org) | An open-source, general-purpose policy engine                                                                                 |
| <img width="52" src="https://github.com/cncf/artwork/raw/main/projects/kyverno/icon/color/kyverno-icon-color.svg">          | [Kyverno](https://kyverno.io/)                   | Kubernetes Native Policy Management                                                                                           |
| <img width="32" src="https://github.com/cncf/artwork/raw/main/projects/dex/icon/color/dex-icon-color.svg">                  | [Dex](https://github.com/dexidp/dex)             | An identity service that uses OpenID Connect to drive authentication for other apps                                           |
| <img width="32" src="https://github.com/cncf/artwork/raw/main/projects/crossplane/icon/color/crossplane-icon-color.svg">    | [Crossplane](https://crossplane.io/)             | Manage any infrastructure your application needs directly from Kubernetes                                                     |
| <img width="32" src="https://github.com/cncf/artwork/raw/main/projects/litmus/icon/color/litmus-icon-color.svg">            | [Litmus](https://litmuschaos.io)                 | Chaos engineering for your Kubernetes                                                                                         |
| <img width="32" src="https://github.com/cncf/artwork/raw/main/projects/openebs/icon/color/openebs-icon-color.svg">          | [OpenEBS](https://openebs.io)                    | Container-attached storage                                                                                                    |
| <img width="32" src="https://github.com/cncf/artwork/raw/main/projects/opentelemetry/icon/color/opentelemetry-icon-color.svg"> | [OpenTelemetry](https://opentelemetry.io)        | Making robust, portable telemetry a built in feature of cloud-native software.                                                |
| <img width="32" src="https://github.com/cncf/artwork/raw/main/projects/thanos/icon/color/thanos-icon-color.svg">               | [Thanos](https://thanos.io)                      | Highly available Prometheus setup with long-term storage capabilities                                                         |
| <img width="32" src="https://github.com/cncf/artwork/raw/main/projects/cert-manager/icon/color/cert-manager-icon-color.svg">   | [Cert Manager](https://cert-manager.io/)         | X.509 certificate management for Kubernetes                                                                                   |
| <img width="32" src="https://grafana.com/static/img/menu/grafana2.svg">                                                     | [Grafana](https://grafana.com)                   | Analytics & monitoring solution for every database.                                                                           |
| <img width="32" src="https://github.com/grafana/loki/blob/main/docs/sources/logo.png?raw=true">                             | [Loki](https://grafana.com/oss/loki/)            | Horizontally-scalable, highly-available, multi-tenant log aggregation system                                                  |
| <img width="62" src="https://velero.io/img/Velero.svg">                                                                     | [Velero](https://velero.io/)                     | Backup and restore, perform disaster recovery, and migrate Kubernetes cluster resources and persistent volumes.               |

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
