#!/bin/bash
# Copyright 2020 The OpenEBS Authors. All rights reserved.
#
# Licensed under the Apache License, Version 2.0 (the "License");
# you may not use this file except in compliance with the License.
# You may obtain a copy of the License at
#
#     http://www.apache.org/licenses/LICENSE-2.0
#
# Unless required by applicable law or agreed to in writing, software
# distributed under the License is distributed on an "AS IS" BASIS,
# WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
# See the License for the specific language governing permissions and
# limitations under the License.


# uninstall OpenEBS Helm chart if openebs was installed using helm chart
# helm delete openebs -n openebs

# SOURCE: https://github.com/openebs/charts/blob/gh-pages/scripts/uninstall/uninstall.sh

# cleanup PVC
echo "Cleaning PVC"

STATUS=$(kubectl get pvc -n openebs -o name | wc -l)

if [ "$STATUS" -gt 0 ]; then
	kubectl delete pvc -n openebs --all --wait=false
	kubectl patch -n openebs -p '{"metadata":{"finalizers":null}}' --type=merge $(kubectl get pvc -n openebs -o name)
fi


# cleanup SPC
echo "Cleaning SPC"
STATUS=$(kubectl get spc -o name | wc -l)

if [ "$STATUS" -gt 0 ]; then
	kubectl delete spc --all --wait=false
	kubectl patch -p '{"metadata":{"finalizers":null}}' --type=merge $(kubectl get spc -o name)
fi

# cleanup BDC
echo "Cleaning BDC"
STATUS=$(kubectl get bdc -n openebs -o name | wc -l)

if [ "$STATUS" -gt 0 ]; then
	kubectl delete bdc -n openebs --all --wait=false
	kubectl patch -n openebs -p '{"metadata":{"finalizers":null}}' --type=merge $(kubectl get bdc -n openebs -o name)
fi

# cleanup CV
echo "Cleaning CV"
STATUS=$(kubectl get cv -n openebs -o name | wc -l)

if [ "$STATUS" -gt 0 ]; then
	kubectl delete cv -n openebs --all --wait=false
	kubectl patch -n openebs -p '{"metadata":{"finalizers":null}}' --type=merge $(kubectl get cv -n openebs -o name)
fi

# cleanup CVC
echo "Cleaning CVC"
STATUS=$(kubectl get cvc -n openebs -o name | wc -l)

if [ "$STATUS" -gt 0 ]; then
	kubectl delete cvc -n openebs --all --wait=false
	kubectl patch -n openebs -p '{"metadata":{"finalizers":null}}' --type=merge $(kubectl get cvc -n openebs -o name)
fi

# cleanup CVR
echo "Cleaning CVR"
STATUS=$(kubectl get cvr -n openebs -o name | wc -l)

if [ "$STATUS" -gt 0 ]; then
	kubectl delete cvr -n openebs --all --wait=false
	kubectl patch -n openebs -p '{"metadata":{"finalizers":null}}' --type=merge $(kubectl get cvr -n openebs -o name)
fi

# cleanup CSTORVOLUME
echo "Cleaning CSTORVOLUME"
STATUS=$(kubectl get cstorvolume -n openebs -o name | wc -l)

if [ "$STATUS" -gt 0 ]; then
	kubectl delete cstorvolume -n openebs --all --wait=false
	kubectl patch -n openebs -p '{"metadata":{"finalizers":null}}' --type=merge $(kubectl get cstorvolume  -n openebs -o name)
fi

# cleanup cbackup
echo "Cleaning cbackup"
STATUS=$(kubectl get cbackup -n openebs -o name | wc -l)

if [ "$STATUS" -gt 0 ]; then
	kubectl delete cbackup -n openebs --all --wait=false
	kubectl patch -n openebs -p '{"metadata":{"finalizers":null}}' --type=merge $(kubectl get cbackup -n openebs -o name)
fi

# cleanup ccompletedbackup
echo "Cleaning ccompletedbackup"
STATUS=$(kubectl get ccompletedbackup -n openebs -o name | wc -l)

if [ "$STATUS" -gt 0 ]; then
	kubectl delete ccompletedbackup -n openebs --all --wait=false
	kubectl patch -n openebs -p '{"metadata":{"finalizers":null}}' --type=merge $(kubectl get ccompletedbackup -n openebs -o name)
fi

# cleanup crestore
echo "Cleaning crestore"
STATUS=$(kubectl get crestore -n openebs -o name | wc -l)

