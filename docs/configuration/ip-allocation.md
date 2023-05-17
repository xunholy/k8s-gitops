# IP Allocation

The IP Allocation section provides an overview of the allocated IP addresses for different applications, nodes, and external devices within my environment.

## Kubernetes Node(s)

The Kubernetes Node(s) table displays the IP addresses assigned to each node in the cluster:

| Node/Instance       | Type          |      IP/CIDR      |
| ------------------- | ------------- | :---------------: |
| Control Plane       | VIP           | 192.168.50.200/32 |
| Protectli FW2B 01   | Control Plane | 192.168.50.111/32 |
| Protectli FW2B 02   | Control Plane | 192.168.50.112/32 |
| Protectli FW2B 03   | Control Plane | 192.168.50.113/32 |
| Protectli VP2410 01 | Node          | 192.168.50.114/32 |
| Protectli VP2410 02 | Node          | 192.168.50.115/32 |
| Protectli VP2410 03 | Node          | 192.168.50.116/32 |
| Raspberry Pi 01     | Node          | 192.168.50.121/32 |
| Raspberry Pi 02     | Node          | 192.168.50.122/32 |
| Raspberry Pi 03     | Node          | 192.168.50.123/32 |
| Raspberry Pi 04     | Node          | 192.168.50.124/32 |
| Rock Pi 01          | Node          | 192.168.50.131/32 |
| Rock Pi 02          | Node          | 192.168.50.132/32 |
| Rock Pi 03          | Node          | 192.168.50.133/32 |


## Kubernetes Application(s)

The Kubernetes Application(s) table presents the IP addresses allocated to different applications in the Kubernetes cluster:

| Application/Node | Type         |              IP/CIDR              |
| ---------------- | ------------ | :-------------------------------: |
| Metallb          | Daemonset    | 192.168.50.150 <-> 192.168.50.155 |
| Istio Ingress    | LoadBalancer |         192.168.50.180/32         |
| Coredns          | LoadBalancer |         192.168.50.181/32         |
| Mosquitto        | LoadBalancer |         192.168.50.182/32         |
| Zigbee2mqtt      | LoadBalancer |         192.168.50.183/32         |
| Nginx Ingress    | LoadBalancer |         192.168.50.180/32         |
| K8s Gateway      | LoadBalancer |         192.168.50.188/32         |
| Blocky           | LoadBalancer |         192.168.50.191/32         |

## External Device(s)

The External Device(s) table lists IP addresses assigned to devices outside the Kubernetes cluster:

| Application            | Type |      IP/CIDR      |
| ---------------------- | ---- | :---------------: |
| Zigbee Controller      | N/A  | 192.168.50.165/32 |
| Ender 5 Pro 3D Printer | N/A  |    x.x.x.x/32     |
