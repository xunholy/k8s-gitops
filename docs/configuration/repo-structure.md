# Flux Repository Structure

!!! note "Work in progress"
    This document is a work in progress.

## TL;DR Quick Start

If you're familiar with Kustomize and how it operates within the Flux ecosystem this will provide a quick overview:

```bash
.
└── kubernetes/
    ├── clusters/
    │   ├── production/                         # One folder per cluster.
    │   │   ├── flux-system/                    # Folder containing flux-system manifests.
    │   │   │   ├── ...                         # Flux component resource manifests.
    │   │   │   └── kustomization.yaml          # Generated kustomization per cluster bootstrap.
    │   │   └── kustomization.yaml              # Kustomization per cluster referring all manifests in core and namespace directory.
    │   └── staging/
    │       ├── flux-system/
    │       │   ├── ...
    │       │   └── kustomization.yaml
    │       └── kustomization.yaml
    ├── core/
    │   ├── base/
    │   │   └── .../                            # One folder per resource type and/or app with its core dependency with prune disabled.
    │   │       └── application/                # One folder per application with core manifests.
    │   │           └── kustomization.yaml      # Kustomization per core application.
    │   └── overlays/
    │       ├── production/
    │       │   ├── kustomization.yaml          # Kustomization per cluster referencing each core app required.
    │       │   └── patch.yaml                  # Optional patch for each environment.
    │       └── staging/
    │           ├── kustomization.yaml
    │           └── patch.yaml
    └── namespaces/
        ├── base/
        │   └── namespace/                      # One folder per namespace containing base resources.
        │       ├── namespace.yaml              # Namespace manifest.
        │       ├── kustomization.yaml          # Kustomization per namespace referring all manifests in this current directory.
        │       └── application/                # Folder per app containing manifests and patches for each application.
        │           └── kustomizaiton.yaml      # Kustomization per app referring all manifests in this directory.
        └── overlays/
            ├── production/
            │   ├── kustomization.yaml          # Kustomization per cluster referencing each namespace and app required.
            │   └── patch.yaml                  # Optional patch for each environment.
            └── staging/
                ├── kustomization.yaml
                └── patch.yaml
```

## Repository Structure Breakdown

This Git repository contains the following directories:

- **clusters** dir contains the Flux configuration per cluster.
- **core** dir contains cluster resources that are core prerequisites to the cluster.
- **namespaces** dir contains namespaces and application workloads per cluster.

```bash
.
├── clusters/
│   ├── production
│   └── staging
├── core/
│   ├── base
│   └── overlays/
│       ├── production
│       └── staging
└── namespaces/
    ├── base
    └── overlays/
        ├── production
        └── staging
```

The **clusters/** dir contains configuration for each cluster definition and the infrastructure as code for each relevant cluster where applicable.

The **core/** dir contains all resources that are prerequisites to namespaces and workloads, this includes resources: CRDs, certain applications like Istio and Gatekeeper that must exist prior to other workloads, and crossplane resources that provisions infrastructure.

The **namespaces/** configuration is structured into:

- **namespaces/base/** dir contains namespaces and application workload resources.
- **namespaces/overlays/production/** dir contains the production cluster values and references what base components to deploy.
- **namespaces/overlays/staging/** dir contains the stating cluster values and references what base components to deploy.

```bash
.
└── namespaces/
    ├── base/
    │   └── namespace/
    │       ├── namespace.yaml
    │       ├── kustomization.yaml
    │       └── application/
    │           ├── helmrelease.yaml
    │           └── kustomizaiton.yaml
    └── overlays/
        ├── production/
        │   ├── kustomization.yaml
        │   └── patch.yaml
        └── staging/
            └── ...
```

In **namespaces/base/** dir will be a hierarchy of all **namespace/** dirs which will contain application resources. Each cluster overlay includes each namespace and/or application which is explicitly referenced; The base application configuration is defined with the following values:

```bash
apiVersion: helm.toolkit.fluxcd.io/v2beta2
kind: HelmRelease
metadata:
  name: metallb
  namespace: network-system
spec:
  interval: 5m
  chart:
    spec:
      chart: metallb
      version: 2.0.4
      sourceRef:
        kind: HelmRepository
        name: bitnami-charts
        namespace: flux-system
      interval: 10m
  values:
    configInline:
      address-pools:
        - name: default
          protocol: layer2
          addresses:
            - 192.168.1.150-192.168.1.155
```

In **namespaces/overlays/production/** dir we have a Kustomize patch file(s) with the production cluster specific values:

```bash
apiVersion: helm.toolkit.fluxcd.io/v2beta2
kind: HelmRelease
metadata:
  name: metallb
  namespace: network-system
spec:
  values:
    configInline:
      address-pools:
        - name: default
          protocol: layer2
          addresses:
            - 192.168.1.150-192.168.1.155
```

Note that whilst using Kustomize we can overwrite default values; in this example the default MetalLB address pool will be patched in the production cluster to a unique pool.
