package cis_5_1_3

import data.lib.kubernetes

violation[msg] {
    kubernetes.clusterroles[clusterrole]
    is_using_wildcard(clusterrole.rules[_])
    msg = kubernetes.format(sprintf("ClusterRole %v - use of wildcard is not allowed", [clusterrole.metadata.name]))
}

violation[msg] {
    kubernetes.roles[role]
    is_using_wildcard(role.rules[_])
    msg = kubernetes.format(sprintf("Role %v - use of wildcard is not allowed", [role.metadata.name]))
}

is_using_wildcard(rule) {
    rule.apiGroups[_] == "*"
}

is_using_wildcard(rule) {
    rule.resources[_] == "*"
}

is_using_wildcard(rule) {
    rule.verbs[_] == "*"
}
