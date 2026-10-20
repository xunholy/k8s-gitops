#!/usr/bin/env bash
# Unified diff of two `flate build all` outputs, one file per object: flate-diff.sh <base.yaml> <pr.yaml>
#
# flate's own `diff` (changed-only mode) can't map changed files back to their owning
# Kustomizations in this repo's OCI + ExternalArtifact layout, so CI renders both trees in
# full (a few seconds each) and compares the objects here instead.
set -euo pipefail

base=$(realpath "$1")
pr=$(realpath "$2")
work=$(mktemp -d)
trap 'rm -rf "$work"' EXIT

split() {
  mkdir -p "$work/$2"
  # Chart bumps rewrite these on every object; they are noise in a review.
  yq 'del(.metadata.labels["helm.sh/chart"], .metadata.labels["app.kubernetes.io/version"], .metadata.labels.chart,
          .spec.template.metadata.labels["helm.sh/chart"], .spec.template.metadata.labels["app.kubernetes.io/version"],
          .spec.template.metadata.annotations["checksum/config"], .spec.template.metadata.annotations["checksum/secret"])' "$1" |
    (cd "$work/$2" && yq -s '.kind + "_" + (.metadata.namespace // "cluster") + "_" + .metadata.name' -)
}

split "$base" base
split "$pr" pr
cd "$work"
diff -ruN base pr | sed -E "s/^(---|\+\+\+) ([^\t]+)\t.*/\1 \2/" || true
