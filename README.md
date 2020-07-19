<h1 align="center">
  <p align="center">Raspbernetes</p>
  <a href="https://raspbernetes.github.io/docs/"><img src="https://raspbernetes.github.io/img/logo.svg" alt="Raspbernetes"></a>
</h1>

<p align="center">
  <a href="https://github.com/raspbernetes/k8s-gitops/actions" alt="Build"><img src="https://github.com/raspbernetes/k8s-gitops/workflows/build/badge.svg" /></a>
  <!-- <a href="#backers" alt="sponsors on Open Collective"><img src="https://opencollective.com/Raspbernetes/backers/badge.svg" /></a> -->
  <a href="https://discord.gg/mey6zUn"><img src="https://img.shields.io/badge/discord-chat-7289DA.svg" alt="Discord"></a>
  <a href="https://github.com/raspbernetes/k8s-gitops/graphs/contributors"><img src="https://img.shields.io/github/contributors/raspbernetes/k8s-gitops.svg" alt="Contributors"></a>
  <a href="https://github.com/raspbernetes/k8s-gitops/issues"><img src="https://img.shields.io/github/issues-raw/raspbernetes/k8s-gitops.svg" alt="Open Issues"></a>
    <a href="https://github.com/raspbernetes/k8s-gitops"><img src="https://img.shields.io/github/stars/raspbernetes/k8s-gitops?style=social.svg" alt="Stars"></a>
</p>

## Install FluxCD

To install FluxCD update the config [values](config/flux/values.yaml) to reflect your repository and execute the following installation [script](scripts/flux.sh).

This will install all required custom resource definitions (CRDs) and deploy the manifests to run flux, it will also use the unofficial Raspbernetes image as this supports multi-architectures.

## Contributors

This project exists thanks to all the people who contribute.

<a href="https://github.com/raspbernetes/k8s-gitops/graphs/contributors"><img src="https://opencollective.com/raspbernetes/contributors.svg?width=890&button=false" /></a>
