package maicontainers_securitycontext_runasuser

import data.lib.test

test_violation {
	test.violations(violation) with input as policy_input(0)
}

test_no_violation {
	test.no_violations(violation) with input as policy_input(10000)
}

policy_input(runAsUser) = {
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
					"runAsNonRoot": false,
					"runAsUser": runAsUser,
          "readOnlyRootFilesystem": true,
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
