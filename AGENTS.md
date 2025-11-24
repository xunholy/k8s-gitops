# Repository Guidelines

## Project Structure & Module Organization
`kubernetes/` hosts GitOps manifests. Use `clusters/<cluster-id>` for environment overlays (default `cluster-00`), `apps/` for per-namespace services, `components/` for reusable bundles, and `tenants/` for Flux tenant definitions. `terraform/` contains infrastructure code; run plans from the provider-specific subdirectories. `talos/` stores machine configuration with encrypted outputs in `talos/generated`. `docs/` feeds the MkDocs site under `.github/mkdocs`, while `hack/` carries operational scripts. Shared Task definitions live in `.taskfiles/` and power the workflows described below.

## Cluster Topology
Cluster-00 runs 3 Talos control-plane nodes and 3 Talos worker nodes. Control planes are NVMe boot only; keep storage workloads off them. Rook Ceph OSDs are limited to worker nodes and match NVMe by-id devices—replace the generic NVMe filter with the exact worker disk IDs when available.

## Build, Test, and Development Commands
Use `task core:lint` to run repository yamllint checks configured by `.yamllint.yaml`. `task flux:bootstrap` bootstraps Flux against this GitHub repository; pair it with `task flux:secrets` when rotating cluster secrets. Preview Flux operator changes with `task bootstrap:diff -- --cluster-id cluster-00` before applying them via `task bootstrap:bootstrap`. Docs authors can run `task docs:serve` from the repo root to preview the site, and `pre-commit run --all-files` mirrors CI validations locally.

## Coding Style & Naming Conventions
Favor two-space indentation and leading `---` document separators in YAML; yamllint and pre-commit enforce this automatically. Use lower-case, hyphenated filenames that mirror the Kubernetes resource (`apps/networking/cloudflared/kustomization.yaml`). Keep Kustomize layouts consistent: `base/` for shared defaults, `overlays/` for cluster-specific tweaks. Format Terraform changes with `terraform fmt`, and ensure shell scripts satisfy `shellcheck` (invoked through pre-commit) before committing.

## Testing Guidelines
Before opening a PR run `task core:lint` and `pre-commit run --all-files`. Validate Kubernetes manifests with `kubectl kustomize kubernetes/clusters/cluster-00` (or the target app path) to confirm resources render cleanly. For Flux updates capture `task bootstrap:diff` output and summarize the impact in the PR description. Infrastructure modifications require a local `terraform plan`; do not commit plan files. When adjusting Talos config, decrypt via `task talos:config`, edit with `sops`, and re-encrypt before staging.

## Commit & Pull Request Guidelines
Follow Conventional Commits (`feat(component):`, `fix:`, `chore:`) as seen in the Git history. Each commit should encapsulate a logical change set and exclude sensitive material. Pull requests must describe affected clusters, link any related issues or Renovate items, and note validation steps (lint results, diff summaries, plans). Request review from the CODEOWNERS specified in `.github/CODEOWNERS`; include screenshots only when visual dashboards change.

## Security & Configuration Tips
Secrets and Talos assets are encrypted with SOPS (Age keys stored in `age.agekey`). Always edit them with `sops kubernetes/.../*.enc.yaml` to preserve encryption headers, and never stage decrypted artifacts. Use `task core:gpg` to import required signing keys before working with Flux secrets. Rotate credentials by updating the encrypted manifests and rerunning `task flux:secrets`. Ensure your kubeconfig targets a safe workspace cluster; production changes should reconcile through Flux.
