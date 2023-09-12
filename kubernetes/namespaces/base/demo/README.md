The following was followed to generate the files in the `configconnector-operator-system` folder https://cloud.google.com/config-connector/docs/how-to/install-other-kubernetes

This generates the namespace and key to the project that will be used locally to install the config connector operator.

I had to update nodeSelector on the deployments and statefulsets as the images aren't multi-archiecture and therefore only worked on AMD64 nodes.

I created a new autopilot GKE cluster which is then going to become my hosted management cluster

Connect to cluster

```bash
gcloud auth login --cred-file=../../kubecon-mgmt.json
gcloud container clusters get-credentials cluster-01 --region us-west1 --project kubecon-mgmt
```

I will then install FluxCD into that cluster and have it begin syncing from a new demo repository

```bash
flux bootstrap github \
  --components-extra=image-reflector-controller,image-automation-controller \
  --owner="xUnholy" \
  --repository="next-demo-01" \
  --path=kubernetes/clusters/cluster-0" \
  --branch="main" \
  --personal=true \
  --private=false
```

Got blocked on auto-pilot - this quota stops flux from running all components because it's using the priority class system-cluster-critical; This meant I had to edit the deployment in-cluster and in-repo definition

```yaml
apiVersion: v1
kind: ResourceQuota
metadata:
  creationTimestamp: "2023-07-18T09:31:22Z"
  labels:
    addonmanager.kubernetes.io/mode: Reconcile
  name: gcp-critical-pods
  namespace: kube-system
  resourceVersion: "7592"
  uid: 9570ea75-5f14-47df-a2e3-9c194e51d066
spec:
  hard:
    pods: 1G
  scopeSelector:
    matchExpressions:
    - operator: In
      scopeName: PriorityClass
      values:
      - system-node-critical
      - system-cluster-critical
```

Didn't add secrets into demo repo, just applying them manually - will consider setting up SOPs with KMS
