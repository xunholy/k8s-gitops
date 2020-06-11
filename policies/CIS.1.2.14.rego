package cis_1_2_14

import data.lib.kubernetes

violation[msg] {
    kubernetes.apiserver[container]
    kubernetes.flag_contains_string(container.command, "--disable-admission-plugins", "ServiceAccount")
    msg = kubernetes.format(sprintf("%s in the %s %s should not have --disable-admission-plugins ServiceAccount", [container.name, kubernetes.kind, kubernetes.name]))
}
