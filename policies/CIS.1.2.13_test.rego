package cis_1_2_13

import data.lib.test

test_violation {
	test.violations(violation) with input as policy_input("--enable-admission-plugins=NodeRestriction")
}

test_no_violation {
	test.no_violations(violation) with input as policy_input("--enable-admission-plugins=SecurityContextDeny")
}

policy_input(kv) = {
  "apiVersion": "v1",
  "kind": "Pod",
  "metadata": {
    "name": "kube-apiserver-k8s-master-01",
    "namespace": "kube-system"
  },
  "spec": {
    "containers": [
      {
        "command": [
          "kube-apiserver",
          "--advertise-address=192.168.1.121",
          "--allow-privileged=true",
          "--authorization-mode=Node,RBAC",
          "--client-ca-file=/etc/kubernetes/pki/ca.crt",
          "--enable-bootstrap-token-auth=true",
          "--etcd-cafile=/etc/kubernetes/pki/etcd/ca.crt",
          "--etcd-certfile=/etc/kubernetes/pki/apiserver-etcd-client.crt",
          "--etcd-keyfile=/etc/kubernetes/pki/apiserver-etcd-client.key",
          "--etcd-servers=https://127.0.0.1:2379",
          "--insecure-port=0",
          "--kubelet-client-certificate=/etc/kubernetes/pki/apiserver-kubelet-client.crt",
          "--kubelet-client-key=/etc/kubernetes/pki/apiserver-kubelet-client.key",
          "--kubelet-preferred-address-types=InternalIP,ExternalIP,Hostname",
          "--proxy-client-cert-file=/etc/kubernetes/pki/front-proxy-client.crt",
          "--proxy-client-key-file=/etc/kubernetes/pki/front-proxy-client.key",
          "--requestheader-allowed-names=front-proxy-client",
          "--requestheader-client-ca-file=/etc/kubernetes/pki/front-proxy-ca.crt",
          "--requestheader-extra-headers-prefix=X-Remote-Extra-",
          "--requestheader-group-headers=X-Remote-Group",
          "--requestheader-username-headers=X-Remote-User",
          "--secure-port=6443",
          "--service-account-key-file=/etc/kubernetes/pki/sa.pub",
          "--service-cluster-ip-range=10.96.0.0/12",
          "--tls-cert-file=/etc/kubernetes/pki/apiserver.crt",
          "--tls-private-key-file=/etc/kubernetes/pki/apiserver.key",
          kv
        ],
        "image": "k8s.gcr.io/kube-apiserver:v1.18.3",
        "imagePullPolicy": "IfNotPresent",
        "name": "kube-apiserver"
      }
    ]
  }
}

