package spec_hostipc

import data.lib.kubernetes

# https://kubesec.io/basics/spec-hostipc/
violation[msg] {
	kubernetes.pods[pod]
	pod.spec.hostIPC
	msg = kubernetes.format(sprintf("%s %s is sharing the host IPC namespace", [kubernetes.kind, kubernetes.name]))
}
