package containers_image_tag

import data.lib.test

test_violation_1 {
	test.violations(violation) with input as policy_input("example")
}

test_violation_2 {
	test.violations(violation) with input as policy_input("example:latest")
}

test_no_violation {
	test.no_violations(violation) with input as policy_input("example:123")
}

policy_input(image) = {
  "apiVersion": "v1",
  "kind": "deployment",
  "metadata": {
    "name": "example"
  },
  "spec": {
    "containers": [
      {
        "image": image,
        "name": "example"
      }
    ]
  }
}
