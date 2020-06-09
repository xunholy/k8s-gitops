package cis_1_2_15

import data.lib.kubernetes

violation[msg] {
	kubernetes.containers[container]
	kubernetes.flag_contains_string(container.command, "--disable-admission-plugins", "NamespaceLifecycle")
	msg = kubernetes.format(sprintf("%s in the %s %s should not have --disable-admission-plugins NamespaceLifecycle", [container.name, kubernetes.kind, kubernetes.name]))
}
