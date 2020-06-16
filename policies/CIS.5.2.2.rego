package cis_5_2_2

import data.lib.kubernetes

violation[msg] {
	kubernetes.pods[pod]
	pod.spec.hostPID
	msg = kubernetes.format(sprintf("The %s %s is sharing the host PID", [kubernetes.kind, kubernetes.name]))
}
