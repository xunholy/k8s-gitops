#!/bin/bash

set -euo pipefail

# Display usage information
usage() {
    echo "Usage: $0 [release-name] [namespace]"
    echo
    echo "If release-name is provided, only that Helm release will be analyzed."
    echo "If namespace is provided, only releases in that namespace will be analyzed."
    echo "Without arguments, all Helm releases in the cluster will be analyzed."
    exit 1
}

# Check for required tools and install helm-values-manager plugin if needed
check_requirements() {
    local required_tools=("kubectl" "helm" "jq")
    for tool in "${required_tools[@]}"; do
        if ! command -v "$tool" &> /dev/null; then
            echo "Error: $tool is required but not installed."
            exit 1
        fi
    done

    # Check if helm-values-manager plugin is installed
    if ! helm plugin list | grep -q "values-manager"; then
        echo "Installing helm-values-manager plugin..."
        helm plugin install https://github.com/xUnholy/helm-values-manager
    fi

    # Create config file to ignore encrypted files if it doesn't exist
    if [ ! -f ~/.helm-values-manager.yaml ]; then
        echo "Creating helm-values-manager config to ignore encrypted files..."
        cat > ~/.helm-values-manager.yaml << EOF
ignore:
  patterns:
    - "\.enc\.ya?ml$"
EOF
    fi
}

# Check if a file is encrypted (ends with .enc.yaml or .enc.yml)
is_encrypted_file() {
    local file="$1"
    if [[ "$file" =~ \.(enc\.yaml|enc\.yml)$ ]]; then
        return 0  # true in bash
    fi
    return 1  # false in bash
}

# Sanitize name
sanitize_name() {
    local name="$1"
    # Remove special characters, convert to lowercase, and ensure valid length
    echo "$name" | tr '[:upper:]' '[:lower:]' | sed 's/[^a-z0-9-]/-/g' | cut -c1-63
}

