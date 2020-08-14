Build Image:

```bash
docker build -t raspbernetes/builder:latest \
  -f scripts/builder/Dockerfile .
```

Test Image:

```bash
docker run --rm --workdir /github/workspace \
  -v $(pwd):/github/workspace \
  raspbernetes/builder:latest \
  bash scripts/helm.sh
```

Push Image:

```bash
docker push raspbernetes/builder:latest
```
