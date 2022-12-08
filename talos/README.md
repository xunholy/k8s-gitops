


https://www.talos.dev/v1.1/introduction/getting-started/#decide-the-kubernetes-endpoint

Kubernetes API Endpoint

```bash
https://192.168.50.200:6443
```

Generate Cluster Config

```bash
talosctl gen config "talos-default" "https://192.168.50.200:6443"
```

```bash
talosctl --talosconfig=./talosconfig \
  config endpoint 192.168.50.131 192.168.50.132 192.168.50.133
```

```bash
talosctl --talosconfig=./talosconfig \
  config node 192.168.50.131
```

```bash
talosctl config merge ./talosconfig
```

Do the following for each controlplane node

```bash
talosctl apply-config --insecure --nodes 192.168.50.121 --file controlplane.yaml
```

Do the following after you've applied the first controlplane nodes configuration

```bash
talosctl bootstrap --nodes 192.168.50.121
```

Do the following for each node in the cluster that is not a controlplane node.

```bash
talosctl apply-config --insecure --nodes 192.168.50.121 --file node.yaml
```
