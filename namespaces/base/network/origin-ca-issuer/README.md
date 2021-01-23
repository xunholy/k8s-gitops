# Origin CA Issuer

The cloudflare Origin CA Issuer helm charts are not published and therefore purely rendered templates from the upstream source https://github.com/cloudflare/origin-ca-issuer.git

Clone repository

```bash
git clone https://github.com/cloudflare/origin-ca-issuer.git
```

Render helm templates

```bash
helm template deploy/charts/origin-ca-issuer/ \
  --name-template=default \
  --namespace=network \
  --output-dir=.
```

CRDs are not installed as part of the chart and must be manually copied.
