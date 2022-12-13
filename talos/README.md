


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

# Updating Local Binary & Talos Nodes

Substitute the version in the download URL with the version that is latest

```bash
curl -L https://github.com/siderolabs/talos/releases/download/v1.2.7/talosctl-linux-amd64 -o talosctl
sudo mv talosctl /usr/local/bin/talosctl
sudo chmod +x /usr/local/bin/talosctl
```

After this you should be able to validate the client is updated locally

```bash
talosctl version
...
Client:
        Tag:         v1.2.7
        SHA:         facc3d12
        Built:
        Go version:  go1.19.2
        OS/Arch:     linux/amd64
```

Follow the guide here to upgrade nodes to the appropriate Talos version https://www.talos.dev/v1.2/talos-guides/upgrading-talos/
