package spec_hostaliases

import data.lib.kubernetes

# https://kubesec.io/basics/spec-hostaliases/
violation[msg] {
	kubernetes.pods[pod]
	pod.spec.hostAliases
	msg = kubernetes.format(sprintf("The %s %s is managing host aliases", [kubernetes.kind, kubernetes.name]))
}
