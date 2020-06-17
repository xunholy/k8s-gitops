package cis_5_1_2

import data.lib.test

test_violation {
    test.violations(violation) with input as policy_input("ClusterRole", "system:node", "", "*", "*")
}

test_violation_2 {
    test.violations(violation) with input as policy_input("ClusterRole", "system:node", "*", "*", "*")
}

test_violation_3 {
    test.violations(violation) with input as policy_input("ClusterRole", "system:node", "*", "*", "get")
}

test_violation_4 {
    test.violations(violation) with input as policy_input("ClusterRole", "system:node", "*", "secrets", "*")
}

test_violation_5 {
    test.violations(violation) with input as policy_input("Role", "system:node", "*", "secrets", "*")
}

test_no_violation {
    test.no_violations(violation) with input as policy_input("ClusterRole", "cluster-admin", "", "*", "*")
}

test_no_violation_2 {
    test.no_violations(violation) with input as policy_input("ClusterRole", "system:node", "extentions", "*", "*")
}

test_no_violation_3 {
    test.no_violations(violation) with input as policy_input("ClusterRole", "system:node", "*", "pods", "*")
}

test_no_violation_4 {
    test.no_violations(violation) with input as policy_input("ClusterRole", "system:node", "*", "*", "create")
}

test_no_violation_5 {
    test.no_violations(violation) with input as policy_input("Role", "system:node", "*", "*", "create")
}

policy_input(rolekind, name, apiGroup, resource, verb) = {
    "apiVersion": "rbac.authorization.k8s.io/v1",
    "kind": rolekind,
    "metadata": {
        "annotations": {
            "rbac.authorization.kubernetes.io/autoupdate": "true"
        },
        "labels": {
            "kubernetes.io/bootstrapping": "rbac-defaults"
        },
        "name": name
    },
    "rules": [
        {
            "apiGroups": [
                apiGroup
            ],
            "resources": [
                resource
            ],
            "verbs": [
                verb
            ]
        }
    ]
}
