By default, the source-controller watches for sources only in the flux-system namespace, this way cluster admins can prevent untrusted sources from being registered by users.

```bash
export GITHUB_TOKEN="<PAT>"
```

```bash
flux bootstrap github \
  --components=source-controller,kustomize-controller,helm-controller,notification-controller \
  --path=cluster \
  --version=latest \
  --owner=raspbernetes \
  --repository=k8s-gitops \
  --arch=arm64
```

```bash
flux create source git k8s-gitops \
  --url=https://github.com/raspbernetes/k8s-gitops \
  --branch=fluxv2-init \
  --interval=30s \
  --export > ./k8s-gitop.yaml
```

```bash
flux install \
  --components=source-controller,kustomize-controller,helm-controller,notification-controller \
  --namespace=flux-system \
  --arch=arm64
```
