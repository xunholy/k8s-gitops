package cis_1_4_2

import data.lib.kubernetes

default_parameters = {
    "key": "--bind-address",
    "requiredValue": "127.0.0.1"
}

params = object.union(default_parameters, kubernetes.parameters)

violation[msg] {
    kubernetes.scheduler[container]
    not kubernetes.flag_contains_string(container.command, params.key, params.requiredValue)
    msg = kubernetes.format(sprintf("%s in the %s %s does not have %s %s", [container.name, kubernetes.kind, kubernetes.name, params.key, params.requiredValue]))
}
