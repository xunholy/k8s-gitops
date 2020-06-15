package containers_resources_limits_cpu

import data.lib.kubernetes

# https://kubesec.io/basics/containers-resources-limits-cpu/
violation[msg] {
	kubernetes.containers[container]
	not container.resources.limits.cpu
	msg = kubernetes.format(sprintf("%s in the %s %s does not have a CPU limit set", [container.name, kubernetes.kind, kubernetes.name]))
}
