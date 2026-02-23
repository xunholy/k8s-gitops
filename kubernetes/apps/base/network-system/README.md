# Cloudflare DDNS

https://github.com/hotio/docker-cloudflare-ddns

Use the following command to generate the secret with the required fields for the deployment.

```bash
kubectl create secret generic cloudflare-ddns \
  --from-literal=api-token="" \
  --from-literal=zones="owncloud.ai" \
  --from-literal=hosts="owncloud.ai" \
  --from-literal=record-types="A;A;AAAA" \
  --namespace network-system --dry-run -o yaml > .secrets/k8s-secret-cloudflare-ddns.yaml
```

# Cert-Manager

```bash
kubectl create secret generic cloudflare-cert-manager-token \
  --from-literal=api-token="" \
  --namespace network-system --dry-run -o yaml > .secrets/k8s-secret-cloudflare-cert-manager-token.yaml
```

# Oauth2-Proxy

https://github.com/oauth2-proxy/oauth2-proxy/blob/master/docs/configuration/configuration.md

Run the following command to generate a secure cookie secret:

```bash
python -c 'import os,base64; print base64.b64encode(os.urandom(16))'
```

Then, create the Kubernetes secret, substituting the highlighted values for your cookie secret, your GitHub client ID, and your GitHub secret key:

```bash
kubectl -n network-system create secret generic oauth2-proxy \
  --from-literal=cookie-secret="" \
  --from-literal=client-id="" \
  --from-literal=client-secret="" --dry-run -o yaml > .secrets/k8s-secret-github-oauth2.yaml
```
