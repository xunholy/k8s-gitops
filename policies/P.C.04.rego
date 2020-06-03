package containers_securitycontext_capabilities_drop_index_all

import data.lib.kubernetes

# https://kubesec.io/basics/containers-securitycontext-capabilities-drop-index-all/
violation[msg] {
	kubernetes.containers[container]
	not dropped_capability(container, "ALL")
	msg = kubernetes.format(sprintf("%s in the %s %s doesn't drop all capabilities", [container.name, kubernetes.kind, kubernetes.name]))
}

dropped_capability(container, cap) {
	container.securityContext.capabilities.drop[_] == cap
}
