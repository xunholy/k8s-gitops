package spec_volumes_hostpath_path_var_run_docker_sock

import data.lib.test

test_violation {
	test.violations(violation) with input as policy_input("/var/run/docker.sock")
}

test_no_violation {
	test.no_violations(violation) with input as policy_input("")
}

policy_input(path) = {
  "apiVersion": "v1",
  "kind": "Pod",
  "metadata": {
    "name": "example"
  },
  "spec": {
    "containers": [
      {
        "image": "example:123",
        "name": "example",
        "volumeMounts": [
          {
            "mountPath": path,
            "name": "example-volume"
          }
        ]
      }
    ],
    "volumes": [
      {
        "name": "example-volume",
        "hostPath": {
          "path": path,
          "type": "File"
        }
      }
    ]
  }
}
