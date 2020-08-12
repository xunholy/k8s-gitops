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
  bash scripts/helm.gen
```

Push Image:

```bash
docker push raspbernetes/builder:latest
```


$(docker run --rm --workdir /github/workspace \
            -v $(pwd):/github/workspace \
            openpolicyagent/opa:0.17.2 \
            test security-policies/policies/ -v || exit 0 )
