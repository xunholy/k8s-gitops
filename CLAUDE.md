# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Repository Overview

This is a Kubernetes GitOps repository for a personal homelab cluster managed with FluxCD and Talos Linux. The cluster follows enterprise-grade security and observability practices, showcasing CNCF ecosystem tools.

## Architecture

- **Operating System**: Talos Linux (minimal, immutable Kubernetes OS)
- **GitOps**: FluxCD with Flux Operator for declarative cluster management
- **Container Runtime**: containerd
- **Networking**: Cilium CNI with Istio service mesh
- **Storage**: OpenEBS for container-attached storage
- **Monitoring**: Prometheus, Grafana, Loki, Jaeger for observability
- **Security**: Kyverno for policy management, Falco for runtime security
- **Load Balancing**: MetalLB for bare metal load balancing

## Directory Structure

```
├── kubernetes/              # Kubernetes manifests and configurations
│   ├── apps/                # Application deployments (base + overlays)
│   ├── bootstrap/           # Initial cluster bootstrap configuration
│   ├── clusters/            # Per-cluster configurations
│   ├── components/          # Shared components and alerts
│   └── tenants/             # Multi-tenant configurations
├── talos/                   # Talos Linux configuration files
│   ├── generated/           # Generated Talos configs (encrypted)
│   ├── integrations/        # Integration configurations
│   └── patches/             # Talos configuration patches
├── terraform/               # Infrastructure as Code
│   ├── cloudflare/          # Cloudflare DNS/CDN configuration
│   └── gcp/                 # Google Cloud Platform resources
├── .taskfiles/              # Task automation definitions
└── docs/                    # Documentation
```

## Common Commands

### Task Management (Primary Build System)
The repository uses [Task](https://taskfile.dev) for automation. All commands should be run via `task`:

```bash
# Core FluxCD operations
task flux:bootstrap          # Bootstrap FluxCD in the cluster
task flux:secrets           # Install cluster secrets and configs

# Talos operations
task talos:config           # Decrypt and load Talos config

# View available tasks
task --list
```

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
Secrets are encrypted using [SOPS](https://github.com/mozilla/sops):
```bash
# Decrypt secrets (requires proper age key setup)
sops -d path/to/encrypted.yaml

# Edit encrypted files
sops path/to/encrypted.yaml
```

## Key Technologies & Patterns

### GitOps with FluxCD
- **Flux Operator**: Manages FluxCD installation via FluxInstance CRDs
- **Kustomizations**: Define how to apply Kubernetes manifests
- **HelmReleases**: Manage Helm chart deployments
- **GitRepository/OCIRepository**: Source definitions for manifests

### Cluster Configuration
- **Bootstrap**: Initial cluster setup in `kubernetes/bootstrap/`
- **Apps**: Application deployments with base configurations and cluster-specific overlays
- **Components**: Shared components like monitoring alerts
- **Tenants**: Multi-tenant namespace configurations

### Security Practices
- All secrets encrypted with SOPS using age encryption
- Kyverno policies enforce security standards
- Falco provides runtime security monitoring
- Talos Linux provides immutable, minimal attack surface

## Development Workflow

1. **Making Changes**:
   - Edit YAML manifests in appropriate directories
   - Ensure proper directory structure (base + overlays pattern)
   - Follow existing naming conventions

2. **Testing**:
   - Use `task` commands to validate configurations
   - Run pre-commit hooks before committing
   - FluxCD will automatically reconcile changes after push

3. **Secrets Management**:
   - Never commit unencrypted secrets
   - Use SOPS for any sensitive data
   - Reference encrypted secrets in `.sops.yaml`

## File Patterns to Understand

- `kustomization.yaml`: Kustomize configuration files
- `*.enc.yaml`: SOPS-encrypted files
- `helmfile.yaml`: Helmfile configurations for chart management
- `app/`: Directory containing application-specific configurations
- `resources/`: Directory for Kubernetes resource definitions

## Important Notes

- The cluster uses cluster ID "cluster-0" as the default
- Talos config is stored encrypted in `talos/generated/`
- FluxCD manages all application deployments automatically
- Changes to `main` branch trigger automatic reconciliation
- The repository follows enterprise GitOps patterns suitable for production use

## External Dependencies

- **Cloudflare**: DNS and CDN services
- **Google Cloud Platform**: OAuth, backup storage
- **GitHub**: Source control and authentication
- **SOPS/age**: Secret encryption (requires age key setup)
- **Task**: Task runner (must be installed locally)