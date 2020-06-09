package cis_1_2_13

import data.lib.kubernetes

violation[msg] {
	kubernetes.containers[container]
	not kubernetes.flag_contains_string(container, "--enable-admission-plugins", "SecurityContextDeny")
	msg = kubernetes.format(sprintf("%s in the %s %s does not have --enable-admission-plugins SecurityContextDeny", [container.name, kubernetes.kind, kubernetes.name]))
}
