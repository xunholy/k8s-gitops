package cis_1_2_1

import data.lib.kubernetes

default_parameters = {
    "key": "--anonymous-auth",
    "deniedValue": "true"
}

params = object.union(default_parameters, kubernetes.parameters)

violation[msg] {
    kubernetes.apiserver[container]
    not kubernetes.has_field(container,"command")
    msg = kubernetes.format(sprintf("%s in the %s %s should not have %s %s", [container.name, kubernetes.kind, kubernetes.name, params.key, params.deniedValue]))
}

violation[msg] {
    kubernetes.apiserver[container]
    not kubernetes.contains_element(container.command, params.key)
    msg = kubernetes.format(sprintf("%s in the %s %s should not have %s %s", [container.name, kubernetes.kind, kubernetes.name, params.key, params.deniedValue]))
}

violation[msg] {
    kubernetes.apiserver[container]
    kubernetes.flag_contains_string(container.command, params.key, params.deniedValue)
    msg = kubernetes.format(sprintf("%s in the %s %s should not have %s %s", [container.name, kubernetes.kind, kubernetes.name, params.key, params.deniedValue]))
}
