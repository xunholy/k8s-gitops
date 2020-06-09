package cis_1_2_1

import data.lib.test

test_violation {
  test.violations(violation) with input as policy_input("")
}

test_violation {
  test.violations(violation) with input as policy_input("--anonymous-auth=true")
}

test_no_violation {
  test.no_violations(violation) with input as policy_input("--anonymous-auth=false")
}

policy_input(kv) = {
  "apiVersion": "v1",
  "kind": "Pod",
  "metadata": {
    "name": "kube-apiserver",
    "namespace": "kube-system"
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
