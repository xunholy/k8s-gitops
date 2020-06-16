package cis_5_2_3

import data.lib.test

test_violation {
	test.violations(violation) with input as policy_input(true)
}

test_no_violation {
	test.no_violations(violation) with input as policy_input(false)
}

policy_input(hostIPC) = {
  "apiVersion": "v1",
  "kind": "Pod",
  "metadata": {
    "name": "example"
  },
  "spec": {
		"hostIPC": hostIPC
	}
}
