package cis_1_2_11

import data.lib.test

test_violation {
    test.violations(violation) with input as policy_input("kube-apiserver", "--enable-admission-plugins=AlwaysAdmit")
}

test_no_violation {
    test.no_violations(violation) with input as policy_input("kube-apiserver", "--enable-admission-plugins=NodeRestriction")
}

test_no_violation_2 {
    test.no_violations(violation) with input as policy_input("kube-apiserver", "")
}

test_no_violation_3 {
    test.no_violations(violation) with input as policy_input("kube-proxy", "--enable-admission-plugins=AlwaysAdmit")
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
