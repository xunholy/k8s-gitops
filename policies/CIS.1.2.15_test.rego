package cis_1_2_15

import data.lib.test

test_violation {
    test.violations(violation) with input as policy_input("kube-apiserver", "--disable-admission-plugins=NamespaceLifecycle")
}

test_no_violation {
    test.no_violations(violation) with input as policy_input("kube-apiserver", "--disable-admission-plugins=")
}

test_no_violation_02 {
    test.no_violations(violation) with input as policy_input("kube-proxy", "--disable-admission-plugins=NamespaceLifecycle")
}

policy_input(component, kv) = {
  "apiVersion": "v1",
  "kind": "Pod",
  "metadata": {
    "name": "kube-apiserver",
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
          "kube-apiserver",
          kv
        ],
        "image": "k8s.gcr.io/kube-apiserver:v1.18.3",
        "imagePullPolicy": "IfNotPresent",
        "name": "kube-apiserver"
      }
    ]
  }
}
