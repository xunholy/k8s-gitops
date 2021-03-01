#!/usr/bin/env bash
set -eo pipefail
shopt -s extglob

[[ -n $DEBUG ]] && set -x

# shellcheck disable=SC2155
REPO_ROOT=$(git rev-parse --show-toplevel)
CLUSTER_ROOT="${REPO_ROOT}/k8s/namespaces"
HELM_REPO_FILES=$(find "$CLUSTER_ROOT/base/flux-system/helm-chart-repositories" -name '*.yaml')
HELM_RELEASE_FILES=$(find "$CLUSTER_ROOT" -name 'helmrelease.yaml')

# TODO: Enable better error handling on overlays without versions explicitly mentioned OR move version out of base
# HELM_RELEASE_FILES=$(find "$CLUSTER_ROOT" -name 'helmrelease.yaml' -or -name 'patch.helmreleases.yaml')

for HELM_REPO_FILE in $HELM_REPO_FILES; do
    # Check resource type equals Kind=HelmRepository
    [[ $(yq r "${HELM_REPO_FILE}" kind) != "HelmRepository" ]] && continue
    CHART_NAME=$(yq r "${HELM_REPO_FILE}" metadata.name)
    CHART_URL=$(yq r "${HELM_REPO_FILE}" spec.url)
    for HELM_RELEASE_FILE in $HELM_RELEASE_FILES; do
        # Check resource type equals Kind=HelmRelease
        [[ $(yq r "${HELM_RELEASE_FILE}" kind) != "HelmRelease" ]] && continue
        # Update HelmRelease when using HelmRepository
        if [[ $(yq r "${HELM_RELEASE_FILE}" spec.chart.spec.sourceRef.name) == "${CHART_NAME}" ]]; then
            # Remove and insert renovate comment; Ensures if a resource does not have the comment
            # or the HelmRepository URL has been changed that these are reflected
            sed -i '/renovate: registryUrl=/d' "${HELM_RELEASE_FILE}"
            sed -i "/.*chart: .*/i \ \ \ \ \ \ # renovate: registryUrl=${CHART_URL}" "${HELM_RELEASE_FILE}"
            echo "Annotated $(basename "${HELM_RELEASE_FILE%.*}") with ${CHART_NAME} for renovatebot..."
        fi
    done
done
