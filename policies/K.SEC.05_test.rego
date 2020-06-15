package containers_securitycontext_privileged_true

import data.lib.test

test_violation {
	test.violations(violation) with input as policy_input(true)
}

test_no_violation {
	test.no_violations(violation) with input as policy_input(false)
}

policy_input(privileged) = {
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
          "privileged": privileged,
          "allowPrivilegeEscalation": true,
          "capabilities": {
            "add": [],
            "drop": []
          }
        }
      }
    ]
  }
}
