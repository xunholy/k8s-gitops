package cis_5_2_4

import data.lib.kubernetes

violation[msg] {
	kubernetes.pods[pod]
	pod.spec.hostNetwork
	msg = kubernetes.format(sprintf("The %s %s is connected to the host network", [kubernetes.kind, kubernetes.name]))
}
