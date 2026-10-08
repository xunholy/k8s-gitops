# Getting Started with Talos

## Step 1: Decide the Kubernetes API Endpoint

Set the Kubernetes API endpoint, for example:

```bash
https://192.168.50.200:6443
```

## Step 2: Generate Cluster Configuration

Generate the cluster configuration using talosctl:

```bash
talosctl gen config "talos-default" "https://192.168.50.200:6443"
```

Configure the endpoints and node:

```bash
talosctl --talosconfig=./talosconfig \
  config endpoint 192.168.50.131 192.168.50.132 192.168.50.133

talosctl --talosconfig=./talosconfig \
  config node 192.168.50.131
```

Merge the configuration:

```bash
talosctl config merge ./talosconfig
```

## Step 3: Configure Control Plane Nodes

Apply the configuration to each control plane node:

```bash
talosctl apply-config --insecure --nodes 192.168.50.121 --file controlplane.yaml
```

After applying the configuration to the first control plane node, bootstrap the cluster:

```bash
talosctl bootstrap --nodes 192.168.50.121
```

## Step 4: Configure Other Nodes

Apply the configuration to each node in the cluster that is not a control plane node:

```bash
talosctl apply-config --insecure --nodes 192.168.50.121 --file node.yaml
```

If the `node.yaml` is encrypted first run the following command:

```bash
sops -d talos/generated/node.enc.yaml > talos/generated/node.yaml
```

## Step 5: Update Talos Client and Nodes

Download the latest Talos binary by substituting the version in the download URL:

```bash
curl -L https://github.com/siderolabs/talos/releases/download/v1.14.2/talosctl-linux-amd64 -o talosctl
sudo mv talosctl /usr/local/bin/talosctl
sudo chmod +x /usr/local/bin/talosctl
```

Verify the local client is updated:

```bash
talosctl version
```

Upgrade one node at a time, non-leader etcd members first, always passing the
installer image explicitly (the image in the live machine config is not bumped
by `talosctl upgrade`). The schematic adds the `iscsi-tools` extension:

```bash
talosctl -n <node-ip> upgrade \
  --image factory.talos.dev/installer/b629e405f5a20ec93f34868f86e6ab24ded1930f5853d0e130fda300b9283a14:v1.14.2
```

Then upgrade Kubernetes (dry-run first; it re-applies the CNI and
`extraManifests`, so they must match what Flux runs):

```bash
talosctl -n <node-ip> upgrade-k8s --to 1.36.5 --dry-run
```

## Step 6: Adding Protectli AMD64 Devices

To boot Talos onto a Protectli device, follow these steps:

Flash Ubuntu onto a flash drive.
Install Ubuntu onto the device and complete the setup.
Run the following commands:

```bash
wget https://factory.talos.dev/image/b629e405f5a20ec93f34868f86e6ab24ded1930f5853d0e130fda300b9283a14/v1.14.2/metal-amd64.iso
dd if=metal-amd64.iso of=/dev/sda && sync
```

**Note:** *You might need to run the commands with sudo. Also, validate the block device using `lsblk` and `df -a` to ensure you're writing to the appropriate drive.*

Restart the device with sudo restart.
Enjoy running Talos!
