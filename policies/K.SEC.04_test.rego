package containers_securitycontext_capabilities_drop_index_all

import data.lib.test

test_violation {
	test.violations(violation) with input as policy_input("")
}

test_no_violation {
	test.no_violations(violation) with input as policy_input("ALL")
}

policy_input(capability) = {
  "apiVersion": "v1",
  "kind": "deployment",
  "metadata": {
    "name": "example"
  },
  "spec": {
    "containers": [
      {
        "name": "example",
        "image": "example:123",
        "securityContext": {
          "readOnlyRootFilesystem": true,
          "privileged": false,
          "allowPrivilegeEscalation": true,
          "capabilities": {
            "add": [],
            "drop": [
              capability
            ]
          }
        }
      }
    ]
  }
}
