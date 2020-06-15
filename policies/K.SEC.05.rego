package containers_securitycontext_privileged_true

import data.lib.kubernetes

# https://kubesec.io/basics/containers-securitycontext-privileged-true/
violation[msg] {
	kubernetes.containers[container]
	container.securityContext.privileged
	msg = kubernetes.format(sprintf("%s in the %s %s is privileged", [container.name, kubernetes.kind, kubernetes.name]))
}
