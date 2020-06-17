package cis_5_1_2

import data.lib.kubernetes

default_parameters = {
    "allowedClusterRoles": [
        "cluster-admin",
        "system:controller:clusterrole-aggregation-controller",
        "system:controller:expand-controller",
        "system:controller:generic-garbage-collector",
        "system:controller:namespace-controller",
        "system:controller:persistent-volume-binder",
        "system:kube-controller-manager"
    ]
}

params = object.union(default_parameters, kubernetes.parameters)

violation[msg] {
    kubernetes.clusterroles[clusterrole]
    is_access_to_secrets_disallowed(clusterrole)
    msg = kubernetes.format(sprintf("ClusterRole %v - access to secrets is not allowed", [clusterrole.metadata.name]))
}

violation[msg] {
    kubernetes.roles[role]
    is_access_to_secrets_disallowed(role)
    msg = kubernetes.format(sprintf("Role %v - access to secrets is not allowed", [role.metadata.name]))
}

is_access_to_secrets_disallowed(role) = false {
    role.metadata.name == params.allowedClusterRoles[_]
} else = true {
    verbs :=  { v |
        api_groups := { r | r := role.rules[i].apiGroups[_] }
        count(api_groups & {"", "*"}) > 0

        resources := { r | r := role.rules[i].resources[_] }
        count(resources & {"secrets", "*"}) > 0

        v := role.rules[i].verbs[_]
    }
    disallowed_verbs := { "get", "list", "watch", "*" }
    count(verbs & disallowed_verbs) > 0
}
