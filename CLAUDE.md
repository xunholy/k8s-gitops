# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Repository Overview

This is a Kubernetes GitOps repository for a personal homelab cluster managed with FluxCD and Talos Linux. The cluster follows enterprise-grade security and observability practices, showcasing CNCF ecosystem tools.

## Architecture

- **Operating System**: Talos Linux (minimal, immutable Kubernetes OS)
- **GitOps**: FluxCD with Flux Operator for declarative cluster management
- **Container Runtime**: containerd
- **Networking**: Cilium CNI with Istio service mesh
- **Storage**: Rook-Ceph, OpenEBS, democratic-csi for container-attached storage
- **Monitoring**: Prometheus, Grafana, Loki, Jaeger, Thanos for observability
- **Security**: Kyverno, OPA Gatekeeper for policy management, Falco & Tetragon for runtime security
- **Load Balancing**: MetalLB for bare metal load balancing
- **Chaos Engineering**: Litmus for chaos testing

## Directory Structure

```
├── kubernetes/                       # Kubernetes manifests and configurations
│   ├── apps/
│   │   ├── base/                     # Base application configurations (DRY principle)
│   │   │   └── [system-name]/        # e.g., observability, kube-system, home-system
│   │   │       ├── [app-name]/
│   │   │       │   ├── app/          # HelmRelease, OCIRepository, secrets, values
│   │   │       │   └── ks.yaml       # Flux Kustomization with dependencies
│   │   │       ├── namespace.yaml
│   │   │       └── kustomization.yaml
│   │   └── overlays/
│   │       └── cluster-00/           # Cluster-specific overrides
│   ├── bootstrap/
│   │   └── helmfile.yaml             # Bootstrap Flux Operator and dependencies
│   ├── clusters/
│   │   └── cluster-00/
│   │       ├── flux-system/          # Flux Operator and FluxInstance configs
│   │       ├── secrets/              # Cluster secrets (SOPS encrypted)
│   │       └── ks.yaml               # Root Kustomization
│   ├── components/
│   │   └── common/alerts/            # Shared monitoring alerts
│   └── tenants/                      # Multi-tenant configurations
├── talos/                            # Talos Linux configuration files
│   ├── generated/                    # Generated Talos configs (encrypted)
│   ├── integrations/                 # Cilium, cert-approver integrations
│   └── patches/                      # iSCSI, metrics patches
├── terraform/                        # Infrastructure as Code
│   ├── cloudflare/                   # Cloudflare DNS/CDN configuration
│   └── gcp/                          # GCP KMS, Thanos storage, Velero backups
├── .taskfiles/                       # Task automation definitions
└── docs/                             # Documentation
```

## Common Commands

