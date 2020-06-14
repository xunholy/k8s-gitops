package cis_2_1

import data.lib.kubernetes

default_parameters = {
    "certFileFlag": "--cert-file",
    "keyFileFlag": "--key-file",
    "certFileRequiredValue": "/etc/kubernetes/pki/etcd/server.crt",
    "keyFileRequiredValue": "/etc/kubernetes/pki/etcd/server.key"
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
