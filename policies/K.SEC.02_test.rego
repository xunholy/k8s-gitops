package containers_resources_limits_memory

import data.lib.test

test_violation {
	test.violations(violation) with input as policy_input({})
}

test_no_violation {
	test.no_violations(violation) with input as policy_input({"memory": "128Mi","cpu": "500m"})
}

policy_input(limits) = {
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
        "resources": {
          "requests": {
            "memory": "64Mi",
            "cpu": "250m"
          },
          "limits": limits
        }
      }
    ]
  }
}
