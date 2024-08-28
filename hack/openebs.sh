#!/bin/bash

kubectl get bdc -A --no-headers | awk '{print $2}' | xargs -I {} kubectl patch -n openebs bdc {} -p '{"metadata":{"finalizers":null}}' --type=merge
