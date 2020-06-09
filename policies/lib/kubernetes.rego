package lib.kubernetes

default name = ""

name = object.metadata.name

kind = object.kind

metadata = object.metadata

default namespace = ""

namespace = metadata.namespace

default username = ""

username = input.review.userInfo.username

default operation = ""

operation = input.review.operation

default parameters = {}

parameters = input.parameters {
  is_input_parameterised
}

is_input_parameterised {
    count(input.parameters) > 0
}

default is_gatekeeper = false

is_gatekeeper {
	has_field(input, "review")
	has_field(input.review, "object")
}

has_field(obj, field) {
	obj[field]
}

object = input {
	not is_gatekeeper
}

object = input.review.object {
	is_gatekeeper
}

format(msg) = gatekeeper_format {
	is_gatekeeper
	gatekeeper_format = {"msg": msg}
}

format(msg) = msg {
	not is_gatekeeper
}

is_service {
	kind = "Service"
}

is_service {
	kind = "Services"
}

services[service] {
  is_service
  service = object
}

is_deployment {
	kind = "Deployment"
}

is_deployment {
	kind = "Deployments"
}

is_pod {
	kind = "Pod"
}

is_pod {
	kind = "Pods"
}

pods[pod] {
	is_deployment
	pod = object.spec.template
}

pods[pod] {
	is_pod
	pod = object
}

is_service_account {
  kind = "ServiceAccount"
}

is_service_account {
  kind = "ServiceAccounts"
}

serviceaccounts[serviceaccount] {
  is_service_account
  serviceaccount = object
}

is_namespace {
  kind = "Namespace"
}

is_namespace {
  kind = "Namespaces"
}

namespaces[namespaceObj] {
  is_namespace
  namespaceObj = object
}

is_rolebinding {
  kind = "RoleBinding"
}

is_rolebinding {
  kind = "RoleBindings"
}

rolebindings[rolebinding] {
  is_rolebinding
  rolebinding = object
}

is_clusterrole {
  kind = "ClusterRole"
}

is_clusterrole {
  kind = "ClusterRoles"
}

clusterroles[clusterrole] {
    is_clusterrole
    clusterrole = object
}

pod_containers(pod) = all_containers {
	keys = {"containers", "initContainers"}
	all_containers = [c | keys[k]; c = pod.spec[k][_]]
}

containers[container] {
	pods[pod]
	all_containers = pod_containers(pod)
	container = all_containers[_]
}

containers[container] {
	all_containers = pod_containers(object)
	container = all_containers[_]
}

volumes[volume] {
	pods[pod]
	volume = pod.spec.volumes[_]
}

#############
# Functions #
#############

flag_contains_string(obj, key, value ) {
  elems := [elem | contains(obj.command[i], key); elem := obj.command[i]]
  contains(elems[_], value)
}
