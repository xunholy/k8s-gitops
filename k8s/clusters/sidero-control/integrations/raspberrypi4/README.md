# Sidero Metal

# UEFI

In order to netboot Raspberry Pis into uefi we need to build a custom image that will be used as a sidecar to the Sidero Metal image running in the cluster. This will move the `RPI_EFI.fd` file into the appropriate path for Sidero Metal. This file contains the MAC address of each device hence it must exist per device.

These commands assume you're in the the CWD of this file.

```bash
docker buildx build -f uefi/Dockerfile --platform linux/arm64 . -t xunholy/raspberrypi4-uefi:latest --push
```

Scale Down Sidero Metal

```bash
k scale deploy sidero-controller-manager -n sidero-system --replicas=0
```

Patch Sidero Metal Deployment

```bash
kubectl patch --patch-file uefi/patch.yaml deploy sidero-controller-manager -n sidero-system
```

Scale Up Sidero Metal

```bash
k scale deploy sidero-controller-manager -n sidero-system --replicas=1
```

## U-BOOT

WIP

### Limitations

Booting directly into u-boot means you lose certain metadata from the server that you would have through uefi. This includes CPU information and System information; Ideally, these are key building blocks when orchestrating cluster creation and using specific machine types and sizes and therefor UEFI is the preffered option. However, netboot into u-boot removes the need to manually configure each device as done for UEFI setup when collecting the `RPI-EFI.fd` so this option may be the fastest route to a working cluster with sidero and a viable option if only running a single cluster.
