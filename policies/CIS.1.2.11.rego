package cis_1_2_11

import data.lib.kubernetes

violation[msg] {
  kubernetes.containers[container]
  kubernetes.flag_contains_string(container, "--enable-admission-plugins", "AlwaysAdmit")
  msg = kubernetes.format(sprintf("%s in the %s %s should not have --enable-admission-plugins AlwaysAdmit", [container.name, kubernetes.kind, kubernetes.name]))
}
