# Sidero

```bash
export CONTROL_PLANE_SERVERCLASS=any
export WORKER_SERVERCLASS=any
export TALOS_VERSION=v0.13.0
export KUBERNETES_VERSION=v1.22.2
export CONTROL_PLANE_PORT=8443
export CONTROL_PLANE_ENDPOINT=192.168.1.205
```

```bash
clusterctl generate cluster cluster-0 --control-plane-machine-count=3 --worker-machine-count=3 -i sidero:v0.4.0 > kubernetes/clusters/sidero/clusters/cluster-0.yaml
```
