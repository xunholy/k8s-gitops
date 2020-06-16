package cis_2_7

import data.lib.test

test_violation {
    test.violations(violation) with input as policy_input("kube-apiserver", "--client-ca-file=/run/config/pki/ca.crt", "--etcd-cafile=/run/config/pki/ca.crt")
}

test_no_violation {
    test.no_violations(violation) with input as policy_input("kube-apiserver", "--client-ca-file=/run/config/pki/ca.crt", "--etcd-cafile=/run/config/pki/etcd/ca.crt")
}

test_no_violation_02 {
    test.no_violations(violation) with input as policy_input("kube-proxy", "--client-ca-file=/run/config/pki/ca.crt", "--etcd-cafile=/run/config/pki/ca.crt")
}

policy_input(component, kv, kv2) = {
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
          kv,
          kv2
        ],
        "image": "k8s.gcr.io/etcd:v1.18.3",
        "imagePullPolicy": "IfNotPresent",
        "name": "etcd"
      }
    ]
  }
}
