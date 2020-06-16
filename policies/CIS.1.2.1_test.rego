package cis_1_2_1

import data.lib.test

test_violation {
    test.violations(violation) with input as policy_input("kube-apiserver", "")
}

test_violation_2 {
    test.violations(violation) with input as policy_input("kube-apiserver", "--anonymous-auth=true")
}

test_no_violation {
    test.no_violations(violation) with input as policy_input("kube-apiserver", "--anonymous-auth=false")
}

test_no_violation_2 {
    test.no_violations(violation) with input as policy_input("kube-proxy", "")
}

policy_input(component, kv) = {
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
        "command": [kv],
        "image": "k8s.gcr.io/kube-apiserver:v1.18.3",
        "imagePullPolicy": "IfNotPresent",
        "name": "kube-apiserver"
      }
    ]
  }
}
