
kubectl create secret generic cloudflare-ddns \
  --from-literal=api-token="GMJuzQ3sijHCE2dYD1hB7-LthWavnCf8hOxo4zfh" \
  --from-literal=zones="raspbernetes.com" \
  --from-literal=hosts="ingress.raspbernetes.com" \
  --from-literal=record-types="A;A;AAAA" \
  --namespace network --dry-run -o yaml > secret.yaml
