# Dex

Enter valid secrets into the values.yaml and use the following command to generate the secret, then use sealed secrets to encrypt these values to be used within the helm release resource.

```bash
kubectl create secret generic dex-helm-values --from-file=values.yaml=config/dex/values.yaml --dry-run=client -n network -o yaml > secret.yaml
```
