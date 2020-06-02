package spec_hostnetwork

import data.lib.kubernetes

# https://kubesec.io/basics/spec-hostnetwork/
violation[msg] {
	kubernetes.pods[pod]
	pod.spec.hostNetwork
	msg = kubernetes.format(sprintf("The %s %s is connected to the host network", [kubernetes.kind, kubernetes.name]))
}
