package cis_5_1_1

import data.lib.kubernetes

violation[msg] {
    kubernetes.clusterrolebindings[clusterrolebinding]
    not is_clusterrole_admin(clusterrolebinding)
    msg = kubernetes.format(sprintf("ClusterRoleBinding %v - Binding to cluster-admin role is not allowed", [clusterrolebinding.metadata.name]))
}

is_clusterrole_admin(clusterrolebinding) {
    trace(sprintf("clusterroleRoleRefname: %v", [clusterrolebinding.roleRef.name]))
    clusterrolebinding.roleRef.name == "cluster-admin"
    trace(sprintf("clusterrolename: %v", [clusterrolebinding.metadata.name]))
    startswith(clusterrolebinding.metadata.name, "system:")
}
