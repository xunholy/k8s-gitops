# Sealed Secrets

!!! note "Work in progress"
    This document is a work in progress.

When bootstrapping your cluster for the first time you must store a unique public & private key for your sealed-secrets controller to use for managing your sensitive keys and passwords in your Kubernetes cluster.

## Install the CLI tool

*For all installation methods visit the Sealed Secrets [install guide](https://github.com/bitnami-labs/sealed-secrets#installation)*

```bash
brew install kubeseal
```

## Remove Existing Key & Cert

Remove the sealed-secrets master key currently present in this repository

```bash
rm -f kubernetes/clusters/<cluster>/secrets/sealed-secret-private-key.enc.yaml
```

Remove the sealed-secrets public cert currently present in this repository

```bash
rm -f kubernetes/clusters/<cluster>/secrets/sealed-secret-public-cert.yaml
```

## Store New Key & Cert

### Private Key

Once sealed-secrets has been re-deployed to a running cluster you must store the private key and public cert in this repository.

Get the generated sealed-secret private key

```bash
kubectl get secret -n kube-system -l sealedsecrets.bitnami.com/sealed-secrets-key -o yaml > kubernetes/clusters/<cluster>/secrets/sealed-secret-private-key.yaml
```

Encrypt the sealed-secret private key using SOPs

```bash
sops --encrypt kubernetes/clusters/<cluster>/secrets/sealed-secret-private-key.yaml > kubernetes/clusters/<cluster>/secrets/sealed-secret-private-key.enc.yaml
```

Remove unencrypted private key

```bash
rm -f kubernetes/clusters/<cluster>/secrets/sealed-secret-private-key.yaml
```

### Public Cert

Fetch the generated sealed-secret public cert and store it

```bash
kubeseal \
    --controller-name sealed-secrets \
    --fetch-cert > kubernetes/clusters/<cluster>/secrets/sealed-secret-public-cert.yaml
```

## Encrypt Secrets

With a newly generated private key from sealed-secrets you will need to re-encrypt all of the existing required secrets.

Create an alias for the CLI tool (*recommended*)

```bash
alias kubeseal='kubeseal --cert kubernetes/clusters/<cluster>/secrets/sealed-secret-public-cert.pem --controller-name sealed-secrets --format yaml'
```

Encrypt new Kubernetes secret

```bash
kubeseal < secret.yaml > secret.encrypted.yaml
```

Remove the unencrypted secret

You must encrypt your secrets with the correct cluster public certificate. For more in-depth instructions the official docs can be found [here](https://github.com/bitnami-labs/sealed-secrets#overview)

## Offline Decryption

Storing the private key allows an offline decryption, this is not recommended and should only be used in a break-glass scenario when the cluster is down and secrets must be accessed.

Unencrypt the sealed-secret private key

```bash
sops --decrypt kubernetes/clusters/<cluster>/secrets/sealed-secret-private-key.enc.yaml -oyaml > kubernetes/clusters/<cluster>/secrets/sealed-secret-private-key.yaml
```

Unseal the encrypted secret(s)

```bash
kubeseal --recovery-unseal --recovery-private-key ./kubernetes/clusters/<cluster>/secrets/sealed-secret-private-key.yaml < <path-to-file>/secret.encrypted.yaml
```

Re-Encrypt the sealed-secret private key

```bash
sops --encrypt kubernetes/clusters/<cluster>/secrets/sealed-secret-private-key.yaml > kubernetes/clusters/<cluster>/secrets/sealed-secret-private-key.enc.yaml
```

Remove the unencrypted private key

```bash
rm -f kubernetes/clusters/<cluster>/secrets/sealed-secret-private-key.yaml
```
