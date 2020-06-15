package spec_volumes_hostpath_path_var_run_docker_sock

import data.lib.kubernetes

# https://kubesec.io/basics/spec-volumes-hostpath-path-var-run-docker-sock/
violation[msg] {
	kubernetes.volumes[volume]
	volume.hostPath.path == "/var/run/docker.sock"
	msg = kubernetes.format(sprintf("The %s %s is mounting the Docker socket", [kubernetes.kind, kubernetes.name]))
}
