package containers_securitycontext_capabilities_add_index_sys_admim

import data.lib.kubernetes

# https://kubesec.io/basics/containers-securitycontext-capabilities-add-index-sys-admin/
violation[msg] {
	kubernetes.containers[container]
	added_capability(container, "CAP_SYS_ADMIN")
	msg = kubernetes.format(sprintf("%s in the %s %s has SYS_ADMIN capabilties", [container.name, kubernetes.kind, kubernetes.name]))
}

added_capability(container, cap) {
	container.securityContext.capabilities.add[_] == cap
}
