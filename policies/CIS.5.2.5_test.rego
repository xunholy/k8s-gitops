package cis_5_2_5

import data.lib.test

test_violation {
	test.violations(violation) with input as policy_input(true)
}

test_no_violation {
	test.no_violations(violation) with input as policy_input(false)
}

policy_input(allowPrivilegeEscalation) = {
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
          "allowPrivilegeEscalation": allowPrivilegeEscalation,
          "capabilities": {
            "add": [],
            "drop": []
          }
        }
      }
    ]
  }
}