if [ "$STATUS" -gt 0 ]; then
	kubectl delete crestore -n openebs --all --wait=false
	kubectl patch -n openebs -p '{"metadata":{"finalizers":null}}' --type=merge $(kubectl get crestore -n openebs -o name)
fi

# cleanup csp
echo "Cleaning csp"
STATUS=$(kubectl get csp -n openebs -o name | wc -l)

if [ "$STATUS" -gt 0 ]; then
	kubectl delete csp -n openebs --all --wait=false
	kubectl patch -n openebs -p '{"metadata":{"finalizers":null}}' --type=merge $(kubectl get csp -n openebs -o name)
fi

# cleanup CSPC
echo "Cleaning CSPC"
STATUS=$(kubectl get cspc -n openebs -o name | wc -l)

if [ "$STATUS" -gt 0 ]; then
	kubectl delete cspc -n openebs --all --wait=false
	kubectl patch -n openebs -p '{"metadata":{"finalizers":null}}' --type=merge $(kubectl get cspc -n openebs -o name)
fi

# cleanup CSPI
echo "Cleaning CSPI"
STATUS=$(kubectl get cspi -n openebs -o name | wc -l)

if [ "$STATUS" -gt 0 ]; then
	kubectl delete cspi -n openebs --all --wait=false
	kubectl patch -n openebs -p '{"metadata":{"finalizers":null}}' --type=merge $(kubectl get cspi -n openebs -o name)
fi

# cleanup BD
echo "Cleaning BD"
STATUS=$(kubectl get bd -n openebs -o name | wc -l)

if [ "$STATUS" -gt 0 ]; then
	kubectl delete bd -n openebs --all --wait=false
	kubectl patch -n openebs -p '{"metadata":{"finalizers":null}}' --type=merge $(kubectl get bd -n openebs -o name)
fi


# delete CRD

kubectl delete crd castemplates.openebs.io --wait=false
kubectl delete crd cstorpools.openebs.io --wait=false
kubectl delete crd cstorpoolinstances.openebs.io --wait=false
kubectl delete crd cstorvolumeclaims.openebs.io --wait=false
kubectl delete crd cstorvolumereplicas.openebs.io --wait=false
kubectl delete crd cstorvolumepolicies.openebs.io --wait=false
kubectl delete crd cstorvolumes.openebs.io --wait=false
kubectl delete crd runtasks.openebs.io --wait=false
kubectl delete crd storagepoolclaims.openebs.io --wait=false
kubectl delete crd storagepools.openebs.io --wait=false
kubectl delete crd volumesnapshotdatas.volumesnapshot.external-storage.k8s.io --wait=false
kubectl delete crd volumesnapshots.volumesnapshot.external-storage.k8s.io --wait=false
kubectl delete crd blockdevices.openebs.io --wait=false
kubectl delete crd blockdeviceclaims.openebs.io --wait=false
kubectl delete crd cstorbackups.openebs.io --wait=false
kubectl delete crd cstorbackups.cstor.openebs.io --wait=false
kubectl delete crd cstorrestores.openebs.io --wait=false
kubectl delete crd cstorcompletedbackups.openebs.io --wait=false
kubectl delete crd upgradetasks.openebs.io --wait=false
kubectl delete crd cstorpoolclusters.cstor.openebs.io --wait=false
kubectl delete crd cstorpoolinstances.cstor.openebs.io --wait=false
kubectl delete crd cstorvolumeattachments.cstor.openebs.io --wait=false
kubectl delete crd cstorvolumeconfigs.cstor.openebs.io --wait=false
kubectl delete crd cstorvolumepolicies.cstor.openebs.io --wait=false
kubectl delete crd cstorvolumereplicas.cstor.openebs.io --wait=false
kubectl delete crd cstorvolumes.cstor.openebs.io --wait=false
kubectl delete crd cstorcompletedbackups.cstor.openebs.io --wait=false
kubectl delete crd cstorrestores.cstor.openebs.io --wait=false



# validate that all CRD from OpenEBS are removed
kubectl api-resources | grep openebs
kubectl get crd | grep openebs




# cleanup PVC (sparse files)
rm -rf /var/openebs/

# deleting namespace
kubectl delete ns openebs --wait=false

# cleanup namespace finalizers

# be sure that everything else was deleted before

#kubectl get namespace openebs -o json | jq -j '.spec.finalizers=null' > tmp.json
#kubectl replace --raw "/api/v1/namespaces/openebs/finalize" -f ./tmp.json
