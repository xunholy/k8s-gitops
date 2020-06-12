package cis_5_1_1

import data.lib.kubernetes

violation[msg] {
    kubernetes.clusterrolebindings[clusterrolebinding]
    is_clusterrole_admin(clusterrolebinding)
    msg = kubernetes.format(sprintf("ClusterRoleBinding %v - Binding to cluster-admin role is not allowed", [clusterrolebinding.metadata.name]))
}

violation[msg] {
    kubernetes.rolebindings[rolebinding]
    is_clusterrole_admin(rolebinding)
    msg = kubernetes.format(sprintf("RoleBinding %v - Binding to cluster-admin role is not allowed", [rolebinding.metadata.name]))
}

is_clusterrole_admin(rolebinding) {
    rolebinding.roleRef.name == "cluster-admin"
    startswith(rolebinding.metadata.name, "system:") == false
}
