#!/usr/bin/env bash
set -eou pipefail


METADATA=config/metadata.yaml
REPO_NAME="tmp"
TOTAL_CHARTS=$(yq r ${METADATA} --length 'charts')

x=$TOTAL_CHARTS
while [ "$x" -gt 0 ];
do
    x=$((x-1))
    CHART_NAME=$(yq r ${METADATA} "charts[$x].name")
    CHART_VERSION=$(yq r ${METADATA} "charts[$x].version")
    CHART_NAMESPACE=$(yq r ${METADATA} "charts[$x].namespace")
    REPOSITORY=$(yq r ${METADATA} "charts[$x].repository")
    OUTPUT=$(yq r ${METADATA} "charts[$x].output")

    # Create output dir if it doesn't exist
    [[ ! -d "${OUTPUT}" ]] && mkdir -p output

    helm repo add "${REPO_NAME}" "${REPOSITORY}"
    helm template "${REPO_NAME}/${CHART_NAME}" \
        --name-template=default \
        --namespace="${CHART_NAMESPACE}" \
        --version="${CHART_VERSION}" \
        --values=config/"${CHART_NAME}"/values.yaml > "${OUTPUT}/${CHART_NAME}.yaml"
done

