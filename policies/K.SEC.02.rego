package containers_resources_limits_memory

import data.lib.kubernetes

# https://kubesec.io/basics/containers-resources-limits-memory
violation[msg] {
	kubernetes.containers[container]
	not container.resources.limits.memory
	msg = kubernetes.format(sprintf("%s in the %s %s does not have a memory limit set", [container.name, kubernetes.kind, kubernetes.name]))
}
