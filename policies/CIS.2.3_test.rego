package cis_2_3

import data.lib.test

test_violation {
    test.violations(violation) with input as policy_input("etcd", "--auto-tls=true")
}

test_no_violation {
    test.no_violations(violation) with input as policy_input("etcd", "--auto-tls=false")
}

test_no_violation_02 {
    test.no_violations(violation) with input as policy_input("kube-proxy", "--auto-tls=true")
}

policy_input(component, kv) = {
  "apiVersion": "v1",
  "kind": "Pod",
  "metadata": {
    "name": "etcd",
    "namespace": "kube-system",
    "labels": {
      "component": component,
      "tier": "control-plane"
    }
  },
  "spec": {
    "containers": [
      {
        "command": [
          "etcd",
          kv
        ],
        "image": "k8s.gcr.io/etcd:v1.18.3",
        "imagePullPolicy": "IfNotPresent",
        "name": "etcd"
      }
    ]
  }
}
