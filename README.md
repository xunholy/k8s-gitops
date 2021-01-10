<h1 align="center">
  <p align="center">Raspbernetes - Kubernetes Cluster</p>
  <a href="https://raspbernetes.github.io/docs/"><img src="https://raspbernetes.github.io/img/logo.svg" alt="Raspbernetes"></a>
</h1>

<div align="center">
  <a href="https://app.fossa.com/projects/git%2Bgithub.com%2Fraspbernetes%2Fk8s-gitops?ref=badge_shield" alt="FOSSA Status"><img src="https://app.fossa.com/api/projects/git%2Bgithub.com%2Fraspbernetes%2Fk8s-gitops.svg?type=shield"/></a>
  <a href="https://github.com/raspbernetes/k8s-gitops/actions" alt="Build"><img src="https://github.com/raspbernetes/k8s-gitops/workflows/build/badge.svg" /></a>
  <a href="https://discord.gg/mey6zUn"><img src="https://img.shields.io/badge/discord-chat-7289DA.svg" alt="Discord"></a>
  <a href="https://kubernetes.io/" alt="k8s"><img src="https://img.shields.io/badge/k8s-v1.19.5-orange" /></a>
  <a href="https://github.com/raspbernetes/k8s-gitops/graphs/contributors"><img src="https://img.shields.io/github/contributors/raspbernetes/k8s-gitops.svg" alt="Contributors"></a>
  <a href="https://github.com/raspbernetes/k8s-gitops/issues"><img src="https://img.shields.io/github/issues-raw/raspbernetes/k8s-gitops.svg" alt="Open Issues"></a>
  <a href="https://github.com/raspbernetes/k8s-gitops"><img src="https://img.shields.io/github/stars/raspbernetes/k8s-gitops?style=social.svg" alt="Stars"></a>
  <a href="https://github.com/raspbernetes/k8s-gitops/commits/main" alt="last commit"><img src="https://img.shields.io/github/last-commit/raspbernetes/k8s-gitops?color=purple" /></a>
  <a href="https://github.com/pre-commit/pre-commit" alt="pre-commit"><img src="https://img.shields.io/badge/pre--commit-enabled-brightgreen?logo=pre-commit&logoColor=white" /></a>
</div>

# Overview

This *repo* is a declarative implementation of a Kubernetes cluster. It's setup to use the [GitOps Toolkit](https://toolkit.fluxcd.io/get-started/) also known as Fluxv2.

Our goal is to demonstrates how to implement enterprise-grade security, observability, and overall cluster config management using GitOps in a Kubernetes cluster.

---

## Instructions

If you’ve forked this repo you must first update url field in [repo.yaml](bootstrap/repo.yaml).

When bootstrapping your cluster for the first time you ***must*** remove the `secrets/k8s-secret-sealed-secret-private-key.yaml` file and replace it once the `sealed-secret` controller has issued a new private key.

Install the GitOps Toolkit into your cluster to begin syncing your *repo* using the following command:

```bash
bootstrap/install.sh
```

To get the newly generated sealed-secret private key use the following command:

```bash
kubectl get secret -n kube-system -l sealedsecrets.bitnami.com/sealed-secrets-key -o yaml > secrets/k8s-secret-sealed-secret-private-key.yaml
```

Once you have obtained your new private key from sealed-secrets you will need to re-encrypt all the secrets found in the `secrets/` dir with your corresponding public certificate. Use the official documentation found [HERE](https://github.com/bitnami-labs/sealed-secrets#overview)

Note: These secrets are also in the repository as `SealedSecret` kubernetes resources which will show the data structure you must follow; Instructions will be added for each secret respectively in the future on how it must be generated and what fields it requires. It's recommended to store secrets using `git-crypt` so if the cluster is unavailable or you lose your private key you can still view the secrets in plaintext locally. (`git-crypt` may also be substituted to `SOPS` in a later implementation)

## Contributors

This project exists thanks to all the people who contribute.

<a href="https://github.com/raspbernetes/k8s-gitops/graphs/contributors"><img src="https://opencollective.com/raspbernetes/contributors.svg?width=890&button=false" /></a>

## Backers

Thank you to all our backers! 🙏 [[Become a backer](https://opencollective.com/raspbernetes#backer)]

<a href="https://opencollective.com/raspbernetes#backers" target="_blank"><img src="https://opencollective.com/raspbernetes/backers.svg"></a>

## Sponsors

Support this project by becoming a sponsor. Your logo will show up here with a link to your website. [[Become a sponsor](https://opencollective.com/raspbernetes#sponsor)]

<a href="https://opencollective.com/raspbernetes/sponsor/0/website" target="_blank"><img src="https://opencollective.com/raspbernetes/sponsor/0/avatar.svg"></a> <a href="https://opencollective.com/raspbernetes/sponsor/1/website" target="_blank"><img src="https://opencollective.com/raspbernetes/sponsor/1/avatar.svg"></a>

## License

Raspbernetes is [Apache 2.0 licensed](./LICENSE).

[![FOSSA Status](https://app.fossa.com/api/projects/git%2Bgithub.com%2Fraspbernetes%2Fk8s-gitops.svg?type=large)](https://app.fossa.com/projects/git%2Bgithub.com%2Fraspbernetes%2Fk8s-gitops?ref=badge_large)
