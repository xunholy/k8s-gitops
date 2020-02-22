# Kubernetes Cluster Management

## Install Flux

Note: Using unofficial Flux image, as armv7l wasn't supported - `https://github.com/fluxcd/flux/issues/1761`

To install flux into the cluster run the following command:

```bash
export GHUSER="raspbernetes"
fluxctl install \
--git-user=${GHUSER} \
--git-email=${GHUSER}@users.noreply.github.com \
--git-url=git@github.com:${GHUSER}/k8s-cluster.git \
--git-path=namespaces \
--namespace=flux | kubectl apply -f -
```