# Get information from a Helm release
get_helm_release_info() {
    local name="$1"
    local namespace="$2"

    # Get release information using helm list
    local release_info chart_info chart_name chart_version
    release_info=$(helm list -n "$namespace" -f "$name" -o json 2>/dev/null)

    if [ -z "$release_info" ] || [ "$release_info" = "[]" ]; then
        echo "Error: Could not find release $namespace/$name"
        return 1
    fi

    # Extract chart and version from helm list
    chart_info=$(echo "$release_info" | jq -r '.[0].chart')
    chart_name=$(echo "$chart_info" | cut -d '-' -f 1)
    chart_version=$(echo "$chart_info" | cut -d '-' -f 2)

    if [ -z "$chart_name" ] || [ "$chart_name" = "null" ]; then
        echo "Error: Could not determine chart name for release $namespace/$name"
        return 1
    fi

    echo "Debug: Found chart $chart_name version $chart_version" >&2

    # Try to determine repository URL
    local repo_url=""

    # Check if we have a matching HelmRelease to get repository information
    local hr_json
    hr_json=$(kubectl get helmrelease -n "$namespace" "$name" -o json 2>/dev/null || echo "{}")

    if [ -n "$hr_json" ] && [ "$hr_json" != "{}" ]; then
        echo "Debug: Found matching HelmRelease CR" >&2

        # First check if it's using chart.spec
        local repo_ref repo_namespace repo_kind
        repo_ref=$(echo "$hr_json" | jq -r '.spec.chart.spec.sourceRef.name // empty')

        if [ -n "$repo_ref" ] && [ "$repo_ref" != "null" ]; then
            repo_namespace=$(echo "$hr_json" | jq -r '.spec.chart.spec.sourceRef.namespace // .metadata.namespace // "'$namespace'"')
            repo_kind=$(echo "$hr_json" | jq -r '.spec.chart.spec.sourceRef.kind // empty')

            if [ -n "$repo_kind" ] && [ "$repo_kind" != "null" ]; then
                case "$repo_kind" in
                    "HelmRepository")
                        repo_url=$(kubectl get helmrepository "$repo_ref" -n "$repo_namespace" -o jsonpath='{.spec.url}' 2>/dev/null)
                        echo "Debug: Found URL from HelmRepository: $repo_url" >&2
                        ;;
                    "OCIRepository")
                        repo_url=$(kubectl get ocirepository "$repo_ref" -n "$repo_namespace" -o jsonpath='{.spec.url}' 2>/dev/null)
                        # Ensure URL has oci:// prefix
                        if [ -n "$repo_url" ] && [[ "$repo_url" != oci://* ]]; then
                            repo_url="oci://$repo_url"
                        fi
                        echo "Debug: Found URL from OCIRepository: $repo_url" >&2
                        ;;
                esac
            fi
        # Then check if it's using chartRef
        else
            repo_kind=$(echo "$hr_json" | jq -r '.spec.chartRef.kind // empty')
            repo_ref=$(echo "$hr_json" | jq -r '.spec.chartRef.name // empty')

            if [ -n "$repo_kind" ] && [ -n "$repo_ref" ] && [ "$repo_kind" = "OCIRepository" ]; then
                repo_url=$(kubectl get ocirepository "$repo_ref" -n "$namespace" -o jsonpath='{.spec.url}' 2>/dev/null)
                if [ -n "$repo_url" ] && [[ "$repo_url" != oci://* ]]; then
                    repo_url="oci://$repo_url"
                fi
                echo "Debug: Found URL from ChartRef OCIRepository: $repo_url" >&2
            fi
        fi
    fi

    # If still no repository URL, try to find the repository in Helm
    if [ -z "$repo_url" ]; then
        echo "Debug: Searching Helm repos for $chart_name" >&2
        # Try searching for the chart in helm repositories
        local search_results repo_name
        search_results=$(helm search repo "$chart_name" -o json 2>/dev/null || echo "[]")

        if [ -n "$search_results" ] && [ "$search_results" != "[]" ]; then
            # Try to find exact match first
            repo_name=$(echo "$search_results" | jq -r --arg chart "$chart_name" --arg version "$chart_version" \
                'map(select(.name | endswith($chart) and .version == $version)) | .[0].name // empty')

            # If no exact version match, try just chart name
            if [ -z "$repo_name" ]; then
                repo_name=$(echo "$search_results" | jq -r --arg chart "$chart_name" \
                    'map(select(.name | endswith($chart))) | .[0].name // empty')
            fi

            if [ -n "$repo_name" ]; then
                # Extract the repository prefix
                repo_name=${repo_name%/*}
                # Get the repository URL
                repo_url=$(helm repo list -o json | jq -r --arg name "$repo_name" \
                    '.[] | select(.name == $name) | .url // empty')
                echo "Debug: Found URL from Helm search: $repo_url" >&2
            fi
        fi
    fi

    # Common repositories fallback if no URL found
    if [ -z "$repo_url" ]; then
        echo "Debug: Using fallback repository for $chart_name" >&2
        case "$chart_name" in
            cilium)
                repo_url="https://helm.cilium.io/"
                ;;
            istio | istio-base | istio-cni | istiod)
                repo_url="https://istio-release.storage.googleapis.com/charts"
                ;;
            cert-manager)
                repo_url="https://charts.jetstack.io"
                ;;
            kube-prometheus-stack)
                repo_url="https://prometheus-community.github.io/helm-charts"
                ;;
            *)
                # Try bitnami as last resort for many common charts
                repo_url="https://charts.bitnami.com/bitnami"
                ;;
        esac
    fi

    echo "$chart_name|$chart_version|$repo_url"
}

# Add a chart repository
add_chart_repo() {
    local url="$1"
    local chart="$2"

    # Skip OCI repositories as they don't need to be added
    if [[ "$url" == oci://* ]]; then
        # Remove oci:// prefix for helm commands
        url="${url#oci://}"
        echo "$url"
        return 0
    fi

    # Create a sanitized repo name from the URL
    local repo_name
    repo_name=$(echo "$url" | sed 's/https:\/\///' | sed 's/http:\/\///' | sed 's/\//-/g')
    repo_name="vm-${repo_name}"
    repo_name=$(sanitize_name "$repo_name")

    # Check if repo already exists
    if helm repo list | grep -q "^${repo_name}[[:space:]]"; then
        local existing_url
        existing_url=$(helm repo list | grep "^${repo_name}[[:space:]]" | awk '{print $2}')
        if [ "$existing_url" != "$url" ]; then
            echo "Repository $repo_name exists but with different URL. Updating..." >&2
            helm repo remove "$repo_name" >/dev/null 2>&1
            helm repo add "$repo_name" "$url" >/dev/null 2>&1
        fi
    else
        echo "Adding repository $repo_name..." >&2
        helm repo add "$repo_name" "$url" >/dev/null 2>&1
    fi

    # Update repository
    echo "Updating repository $repo_name..." >&2
    helm repo update "$repo_name" >/dev/null 2>&1

    # Verify chart is available in repository
    if ! helm search repo "$repo_name/$chart" &>/dev/null; then
        echo "Warning: Chart $chart not found in repository $repo_name" >&2
    fi

    # Just return the repo name cleanly, with no other output
    echo -n "$repo_name"
}

# Analyze a Helm release
analyze_helm_release() {
    local name="$1"
    local namespace="$2"

    echo "Analyzing Helm release ${namespace}/${name}..."

    # Get release info
    local release_info
    if ! release_info=$(get_helm_release_info "$name" "$namespace"); then
        echo "Error: Failed to get information for release $namespace/$name"
        return 1
    fi

    IFS='|' read -r chart_name chart_version repo_url <<< "$release_info"

    if [ -z "$chart_name" ] || [ -z "$repo_url" ]; then
        echo "Error: Could not determine chart information for release $namespace/$name"
        return 1
    fi

    echo "Found chart: $chart_name, version: $chart_version, repository: $repo_url"

    # Add repository and get repo name
    echo "Adding chart repository..."
    local repo_name
    repo_name=$(add_chart_repo "$repo_url" "$chart_name")

    # Check if we got a valid repo name
    if [[ -z "$repo_name" || "$repo_name" == *"Updating repository"* ]]; then
        echo "Error: Failed to get valid repository name"
        return 1
    fi

    echo "Using repository: $repo_name"

    # Create temporary file for current values
    local temp_values
    temp_values=$(mktemp)

    # Get the values from the Helm release
    echo "Fetching values for ${namespace}/${name}..."
    helm get values -n "$namespace" "$name" > "$temp_values"

    # Check if the values file is encrypted
    if grep -q "sops:" "$temp_values" || grep -q "ENC\[" "$temp_values"; then
        echo "Warning: The values for ${namespace}/${name} appear to be encrypted. Skipping analysis."
        rm -f "$temp_values"
        return 0
    fi

    # Run helm values-manager command directly (let the plugin handle all output)
    echo "Analyzing values for ${namespace}/${name} (chart: ${repo_name}/${chart_name})..."
    local status=0
    helm values-manager -chart "${repo_name}/${chart_name}" \
                     -chart-version "${chart_version}" \
                     -downstream "$temp_values" || status=$?

    if [ $status -eq 0 ]; then
        echo "Analysis completed successfully for ${namespace}/${name}"
    else
        echo "Warning: Analysis failed for ${namespace}/${name}"
    fi

    # Clean up
    rm -f "$temp_values"
}

# Main execution
main() {
    check_requirements

    local target_name=""
    local target_namespace=""

    # Parse arguments
    if [[ $# -ge 1 ]]; then
        target_name="$1"
    fi

    if [[ $# -ge 2 ]]; then
        target_namespace="$2"
    fi

    if [ -n "$target_name" ] && [ -n "$target_namespace" ]; then
        # Analyze specific release
        analyze_helm_release "$target_name" "$target_namespace"
    elif [ -n "$target_namespace" ]; then
        # Analyze all releases in namespace
        echo "Analyzing all Helm releases in namespace $target_namespace..."
        helm list -n "$target_namespace" -o json | jq -r '.[] | .name' | while read -r name; do
            analyze_helm_release "$name" "$target_namespace"
        done
    else
        # Analyze all releases in all namespaces
        echo "Analyzing all Helm releases in the cluster..."
        helm list --all-namespaces -o json | jq -r '.[] | "\(.namespace)/\(.name)"' | while IFS='/' read -r namespace name; do
            analyze_helm_release "$name" "$namespace"
        done
    fi
}

main "$@"
