#!/usr/bin/env bash
# Stage kubernetes/ for an offline flate render: flate-stage.sh <kubernetes dir> <dest dir>
#
# Flux pulls this tree as an OCI artifact (oci.yaml pushes ./kubernetes, so it is the artifact
# root) and each app from an ExternalArtifact that the ArtifactGenerator cuts out of it at
# runtime. flate can resolve neither: it treats the git root as the artifact root and only
# reads ExternalArtifacts from a local status.artifact.url. Rendering from a copy of
# kubernetes/ makes the copy the root, and the apps' paths are the same inside their artifacts,
# so pointing them at the root source renders exactly what the cluster gets.
set -euo pipefail

src=$(cd "$1" && pwd)
dest=$2

rm -rf "$dest"
mkdir -p "$dest"
cp -R "$src"/. "$dest"/
cd "$dest"

grep -rlZ --include='*.yaml' 'kind: ExternalArtifact' . |
  xargs -0 -r -n1 yq -i '(select(.kind == "Kustomization" and .spec.sourceRef.kind == "ExternalArtifact") | .spec.sourceRef) = {"kind": "OCIRepository", "name": "flux-system", "namespace": "flux-system"}'

# Runtime-only objects with nothing to render offline:
# - flux-instance / flux-operator ResourceSets need the Flux CRDs that the operator installs.
# - The PR-preview ResourceSet takes its inputs from open GitHub PRs.
yq -i 'del(.resources[] | select(. == "helmrelease.yaml"))' clusters/cluster-00/flux-system/flux-instance/app/kustomization.yaml
yq -i 'del(.resources[] | select(. == "helmrelease.yaml"))' clusters/cluster-00/flux-system/flux-operator/app/kustomization.yaml
grep -rlZ --include='*.yaml' 'type: GitHubPullRequest' apps |
  while IFS= read -r -d '' provider; do
    yq -i 'del(.resources[] | select(. == "resourceset.yaml"))' "$(dirname "$provider")/kustomization.yaml"
  done
