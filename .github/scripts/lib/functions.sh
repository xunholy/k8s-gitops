#!/usr/bin/env bash
# shellcheck disable=all

set -o errexit
set -o nounset
set -o pipefail
shopt -s lastpipe

check() {
    command -v "${1}" >/dev/null 2>&1 || {
        echo >&2 "ERROR: ${1} is not installed or not found in \$PATH" >&2
        exit 1
    }
}

chart_registry_url() {
    local helm_release=
    local chart_id=
    helm_release="${1}"
    chart_id=$(yq eval .spec.chart.spec.sourceRef.name "${helm_release}" 2>/dev/null)
    # Discover all HelmRepository
    for file in $(find . -iname '*-charts.yaml' -o -iname '*-chart.yaml' -type f); do
        # Skip non HelmRepository
        [[ $(yq eval .kind "${file}" 2>/dev/null) != "HelmRepository" ]] && continue
        # Skip unrelated HelmRepository
        [[ "${chart_id}" != $(yq eval .metadata.name "${file}" 2>/dev/null) ]] && continue
        yq eval .spec.url "${file}"
        break
    done
}

chart_name() {
    local helm_release=
    helm_release="${1}"
    yq eval .spec.chart.spec.chart "${helm_release}" 2>/dev/null
}

chart_version() {
    local helm_release=
    helm_release="${1}"
    yq eval .spec.chart.spec.version "${helm_release}" 2>/dev/null
}

chart_values() {
    local helm_release=
    helm_release="${1}"
    yq eval .spec.values "${helm_release}" 2>/dev/null
}
