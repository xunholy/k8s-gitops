package spec_hostpid

import data.lib.kubernetes

# https://kubesec.io/basics/spec-hostpid/
violation[msg] {
	kubernetes.pods[pod]
	pod.spec.hostPID
	msg = kubernetes.format(sprintf("The %s %s is sharing the host PID", [kubernetes.kind, kubernetes.name]))
}
