package cis_5_4_1

import data.lib.kubernetes

violation[msg] {
    kubernetes.containers[container]
    container.env[_].valueFrom.secretKeyRef
    msg = kubernetes.format(sprintf("%s in the %s %s is not allowed to use secrets as environment variables", [container.name, kubernetes.kind, kubernetes.name]))
}
