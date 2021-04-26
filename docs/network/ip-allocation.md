# IP Allocation

!!! note "Work in progress"
    This document is a work in progress.

## Production

| Application/Node          | Type               |             IP/CIDR             |
| ------------------------- | ------------------ | :-----------------------------: |
| keepalived                | VIP                |        192.168.1.200/32         |
| k8s-controlplane-01       | Control-Plane Node |        192.168.1.121/32         |
| k8s-controlplane-02       | Control-Plane Node |        192.168.1.122/32         |
| k8s-controlplane-03       | Control-Plane Node |        192.168.1.123/32         |
| k8s-node-01               | Node               |        192.168.1.131/32         |
| k8s-node-01               | Node               |        192.168.1.132/32         |
| k8s-node-01               | Node               |        192.168.1.133/32         |
| metallb                   | Daemonset          | 192.168.1.150 <-> 192.168.1.155 |
| istio                     | LoadBalancer       |        192.168.1.150/32         |
| coredns                   | LoadBalancer       |        192.168.1.151/32         |
| mosquitto                 | LoadBalancer       |        192.168.1.152/32         |
| zigbee2mqtt               | LoadBalancer       |        192.168.1.153/32         |
| zigbee2mqtt (code-server) | LoadBalancer       |        192.168.1.154/32         |

## Staging

| Application         | Type               |             IP/CIDR             |
| ------------------- | ------------------ | :-----------------------------: |
| keepalived          | VIP                |        192.168.1.205/32         |
| k8s-controlplane-01 | Control-Plane Node |        192.168.1.161/32         |
| k8s-controlplane-02 | Control-Plane Node |        192.168.1.162/32         |
| k8s-controlplane-03 | Control-Plane Node |        192.168.1.163/32         |
| k8s-node-01         | Node               |        192.168.1.171/32         |
| k8s-node-02         | Node               |        192.168.1.172/32         |
| k8s-node-03         | Node               |        192.168.1.173/32         |
| metallb             | Daemonset          | 192.168.1.165 <-> 192.168.1.170 |
| istio               | LoadBalancer       |        192.168.1.165/32         |
| coredns             | LoadBalancer       |        192.168.1.166/32         |
