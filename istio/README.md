# Istio

> Note: This is still heavily a WIP

Step 1: Download specific istio version

```bash
curl -L https://istio.io/downloadIstio | ISTIO_VERSION=1.6.5 sh -
```

Step 2: Generate operator manifests

```bash
helm template manifests/charts/istio-operator/ \
  --set hub=docker.io/querycapistio \
  --set tag=1.6.5 \
  --set operatorNamespace=istio-operator \
  --set istioNamespace=istio-system
```

Alternatively install using the following command:

```bash
istioctl operator init --hub docker.io/querycapistio --tag 1.6.5
```

Step 3: Apply IstioOperator resource

Step 4: Edit deployments to include `arm64` in affinity
