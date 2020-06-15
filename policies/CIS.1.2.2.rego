package cis_1_2_2

import data.lib.kubernetes

default_parameters = {
    "key": "--basic-auth-file",
}

params = object.union(default_parameters, kubernetes.parameters)

violation[msg] {
    kubernetes.apiserver[container]
    kubernetes.contains_element(container.command, params.key)
    msg = kubernetes.format(sprintf("%s in the %s %s should not have %s", [container.name, kubernetes.kind, kubernetes.name, params.key]))
}
