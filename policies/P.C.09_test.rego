package spec_hostaliases

import data.lib.test

test_violation {
	test.violations(violation) with input as policy_input({"hostAliases": []})
}

test_no_violation {
	test.no_violations(violation) with input as policy_input({})
}

policy_input(hostAliases) = {
  "apiVersion": "v1",
  "kind": "Pod",
  "metadata": {
    "name": "example"
  },
  "spec": hostAliases
}
