
kubectl create secret generic cloudflare-ddns \
  --from-literal=api-token="insert api-token here" \
  --from-literal=zones="raspbernetes.com" \
  --from-literal=hosts="ingress.raspbernetes.com" \
  --from-literal=record-types="A;A;AAAA" \
  --namespace network --dry-run -o yaml > secret.yaml
