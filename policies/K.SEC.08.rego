package maicontainers_securitycontext_runasuser

import data.lib.kubernetes

# https://kubesec.io/basics/containers-securitycontext-runasuser/
violation[msg] {
	kubernetes.containers[container]
	container.securityContext.runAsUser < 10000
	msg = kubernetes.format(sprintf("%s in the %s %s has a UID of less than 10000", [container.name, kubernetes.kind, kubernetes.name]))
}
