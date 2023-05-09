# Getting Started

These instructions will assume you have an Kubernetes cluster and want to bootstrap this GitOps repository to it.

## Install the CLI tool

*For all installation methods visit the Flux [install guide](https://toolkit.fluxcd.io/get-started/#install-the-flux-cli)*

```bash
brew install fluxcd/tap/flux
```

## Install Flux

*For the full installation guide visit the Flux [bootstrap guide](https://toolkit.fluxcd.io/get-started/#install-flux-components)*

Validate the cluster and its connectivity

```bash
kubectl cluster-info
```

Export your GitHub personal access token, username, repository and cluster

```bash
export GITHUB_TOKEN=<your-token>
export GITHUB_USER=<your-username>
export GITHUB_REPO=<your-repo>
export CLUSTER=<target-cluster>
```

Verify that your cluster satisfies the prerequisites

```bash
flux check --pre
```

Run the bootstrap command to install Flux

```bash
flux bootstrap github \
  --owner="${GITHUB_USER}" \
  --repository="${GITHUB_REPO}" \
  --path=kubernetes/clusters/"${CLUSTER}" \
  --branch=main \
  --personal
```

**Note:** If you have network issues with Flux starting you may need to set `--network-policies=false` in the bootstrap command.

You may also use the automated installation script - Either override the defaults in the [install script](../.././.github/actions/buildkit/install.sh) or as environment variables.
