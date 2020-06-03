package containers_securitycontext_readonlyrootfilesystem_true

import data.lib.test

test_violation {
	test.violations(violation) with input as policy_input(false)
}

test_no_violation {
	test.no_violations(violation) with input as policy_input(true)
}

policy_input(readOnlyRootFilesystem) = {
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
          "readOnlyRootFilesystem": readOnlyRootFilesystem,
          "privileged": false,
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
