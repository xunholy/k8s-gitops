package cis_5_1_3

import data.lib.test

test_violation {
    test.violations(violation) with input as policy_input("ClusterRole", "authorization.k8s.io", "tokenviews", "*")
}

test_violation {
    test.violations(violation) with input as policy_input("ClusterRole", "authorization.k8s.io", "*", "create")
}

test_violation {
    test.violations(violation) with input as policy_input("ClusterRole", "*", "tokenviews", "create")
}

test_violation {
    test.violations(violation) with input as policy_input("Role", "authorization.k8s.io", "tokenviews", "*")
}

test_violation {
    test.violations(violation) with input as policy_input("Role", "authorization.k8s.io", "*", "create")
}

test_violation {
    test.violations(violation) with input as policy_input("Role", "*", "tokenviews", "create")
}

test_no_violation {
    test.no_violations(violation) with input as policy_input("ClusterRole", "authorization.k8s.io", "tokenviews", "create")
}

test_no_violation_2 {
    test.no_violations(violation) with input as policy_input("Role", "authorization.k8s.io", "tokenviews", "create")
}

policy_input(rolekind, apiGroup, resource, verb) = {
    "apiVersion": "rbac.authorization.k8s.io/v1",
    "kind": rolekind,
    "metadata": {
        "annotations": {
            "rbac.authorization.kubernetes.io/autoupdate": "true"
        },
        "labels": {
            "kubernetes.io/bootstrapping": "rbac-defaults"
        },
        "name": "system:node"
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
