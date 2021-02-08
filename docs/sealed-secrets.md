# Sealed Secrets

Assuming you're bootstrapping your cluster for the first time you must create a unique public & private key for your sealed-secrets controller to use for managing your sensative keys and passwords in your Kubernetes cluster.

## Setup

Remove the one currently present in this repository.

```bash
rm -f secrets/k8s-secret-sealed-secret-private-key.yaml
```

To get the newly generated sealed-secret private key use the following command:

```bash
kubectl get secret -n kube-system -l sealedsecrets.bitnami.com/sealed-secrets-key -o yaml > secrets/k8s-secret-sealed-secret-private-key.yaml
```

Once you have obtained your new private key from sealed-secrets you will need to re-encrypt all of your secrets, currently mine are stored in the `secrets/` dir. You must also encrypt your secrets with the corresponding public certificate. Use the official guide in the documentation found [HERE](https://github.com/bitnami-labs/sealed-secrets#overview)

If you followed this guide correctly all future times you bootstrap your cluster using the `install.sh` your cluster will re-use the private key and you will NOT need to re-encypt secrets everytime.

Note: These secrets are also in the repository as `SealedSecret` kubernetes resources which will show the data structure you must follow; Instructions will be added for each secret respectively in the future on how it must be generated and what fields it requires. It's recommended to store secrets using `git-crypt` so if the cluster is unavailable or you lose your private key you can still view the secrets in plaintext locally. (`git-crypt` may also be substituted to `SOPS` in a later implementation)
