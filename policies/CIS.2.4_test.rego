package cis_2_4

import data.lib.test

test_violation {
    test.violations(violation) with input as policy_input("etcd", "--peer-cert-file=/tmp/server.crt, --peer-key-file=/tmp/server.key")
}

test_violation_2 {
    test.violations(violation) with input as policy_input("etcd", "--peer-cert-file=/etc/kubernetes/pki/etcd/server.crt, --peer-key-file=/tmp/server.key")
}

test_violation_3 {
    test.violations(violation) with input as policy_input("etcd", "--cert-file=/tmp/server.crt, --peer-key-file=/etc/kubernetes/pki/etcd/server.key")
}

test_no_violation {
    test.no_violations(violation) with input as policy_input("etcd", "--peer-cert-file=/etc/kubernetes/pki/etcd/peer.crt, --peer-key-file=/etc/kubernetes/pki/etcd/peer.key")
}

test_no_violation {
    test.no_violations(violation) with input as policy_input("kube-proxy", "--peer-cert-file=/tmp/server.crt, --peer-key-file=/tmp/server.key")
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
