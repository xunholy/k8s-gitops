package cis_1_2_1

import data.lib.test

test_violation_no_flags {
    test.violations(violation) with input as policy_input("kube-apiserver", "command", "")
}

test_violation_denied_value {
    test.violations(violation) with input as policy_input("kube-apiserver", "command", "--anonymous-auth=true")
}

test_violation_no_commands {
    test.violations(violation) with input as policy_input("kube-apiserver", "", "")
}

test_no_violation_false_an {
    test.no_violations(violation) with input as policy_input("kube-apiserver", "command", "--anonymous-auth=false")
}

test_no_violation_not_apiserver {
    test.no_violations(violation) with input as policy_input("kube-proxy", "", "")
}

policy_input(component, command, kv) = {
  "apiVersion": "v1",
  "kind": "Pod",
  "metadata": {
    "labels": {
      "component": component,
      "tier": "control-plane"
    }
  },
  "spec": {
    "containers": [
      {
        command: [kv],
        "image": "k8s.gcr.io/kube-apiserver:v1.18.3",
        "imagePullPolicy": "IfNotPresent",
        "name": "kube-apiserver"
      }
    ]
  }
}
