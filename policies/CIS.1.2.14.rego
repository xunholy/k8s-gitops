package cis_1_2_14

import data.lib.kubernetes

default_parameters = {
    "key": "--disable-admission-plugins",
    "deniedValue": "ServiceAccount"
}

params = object.union(default_parameters, kubernetes.parameters)

violation[msg] {
    kubernetes.apiserver[container]
    kubernetes.flag_contains_string(container.command, "--disable-admission-plugins", "ServiceAccount")
    msg = kubernetes.format(sprintf("%s in the %s %s should not have --disable-admission-plugins ServiceAccount", [container.name, kubernetes.kind, kubernetes.name]))
}
