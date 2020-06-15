package containers_securitycontext_allowprivilegedeescalation_true

import data.lib.kubernetes

violation[msg] {
	kubernetes.containers[container]
	container.securityContext.allowPrivilegeEscalation
	msg = kubernetes.format(sprintf("%s in the %s %s allows priviledge escalation", [container.name, kubernetes.kind, kubernetes.name]))
}
