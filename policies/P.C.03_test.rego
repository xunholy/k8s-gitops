package containers_securitycontext_capabilities_add_index_sys_admim

import data.lib.test

test_violation {
	test.violations(violation) with input as policy_input("CAP_SYS_ADMIN")
}

test_no_violation {
	test.no_violations(violation) with input as policy_input({""})
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
            "add": [
              capability
            ],
            "drop": [
              "ALL"
            ]
          }
        }
      }
    ]
  }
}
