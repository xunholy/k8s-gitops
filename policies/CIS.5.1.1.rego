package cis_5_1_1

import data.lib.kubernetes

violation[msg] {
    kubernetes.clusterrolebindings[clusterrolebinding]
    is_clusterrole_admin(clusterrolebinding)
    msg = kubernetes.format(sprintf("ClusterRoleBinding %v - Binding to cluster-admin role is not allowed", [clusterrolebinding.metadata.name]))
}

is_clusterrole_admin(clusterrolebinding) {
    clusterrolebinding.roleRef.name == "cluster-admin"
    startswith(clusterrolebinding.metadata.name, "system:") == false
}
