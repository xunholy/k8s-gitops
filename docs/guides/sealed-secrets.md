# Sealed Secrets

!!! note "Work in progress"
    This document is a work in progress.

When bootstrapping your cluster for the first time you must store a unique public & private key for your sealed-secrets controller to use for managing your sensative keys and passwords in your Kubernetes cluster.

## Install the CLI tool

```bash
brew install kubeseal
```

Note: For other installation methods visit the official [docs](https://github.com/bitnami-labs/sealed-secrets#installation)

## Remove Existing Key & Cert

1. Remove the sealed-secrets master key currently present in this repository

```bash
rm -f k8s/clusters/<cluster>/secrets/sealed-secret-private-key.enc.yaml
```

2. Remove the sealed-secrets public cert currently present in this repository

```bash
rm -f k8s/clusters/<cluster>/secrets/sealed-secret-public-cert.yaml
```

## Store New Key & Cert

### Private Key

Once sealed-secrets has been re-deployed to a running cluster you must store the private key and public cert in this repository.

1. Get the generated sealed-secret private key

```bash
kubectl get secret -n kube-system -l sealedsecrets.bitnami.com/sealed-secrets-key -o yaml > k8s/clusters/<cluster>/secrets/sealed-secret-private-key.yaml
```

2. Encrypt the sealed-secret private key using SOPs

```bash
sops --encrypt k8s/clusters/<cluster>/secrets/sealed-secret-private-key.yaml > k8s/clusters/<cluster>/secrets/sealed-secret-private-key.enc.yaml
```

3. Remove unencrypted private key

```bash
rm -f k8s/clusters/<cluster>/secrets/sealed-secret-private-key.yaml
```

### Public Cert

1. Fetch the generated sealed-secret public cert

```bash
kubeseal \
    --controller-name sealed-secrets \
    --fetch-cert > k8s/clusters/<cluster>/secrets/sealed-secret-public-cert.yaml
```

## Encrypt Secrets

With a newly generated private key from sealed-secrets you will need to re-encrypt all of the existing required secrets.

1. Create an alias for the CLI tool (*recommended*)

```bash
alias kubeseal='kubeseal --cert k8s/clusters/<cluster>/secrets/sealed-secret-public-cert.yaml --controller-name sealed-secrets --format yaml'
```

2. Encrypt new kubernetes secret

```bash
kubeseal < secret.yaml > secret.encrypted.yaml
```

3. Remove unencrypted secret

You must encrypt your secrets with the correct cluster public certificate. For more in-depth instructions the official docs can be found [here](https://github.com/bitnami-labs/sealed-secrets#overview)

## Offline Decryption

Storing the private key allows an offline decryption, this is not recommended and should only be used in a break-glass scenario when the cluster is down and secrets must be accessed.

1. Unencrypt the sealed-secret private key

```bash
sops --decrypt k8s/clusters/<cluster>/secrets/sealed-secret-private-key.enc.yaml > k8s/clusters/<cluster>/secrets/sealed-secret-private-key.yaml
```

2. Unseal encrypted secret(s)

```bash
kubeseal --recovery-unseal --recovery-private-key .secrets/git-crypt/k8s-secret-sealed-secret-private-key.yaml < <path-to-file>/secret.encrypted.yaml
```

3. Re-Encrypt the sealed-secret private key

```bash
sops --encrypt k8s/clusters/<cluster>/secrets/sealed-secret-private-key.yaml > k8s/clusters/<cluster>/secrets/sealed-secret-private-key.enc.yaml
```

4. Remove unencrypted private key

```bash
rm -f k8s/clusters/<cluster>/secrets/sealed-secret-private-key.yaml
```
