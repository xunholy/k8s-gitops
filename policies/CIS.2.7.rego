package cis_2_7

import data.lib.kubernetes

params = {
    "key": "--client-ca-file", 
    "key2": "--etcd-cafile"
}

violation[msg]{
    kubernetes.apiserver[container]
    kubernetes.value_by_key(container.command, params.key) == kubernetes.value_by_key(container.command, params.key2)
    msg = kubernetes.format(sprintf("%s in the %s %s should not have same value for %s and %s", [container.name, kubernetes.kind, kubernetes.name, params.key, params.key2]))
}


