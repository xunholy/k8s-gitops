# Production

| Application/Node          | Type               |             IP/CIDR             |
| ------------------------- | ------------------ | :-----------------------------: |
| keepalived                | VIP                |          192.168.1.200          |
| k8s-controlplane-01       | Control-Plane Node |          192.168.1.121          |
| k8s-controlplane-02       | Control-Plane Node |          192.168.1.122          |
| k8s-controlplane-03       | Control-Plane Node |          192.168.1.123          |
| k8s-node-01               | Node               |          192.168.1.131          |
| k8s-node-01               | Node               |          192.168.1.132          |
| k8s-node-01               | Node               |          192.168.1.133          |
| metallb                   | Daemonset          | 192.168.1.150 <-> 192.168.1.155 |
| istio                     | LoadBalancer       |          192.168.1.150          |
| coredns                   | LoadBalancer       |          192.168.1.151          |
| mosquitto                 | LoadBalancer       |          192.168.1.152          |
| zigbee2mqtt               | LoadBalancer       |          192.168.1.153          |
| zigbee2mqtt (code-server) | LoadBalancer       |          192.168.1.154          |

# Staging

| Application               | Type               |             IP/CIDR             |
| ------------------------- | ------------------ | :-----------------------------: |
| keepalived                | VIP                |          192.168.1.201          |
| k8s-controlplane-01       | Control-Plane Node |          192.168.1.111          |
| k8s-controlplane-02       | Control-Plane Node |          192.168.1.112          |
| k8s-controlplane-03       | Control-Plane Node |          192.168.1.113          |
| k8s-node-01               | Node               |          192.168.1.114          |
| k8s-node-01               | Node               |          192.168.1.115          |
| k8s-node-01               | Node               |          192.168.1.116          |
| metallb                   | Daemonset          | 192.168.1.160 <-> 192.168.1.165 |
| istio                     | LoadBalancer       |          192.168.1.160          |
| coredns                   | LoadBalancer       |          192.168.1.161          |
| mosquitto                 | LoadBalancer       |          192.168.1.162          |
| zigbee2mqtt               | LoadBalancer       |          192.168.1.163          |
| zigbee2mqtt (code-server) | LoadBalancer       |          192.168.1.164          |
