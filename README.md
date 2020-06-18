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

## Anthos Config Management

| Directory | Description |
|---------- | ----------- |
| cluster/ | The cluster/ directory contains configs that apply to entire clusters, rather than to namespaces. By default, any config in the cluster/ directory applies to every cluster enrolled in Config Sync. You can limit which clusters a config can affect by using a ClusterSelector. |
| clusterregistry/ | The clusterregistry/ directory is optional, and contains configs for ClusterSelectors. ClusterSelectors limit which clusters a config applies to, and are referenced in configs found in the cluster/ and namespaces/ directories. |
| namespaces/ | The namespaces/ directory contains configs for namespaces and namespace-scoped objects. The structure within namespaces/ is the mechanism that drives namespace inheritance. See that topic for details. |
| system/ | The system/ directory contains configs for the Operator. See Installing Config Sync for more information on configuring Config Sync. |



## Contributors

This project exists thanks to all the people who contribute.

<a href="https://github.com/raspbernetes/k8s-gitops/graphs/contributors"><img src="https://opencollective.com/raspbernetes/contributors.svg?width=890&button=false" /></a>
