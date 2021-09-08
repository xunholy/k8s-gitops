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

# Canary

```bash
helm template manifests/charts/istio-operator/ \
  --set hub=docker.io/querycapistio \
  --set tag=1.6.5 \
  --set operatorNamespace=istio-operator \
  --set istioNamespace=istio-system \
  --set revision=canary > bla.yaml
```

## Demo Deployment

A demo application to test Istio sidecar injection

```bash
apiVersion: apps/v1
kind: Deployment
metadata:
  labels:
    app: curl
  name: curl
  namespace: test
spec:
  progressDeadlineSeconds: 600
  replicas: 1
  revisionHistoryLimit: 10
  selector:
    matchLabels:
      app: curl
  strategy:
    rollingUpdate:
      maxSurge: 25%
      maxUnavailable: 25%
    type: RollingUpdate
  template:
    metadata:
      labels:
        app: curl
    spec:
      containers:
      - image: curlimages/curl
        imagePullPolicy: Always
        name: curl
        resources: {}
        terminationMessagePath: /dev/termination-log
        terminationMessagePolicy: File
        command: ["sleep", "9999999"]
      dnsPolicy: ClusterFirst
      restartPolicy: Always
      schedulerName: default-scheduler
      securityContext: {}
```
