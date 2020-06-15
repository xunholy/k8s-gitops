# Security Policies

| ID | Description | URL |
|:--:| ----------- |:---:|
| K.SEC.01 | Enforcing CPU limits prevents DOS via resource exhaustion |[Link](https://kubesec.io/basics/containers-resources-limits-cpu/)|
|  K.SEC.02 | Enforcing memory limits prevents DOS via resource exhaustion | [Link](https://kubesec.io/basics/containers-resources-limits-memory) |
| K.SEC.03 | CAP_SYS_ADMIN is the most privileged capability and should always be avoided | [Link](https://kubesec.io/basics/containers-securitycontext-capabilities-add-index-sys-admin/) |
| K.SEC.04 | Drop all capabilities and add only those required to reduce syscall attack surface | [Link](https://kubesec.io/basics/containers-securitycontext-capabilities-drop-index-all/) |
| K.SEC.05 | Privileged containers can allow almost completely unrestricted host access | [Link](https://kubesec.io/basics/containers-securitycontext-privileged-true/) |
| K.SEC.06 | An immutable root filesystem can prevent malicious binaries being added to PATH and increase attack cost | [Link](https://kubesec.io/basics/containers-securitycontext-readonlyrootfilesystem-true/) |
| K.SEC.07 | Force the running image to run as a non-root user to ensure least privilege | [Link](https://kubesec.io/basics/containers-securitycontext-runasnonroot-true/) |
| K.SEC.08 | Run as a high-UID user to avoid conflicts with the host’s user table | [Link](https://kubesec.io/basics/containers-securitycontext-runasuser/) |
| K.SEC.09 | Managing /etc/hosts aliases can prevent Docker from modifying the file after a pod’s containers have already been started | [Link](https://kubesec.io/basics/spec-hostaliases/) |
| K.SEC.10 | Sharing the host’s IPC namespace allows container processes to communicate with processes on the host | [Link](https://kubesec.io/basics/spec-hostipc/) |
| K.SEC.11 | Sharing the host’s network namespace permits processes in the pod to communicate with processes bound to the host’s loopback adapter | [Link](https://kubesec.io/basics/spec-hostnetwork/) |
| K.SEC.12 | Sharing the host’s PID namespace allows visibility of processes on the host, potentially leaking information such as environment variables and configuration | [Link](https://kubesec.io/basics/spec-hostpid/) |
| K.SEC.13 | Mounting the docker.socket leaks information about other containers and can allow container breakout | [Link](https://kubesec.io/basics/spec-volumes-hostpath-path-var-run-docker-sock/) |
| K.SEC.14 | Avoid using the :latest tag when deploying containers in production as it is harder to track which version of the image is running and more difficult to roll back properly. | [Link](https://kubernetes.io/docs/concepts/configuration/overview/#container-images) |
| K.SEC.15 | Disabling allowPrivilegeEscalation to false ensures that no child process of a container can gain more privileges than its parent. | [Link](https://kubernetes.io/docs/concepts/policy/pod-security-policy/#privilege-escalation) |
