package cis_5_1_1

import data.lib.test

test_violation {
    test.violations(violation) with input as policy_input("example:view:binding", "cluster-admin")
}

test_no_violation {
    test.no_violations(violation) with input as policy_input("system:cluster-admin", "cluster-admin")
}

policy_input(name, ref) = {
    "apiVersion": "rbac.authorization.k8s.io/v1",
    "kind": "ClusterRoleBinding",
    "metadata": {
        "name": name
    },
    "roleRef": {
        "apiGroup": "rbac.authorization.k8s.io",
        "kind": "ClusterRole",
        "name": ref
    },
    "subjects": [
        {
            "apiGroup": "rbac.authorization.k8s.io",
            "kind": "Group",
            "name": "system:masters"
        }
    ]
}
