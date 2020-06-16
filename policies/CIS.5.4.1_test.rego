package cis_5_4_1

import data.lib.test

test_violation {
    test.violations(violation) with input as policy_input({"secretKeyRef": {"name": "example","key": "example"}})
}

test_no_violation {
    test.no_violations(violation) with input as policy_input({"valueFrom": {"name": "example","key": "example"}})
}

policy_input(valueFrom) = {
  "apiVersion": "apps/v1",
  "kind": "Deployment",
  "metadata": {
    "name": "hello"
  },
  "spec": {
    "template": {
      "spec": {
        "containers": [
          {
            "name": "hello",
            "image": "example:latest",
            "imagePullPolicy": "Always",
            "env": [
              {
                "name": "NODE_NAME",
                "valueFrom": valueFrom
              }
            ]
          }
        ]
      }
    }
  }
}
