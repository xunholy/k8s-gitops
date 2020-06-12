package cis_5_1_1

import data.lib.test

test_violation {
    test.violations(violation) with input as policy_input("ClusterRoleBinding", "example:view:binding", "cluster-admin")
}

test_violation_2 {
    test.violations(violation) with input as policy_input("RoleBinding", "example:view:binding", "cluster-admin")
}

test_no_violation {
    test.no_violations(violation) with input as policy_input("ClusterRoleBinding", "system:cluster-admin", "cluster-admin")
}

test_no_violation_2 {
    test.no_violations(violation) with input as policy_input("RoleBinding", "system:cluster-admin", "cluster-admin")
}

test_no_violation_3 {
    test.no_violations(violation) with input as policy_input("ClusterRoleBinding", "stackdriver:fluentd-gcp", "stackdriver:fluentd-gcp")
}

test_no_violation_4 {
    test.no_violations(violation) with input as policy_input("RoleBinding", "stackdriver:fluentd-gcp", "stackdriver:fluentd-gcp")
}

policy_input(rolebindingkind, name, ref) = {
    "apiVersion": "rbac.authorization.k8s.io/v1",
    "kind": rolebindingkind,
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
