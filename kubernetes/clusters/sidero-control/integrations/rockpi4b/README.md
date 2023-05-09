Patch the sidero controller manager to use the custom image with the dtb files for rockchips

You will need to scale down and scale back up as it's using hostNetwork and will be already bound to the port

```bash
kubectl --namespace sidero-system patch deployments.apps sidero-controller-manager --type=json --patch='[{"op": "replace", "path": "/spec/template/spec/containers/0/image", "value":  "xunholy/sidero-controller-manager:v0.4.1"}]'
```
