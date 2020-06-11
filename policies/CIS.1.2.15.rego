package cis_1_2_15

import data.lib.kubernetes

default_parameters = {
    "key": "--disable-admission-plugins",
    "deniedValue": "NamespaceLifecycle"
}

params = object.union(default_parameters, kubernetes.parameters)

violation[msg] {
    kubernetes.apiserver[container]
    kubernetes.flag_contains_string(container.command, "--disable-admission-plugins", "NamespaceLifecycle")
    msg = kubernetes.format(sprintf("%s in the %s %s should not have --disable-admission-plugins NamespaceLifecycle", [container.name, kubernetes.kind, kubernetes.name]))
}
