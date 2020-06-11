package cis_1_2_11

import data.lib.kubernetes

default_parameters = {
    "key": "--enable-admission-plugins",
    "deniedValue": "AlwaysAdmit"
}

params = object.union(default_parameters, kubernetes.parameters)

violation[msg] {
    kubernetes.apiserver[container]
    kubernetes.flag_contains_string(container.command, params.key, params.deniedValue)
    msg = kubernetes.format(sprintf("%s in the %s %s should not have %s %s", [container.name, kubernetes.kind, kubernetes.name, params.key, params.deniedValue]))
}
