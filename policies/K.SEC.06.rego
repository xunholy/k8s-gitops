package containers_securitycontext_readonlyrootfilesystem_true

import data.lib.kubernetes

# https://kubesec.io/basics/containers-securitycontext-readonlyrootfilesystem-true/
violation[msg] {
	kubernetes.containers[container]
	not container.securityContext.readOnlyRootFilesystem
	msg = kubernetes.format(sprintf("%s in the %s %s is not using a read only root filesystem", [container.name, kubernetes.kind, kubernetes.name]))
}
