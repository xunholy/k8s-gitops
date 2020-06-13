package cis_2_4

import data.lib.kubernetes

default_parameters = {
    "certFileFlag": "--peer-cert-file",
    "keyFileFlag": "--peer-key-file",
    "certFileRequiredValue": "/etc/kubernetes/pki/etcd/peer.crt",
    "keyFileRequiredValue": "/etc/kubernetes/pki/etcd/peer.key"
}

params = object.union(default_parameters, kubernetes.parameters)

violation[msg] {
    kubernetes.etcd[container]
    not kubernetes.flag_contains_string(container.command, params.certFileFlag, params.certFileRequiredValue)
    msg = kubernetes.format(sprintf("%s in the %s %s does not have %s %s", [container.name, kubernetes.kind, kubernetes.name, params.certFileFlag, params.certFileRequiredValue]))
}

violation[msg] {
    kubernetes.etcd[container]
    not kubernetes.flag_contains_string(container.command, params.keyFileFlag, params.keyFileRequiredValue)
    msg = kubernetes.format(sprintf("%s in the %s %s does not have %s %s", [container.name, kubernetes.kind, kubernetes.name, params.keyFileFlag, params.keyFileRequiredValue]))
}
