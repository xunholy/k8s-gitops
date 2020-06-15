package cis_2_7

import data.lib.kubernetes

default_parameters = {
    "key": "--client-ca-file", #/etc/kubernetes/pki/ca.crt
    "key2": "--etcd-cafile", # /etc/kubernetes/pki/etcd/ca.crt
}

params = object.union(default_parameters, kubernetes.parameters)

# violation[msg] {
#     kubernetes.apiserver[container]
#     validate(container.command)
#     msg = kubernetes.format(sprintf("%s in the %s %s does not have %s %s", [container.name, kubernetes.kind, kubernetes.name, params.key, params.requiredValue]))
# }

violation[msg] {
    kubernetes.apiserver[container]
    array := container.command
    apiserver := [elem | contains(array[i], params.key); elem := array[i]]
    etcd := [elem | contains(array[i], params.key); elem := array[i]]
    [_, apiKey] := split(apiserver[_], "=")
    [_, etcdKey] := split(etcd[_], "=")
    apiKey == etcdKey
    msg = sprintf("Error: %s %s",[apiKey, etcdKey])
}


# flag_contains_string(array, key, value) {
#     elems := [elem | contains(array[i], key); elem := array[i]]
#     pattern := sprintf("%v=|,", [key])
#     v = { l | l := regex.split(pattern, elems[i])[_] }
#     v[value]
# }