### Task Management (Primary Build System)
The repository uses [Task](https://taskfile.dev) for automation. All commands should be run via `task`:

```bash
# FluxCD Operations
task flux:bootstrap          # Bootstrap Flux Operator via Helmfile
task flux:secrets           # Install cluster secrets (SOPS decrypt + apply)
task fluxcd:bootstrap       # Alternative bootstrap path
task fluxcd:diff            # Preview FluxCD operator changes

# Talos Operations
task talos:config           # Decrypt and load talosconfig to ~/.talos/config

# Core Operations
task core:gpg               # Import SOPS PGP keys
task core:lint              # Run yamllint

# View available tasks
task --list
```

**Important Variables:**
- `CLUSTER`: cluster-00 (default cluster ID)
- `GITHUB_USER`: xunholy
- `GITHUB_REPO`: k8s-gitops
- `GITHUB_BRANCH`: main

### Pre-commit Hooks
The repository uses pre-commit for code quality:
```bash
pre-commit run --all-files   # Run all pre-commit hooks
```

Active hooks include:
- YAML/JSON/TOML validation
- yamllint (with `.yamllint.yaml` config)
- shellcheck for shell scripts
- Trailing whitespace and EOF fixes

### Secret Management
Secrets are encrypted using [SOPS](https://github.com/mozilla/sops) with dual encryption (PGP + GCP KMS):
```bash
# Edit encrypted files (automatically decrypts/encrypts)
sops path/to/file.enc.yaml

# Decrypt for viewing only
sops -d path/to/file.enc.yaml
```

**SOPS Configuration:**
- **PGP Key**: `0635B8D34037A9453003FB7B93CAA682FF4C9014`
- **Age Key**: `age19gj66fq5v2veu940ftyj4pkw0w5tgxgddlyqnd00pnjzyndevurqx70g4t`
- **GCP KMS**: Used for stored PGP keys
- Encrypted files use `.enc.yaml` or `.enc.age.yaml` suffix

## Key Technologies & Patterns

### GitOps with FluxCD
This repository uses **Flux Operator** instead of traditional `flux bootstrap`:
- **FluxInstance CRDs**: Declaratively manage FluxCD components
- **OCIRepository**: Used for Helm charts instead of HelmRepository (e.g., `oci://ghcr.io/prometheus-community/charts`)
- **Kustomizations**: Define manifest application with SOPS decryption, post-build substitution, and dependency chains
- **HelmReleases**: Reference charts via `chartRef` pointing to OCIRepository
- **Root Kustomization**: Located at `kubernetes/clusters/cluster-00/ks.yaml`

### Application Deployment Pattern
Each application follows this structure:
1. **Base configuration** in `kubernetes/apps/base/[system-name]/[app-name]/`:
   - `app/helmrelease.yaml`: Helm release definition
   - `app/ocirepository.yaml`: Chart source
   - `app/secret.enc.yaml`: Encrypted secrets
   - `app/values.yaml`: Helm values
   - `ks.yaml`: Flux Kustomization with `dependsOn`, SOPS settings, substitutions

2. **Cluster overlays** in `kubernetes/apps/overlays/cluster-00/`: Cluster-specific customizations using Kustomize patches

3. **System categories**: Apps organized into logical systems:
   - `kube-system`: Core Kubernetes (Cilium, metrics-server, reflector)
   - `network-system`: Networking (cert-manager, external-dns, oauth2-proxy, dex)
   - `observability`: Monitoring (Prometheus, Grafana, Loki, Jaeger, Thanos)
   - `security-system`: Security (Kyverno, Falco, Gatekeeper, Crowdsec)
   - `istio-system` & `istio-ingress`: Service mesh
   - `home-system`: Home automation & media
   - `rook-ceph`: Storage

### HelmRelease Global Defaults
All HelmReleases are patched with these defaults via Kustomization:
```yaml
install:
  crds: CreateReplace
  createNamespace: true
  replace: true
  strategy: RetryOnFailure
  timeout: 10m
rollback:
  recreate: true
  force: true
  cleanupOnFail: true
upgrade:
  cleanupOnFail: true
  crds: CreateReplace
  remediation:
    remediateLastFailure: true
    retries: 3
    strategy: rollback
```

### Security Practices
- **Dual encryption**: SOPS with PGP (primary) + GCP KMS backup
- **Never commit unencrypted secrets**: All secrets use `.enc.yaml` suffix
- **Policy enforcement**: Kyverno & OPA Gatekeeper
- **Runtime security**: Falco & Tetragon
- **Pod security labels**: Applied to all namespaces
- **Immutable OS**: Talos Linux minimal attack surface

## Development Workflow

### Bootstrap New Cluster
```bash
# 1. Set environment variables (CLUSTER_ID defaults to cluster-00)
# 2. Bootstrap Flux Operator
task fluxcd:bootstrap  # Installs flux-operator, flux-instance, cert-manager, kustomize-mutating-webhook

# 3. Install cluster secrets
task flux:secrets      # Decrypts and applies sops-gpg, sops-age, cluster-secrets, github-auth, cluster-config

# 4. Configure Talos
task talos:config      # Decrypts talosconfig to ~/.talos/config
```

### Making Changes to Applications
1. **Edit base configuration** in `kubernetes/apps/base/[system-name]/[app-name]/`
2. **Use overlays** for cluster-specific customization in `kubernetes/apps/overlays/cluster-00/`
3. **Follow naming conventions**:
   - `ks.yaml`: Flux Kustomization resources
   - `kustomization.yaml`: Kustomize configuration
   - `*.enc.yaml`: SOPS encrypted files
   - `helmrelease.yaml`: Helm release definitions
   - `ocirepository.yaml`: OCI repository sources
4. **Ensure secrets are encrypted** before committing (use `sops` command)
5. **Run pre-commit hooks**: `pre-commit run --all-files`
6. **FluxCD auto-reconciles** from main branch after push

### Adding New Applications
1. Create directory structure: `kubernetes/apps/base/[system-name]/[app-name]/`
2. Add `app/` directory with:
   - `helmrelease.yaml` (with `chartRef` to OCIRepository)
   - `ocirepository.yaml` (chart source)
   - `values.yaml` (Helm values)
   - `secret.enc.yaml` (if needed, encrypted with SOPS)
   - `kustomization.yaml`
3. Create `ks.yaml` with:
   - `dependsOn` for dependency chain
   - `decryption` for SOPS secrets
   - `postBuild.substituteFrom` for ConfigMap/Secret references
4. Add to parent `kustomization.yaml`
5. Create overlay if cluster-specific customization needed

## Important Patterns & Conventions

### File Naming
- `ks.yaml`: Flux Kustomization resources (defines how to apply manifests)
- `kustomization.yaml`: Kustomize configuration (defines what resources to include)
- `*.enc.yaml`: SOPS-encrypted with PGP
- `*.enc.age.yaml`: SOPS-encrypted with Age
- `helmfile.yaml`: Helmfile configurations (used in bootstrap)
- `helmrelease.yaml`: Helm release definitions
- `ocirepository.yaml`: OCI repository sources for Helm charts
- `namespace.yaml`: Namespace definitions with pod security labels

### Kustomization Labels
- `substitution.flux/enabled=true`: Enables SOPS decryption and variable substitution
- Patches applied globally to all Kustomizations for HelmRelease defaults

### Namespace Conventions
Labels applied to namespaces:
- `pod-security.kubernetes.io/enforce: privileged` (or `restricted`/`baseline`)
- `goldilocks.fairwinds.com/enabled: "true"` (monitoring)
- `kustomize.toolkit.fluxcd.io/prune: disabled` (on flux-system)

### Dependency Management
Flux Kustomizations use `dependsOn` to establish deployment order:
```yaml
dependsOn:
  - name: cert-manager
    namespace: flux-system
```

## Important Notes

- **Cluster ID**: "cluster-00" is the default cluster identifier
- **Branch**: `main` is the primary branch (auto-reconciled by FluxCD)
- **Talos configs**: Stored encrypted in `talos/generated/`
- **Bootstrap method**: Uses Flux Operator (not traditional `flux bootstrap`)
- **Chart sources**: Uses OCIRepository instead of HelmRepository
- **Yamllint config**: Line length warning at 240 characters, 2-space indentation
- **Renovate automation**: Auto-merge enabled for digests, ignores encrypted files
- **Multi-cluster ready**: Designed with overlay pattern for multiple clusters
- **Enterprise patterns**: Production-grade GitOps implementation showcasing CNCF ecosystem

## External Dependencies

- **Cloudflare**: DNS management and CDN services
- **Google Cloud Platform**:
  - GCP KMS for SOPS encryption
  - Google Cloud Storage for Thanos long-term metrics storage
  - Google Cloud Storage for Velero backups
  - OAuth for authentication
- **GitHub**: Source control, authentication, and OCI registry for Helm charts
- **SOPS/age**: Secret encryption (requires PGP and/or age key setup)
- **Task**: Task runner (must be installed locally)
- **Helmfile**: Used for bootstrap process
- **Let's Encrypt**: Certificate generation for secure communication
- **NextDNS**: Malware protection and ad-blocking
- **UptimeRobot**: Service monitoring

## Troubleshooting with Flux MCP

This repository includes Cursor rules for troubleshooting Flux resources using the `flux-operator-mcp` tools. Key troubleshooting workflows:

### Analyzing HelmReleases
1. Check helm-controller status with `get_flux_instance`
2. Get HelmRelease resource and analyze spec, status, inventory, events
3. Check `valuesFrom` ConfigMaps and Secrets
4. Verify source (OCIRepository) status
5. Analyze managed resources from inventory
6. Check logs if resources are failing

### Analyzing Kustomizations
1. Check kustomize-controller status with `get_flux_instance`
2. Get Kustomization resource and analyze spec, status, inventory, events
3. Check `substituteFrom` ConfigMaps and Secrets
4. Verify source (GitRepository/OCIRepository) status
5. Analyze managed resources from inventory

### Comparing Resources Across Clusters
Use `get_kubernetes_contexts` and `set_kubernetes_context` to switch between clusters, then compare resource specs and status.
