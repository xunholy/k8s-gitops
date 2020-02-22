# Kubernetes Cluster Management

## Install Flux

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
