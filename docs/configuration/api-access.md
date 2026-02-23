# Access Kubernetes API through Cloudflare

![Remote Kubectl Access](https://github.com/raspbernetes/raspbernetes.github.io/raw/master/img/kubectl.png 'Remote Kubectl Access')

## Prerequisites

Before proceeding, make sure you have the following prerequisites in place to set up your kubeconfig locally.

### Setup Kubeconfig Locally

To configure the kubeconfig correctly, follow the steps below:

1. Go to https://login.owncloud.ai in your web browser.

2. Once authenticated, the webpage will provide command prompts specific to your account. These commands need to be set up on the client machine that you will be using with kubectl to run commands.

**Note**: *The commands provided will configure the necessary authentication and context for your kubeconfig, enabling seamless interaction with the Kubernetes cluster.*

By following these steps, you will set up the kubeconfig correctly on your local machine, allowing you to access and manage the Kubernetes cluster using kubectl commands.

## Connect From a Client Machine

To enable remote kubectl access to the Kubernetes cluster, follow the instructions below.

**Note**: *These instructions are applicable if you are part of the Raspbernetes project and have been granted access by an admin.*

### 1. Install Cloudflared on the Client Machine

Download and install cloudflared on the client machine that will connect to the Kubernetes cluster. You can find the installation instructions [HERE](https://developers.cloudflare.com/cloudflare-one/connections/connect-apps/install-and-setup/installation/).

*`Cloudflared` will need to be installed on each user device that will connect to the kube-apiserver.*

### 2. Establish Connection

Run the following command to create a connection from the device to Cloudflare. Any available port can be specified.

```bash
$ cloudflared access tcp --hostname api.owncloud.ai --url 127.0.0.1:1234
```

With this service running, you can run a `kubectl` command and `cloudflared` will launch a browser window and prompt the user to authenticate with the Github SSO provider. Once authenticated, `cloudflared` will expose the connection to the client machine at the local URL specified in the command.

`kubeconfig` does not support proxy command configurations at this time, though the community has submitted plans to do so. In the interim, users can alias the cluster's API server to save time.

```bash
$ alias kubeone="env HTTPS_PROXY=socks5://127.0.0.1:1234 kubectl"
```

### 3. Test Connection

To test the connection, use the alias and run a kubectl command. For example:

```bash
kubeone get nodes
```

If the connection is successful, you should see the appropriate information about the cluster's nodes.

Example result:

```bash
NAME            STATUS   ROLES            AGE   VERSION
k8s-master-01   Ready    control-plane    8h    v1.26.1
k8s-master-02   Ready    control-plane    8h    v1.26.1
k8s-master-03   Ready    control-plane    8h    v1.26.1
k8s-worker-01   Ready    <none>           8h    v1.26.1
```

You now have complete access to the cluster using the set alias. Please ensure that the cluster has RBAC enabled and that you have been granted the necessary user permissions by an admin.

---
**Note**: *Official documentation can also be referenced [HERE](https://developers.cloudflare.com/cloudflare-one/tutorials/kubectl/)*
