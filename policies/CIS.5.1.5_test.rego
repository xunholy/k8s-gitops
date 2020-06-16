package cis_5_1_5

import data.lib.test

test_violation {
    test.violations(violation) with input as policy_input("ServiceAccount", true, "example-service-account")
}

test_violation_2 {
    test.violations(violation) with input as policy_input("Pod", true, "example-service-account")
}

test_violation_3 {
    test.violations(violation) with input as policy_input("Pod", false, "default")
}

test_no_violation {
    test.no_violations(violation) with input as policy_input("Pod", false, "example-service-account")
}

test_no_violation_2 {
    test.no_violations(violation) with input as policy_input("ServiceAccount", false, "example-service-account")
}

policy_input(kind, automountSetting, serviceAccount) = {
    "apiVersion": "v1",
    "kind": kind,
    "automountServiceAccountToken": automountSetting,
    "metadata": {
        "name": "example-serviceaccount-or-pod"
    },
    "spec": {
        "serviceAccount": serviceAccount,
        "automountServiceAccountToken": automountSetting
    }
}
