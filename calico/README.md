# eBPF

Needed to updated the `calicoctl` command as I originally have the following error:

```bash
Failed to create Calico API client: no etcd endpoints specified
```

Added `DATASTORE_TYPE` & `KUBECONFIG` to command as recommended here https://docs.projectcalico.org/getting-started/clis/calicoctl/configure/kdd

```bash
DATASTORE_TYPE=kubernetes KUBECONFIG=~/.kube/config calicoctl patch felixconfiguration default --patch='{"spec": {"bpfEnabled": true}}'
```

## Monitoring

Resources:

https://docs.projectcalico.org/maintenance/monitor/monitor-component-metrics
https://docs.projectcalico.org/reference/felix/prometheus

> TODO: When you enable Calico metrics to Prometheus, a best practice is to use network policy to limit access to the Calico metrics endpoints. https://docs.projectcalico.org/security/comms/secure-metrics
