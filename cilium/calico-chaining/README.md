# Cilium

## Calico Chaining

Documentation: https://docs.cilium.io/en/v1.8/gettingstarted/cni-chaining-calico/

### Deployment

```bash
kubectl apply -f cilium/calico-chaining/chaining.yaml
```

```bash
helm repo add cilium https://helm.cilium.io/
```

#### Helm Template (Optional)

```bash
helm template cilium/cilium --version v1.9.0-rc0 \
  --namespace=kube-system \
  --values=cilium/calico-chaining/values.yaml > cilium/calico-chaining/cilium.yaml
```

```bash
kubectl apply -f cilium/calico-chaining/cilium.yaml
```

#### Helm Install (Recommended)

```bash
helm install cilium cilium/cilium --version v1.9.0-rc0 \
  --namespace=kube-system \
  --values=cilium/calico-chaining/values.yaml
```

### Testing

Image: https://hub.docker.com/r/raspbernetes/json-mock

```bash
kubectl apply -f cilium/calico-chaining/json-mock.yaml
```

### Cleanup

```bash
kubectl delete -f cilium/calico-chaining/chaining.yaml
```

#### Helm Template Cleanup

```bash
kubectl delete -f cilium/calico-chaining/cilium.yaml
```

#### Helm Install Cleanup

```bash
helm uninstall cilium
```

```bash
kubectl delete -f cilium/calico-chaining/json-mock.yaml
```

## Output

```bash
❯ k get po
NAME                                                    READY   STATUS    RESTARTS   AGE
calico-kube-controllers-c9784d67d-pmh2h                 1/1     Running   1          64m
calico-node-j2ppc                                       1/1     Running   0          64m
calico-node-m6c74                                       1/1     Running   0          64m
calico-node-rhlw8                                       1/1     Running   0          64m
calico-node-rm9nj                                       1/1     Running   0          64m
cilium-62whg                                            1/1     Running   0          21m
cilium-7q7bj                                            1/1     Running   1          21m
cilium-b6zd9                                            1/1     Running   1          21m
cilium-gwrmj                                            1/1     Running   0          21m
cilium-operator-5cf59548b6-7vdn4                        1/1     Running   0          21m
cilium-operator-5cf59548b6-mthbh                        1/1     Running   1          21m
coredns-f9fd979d6-kh8j9                                 1/1     Running   0          14m
coredns-f9fd979d6-zzwxk                                 1/1     Running   0          19m
echo-a-66c7b457cb-5pnqn                                 1/1     Running   0          5m
echo-b-5cb69b67dd-869ll                                 1/1     Running   0          5m
echo-b-host-fbccc9bb9-9dgc6                             1/1     Running   0          5m
etcd-k8s-master-01                                      1/1     Running   0          115m
etcd-k8s-master-02                                      1/1     Running   1          115m
etcd-k8s-master-03                                      1/1     Running   0          114m
host-to-b-multi-node-clusterip-5b7666b85f-fnkn2         0/1     Running   4          4m56s
host-to-b-multi-node-headless-7788c557df-shn2d          0/1     Running   4          4m55s
kube-apiserver-k8s-master-01                            1/1     Running   0          115m
kube-apiserver-k8s-master-02                            1/1     Running   1          115m
kube-apiserver-k8s-master-03                            1/1     Running   1          114m
kube-controller-manager-k8s-master-01                   1/1     Running   1          115m
kube-controller-manager-k8s-master-02                   1/1     Running   2          115m
kube-controller-manager-k8s-master-03                   1/1     Running   1          113m
kube-proxy-bvvft                                        1/1     Running   0          115m
kube-proxy-h6l52                                        1/1     Running   0          115m
kube-proxy-x6fg9                                        1/1     Running   0          114m
kube-proxy-zqnw8                                        1/1     Running   0          115m
kube-scheduler-k8s-master-01                            1/1     Running   1          115m
kube-scheduler-k8s-master-02                            1/1     Running   2          115m
kube-scheduler-k8s-master-03                            1/1     Running   1          113m
metrics-server-64dd4994b-mw8g2                          1/1     Running   1          108m
pod-to-a-85c9d7755c-29fnd                               0/1     Running   4          4m59s
pod-to-a-allowed-cnp-655c99c98f-7q84v                   0/1     Running   4          4m58s
pod-to-a-denied-cnp-7998f5bd67-jrxg7                    1/1     Running   0          4m58s
pod-to-b-intra-node-nodeport-8d9fb4ccc-gb45d            0/1     Running   4          4m53s
pod-to-b-multi-node-clusterip-c6b4b97c7-kmgdx           0/1     Running   4          4m57s
pod-to-b-multi-node-headless-54649b5569-s6rmd           0/1     Running   4          4m56s
pod-to-b-multi-node-nodeport-75bfddc769-gh4ql           0/1     Running   4          4m54s
pod-to-external-1111-64cffd6cd7-xmvs5                   1/1     Running   0          4m59s
pod-to-external-fqdn-allow-google-cnp-95c44f8ff-ftm5b   0/1     Running   4          4m57s
```
