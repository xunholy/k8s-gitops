package cis_1_2_1

import data.lib.kubernetes

default_parameters = {
  "key": "--anonymous-auth",
  "requiredValue": "false"
}

params = object.union(default_parameters, kubernetes.parameters)

# Below is checking if the pod has command field, might need to enable we check this only for the api server,
# Otherwise will fail all the pods that don't have command field
# violation[msg] {
#   kubernetes.containers[container]
#   not kubernetes.has_field(container,"command")
#   msg = kubernetes.format(sprintf("%s in the %s %s does not have --anonymous-auth flag in the command field", [container.name, kubernetes.kind, kubernetes.name]))
# }

violation[msg] {
  kubernetes.containers[container]
  not kubernetes.contains_element(container.command, params.requiredValue)
  msg = kubernetes.format(sprintf("%s in the %s %s does not have --anonymous-auth flag", [container.name, kubernetes.kind, kubernetes.name]))
}

violation[msg] {
  kubernetes.containers[container]
  not kubernetes.flag_contains_string(container.command, params.key, params.requiredValue)
  msg = kubernetes.format(sprintf("%s in the %s %s should set --anonymous-auth to false", [container.name, kubernetes.kind, kubernetes.name]))
}
