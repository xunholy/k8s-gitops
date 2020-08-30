# Dex

Enter valid secrets into the values.yaml and use the following command to generate the secret, then use sealed secrets to encrypt these values to be used within the helm release resource.

```bash
kubectl -n network create secret generic dex-values --from-file=values.yaml=namespaces/network/dex/values.yaml --dry-run=true -o yaml > secret.yaml
```
