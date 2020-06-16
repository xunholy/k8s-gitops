package cis_1_3_2

import data.lib.kubernetes

default_parameters = {
    "key": "--profiling",
    "requiredValue": "false"
}

params = object.union(default_parameters, kubernetes.parameters)

violation[msg] {
    kubernetes.controller[container]
    not kubernetes.flag_contains_string(container.command, params.key, params.requiredValue)
    msg = kubernetes.format(sprintf("%s in the %s %s does not have %s %s", [container.name, kubernetes.kind, kubernetes.name, params.key, params.requiredValue]))
}
