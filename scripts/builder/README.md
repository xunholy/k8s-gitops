# Builder Image

This image is intended to be an image that encapsulates all the build dependencies. It can be used in the CI workflow or locally.

Build Image:

```bash
docker buildx build \
  --platform linux/amd64,linux/arm64 \
  -t docker.io/raspbernetes/builder:latest \
  -f scripts/builder/Dockerfile . --push
```

Usage:

```bash
docker run --rm --workdir /github/workspace \
  -v $(pwd):/github/workspace \
  -v $HOME/.kube/:/github/workspace/.kube/ \
  docker.io/raspbernetes/builder:latest \
  scripts/validate.sh
```
