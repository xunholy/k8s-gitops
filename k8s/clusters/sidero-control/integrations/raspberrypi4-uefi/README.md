# Building Sidero Init Container

Build Image

```bash
docker buildx build -f Dockerfile --platform linux/arm64 . -t xunholy/raspberrypi4-uefi:latest --push
```

Scale Down Sidero Metal

```bash
k scale deploy sidero-controller-manager --replicas=0
```

Patch Sidero Metal Deployment

```bash
kubectl patch --patch-file patch.yaml deploy sidero-controller-manager  -n sidero-system
```

Scale Up Sidero Metal

```bash
k scale deploy sidero-controller-manager --replicas=1
```
