#!/bin/bash
set -e
set -u
set -o pipefail

# This script upgrades Talos on a list of specified IP addresses.
# Instructions https://www.talos.dev/v1.4/talos-guides/upgrading-talos/
# Note: Use the version of the documentation corrosponding with the upgrade path.

# List of IPs to process
readonly IP_LIST=(
  # "192.168.50.111"
  # "192.168.50.112"
  # "192.168.50.113"
  # "192.168.50.114"
  # "192.168.50.115"
  # "192.168.50.116"
  "192.168.50.121"
  "192.168.50.122"
  "192.168.50.123"
  "192.168.50.124"
)

# Define the Talos installer image
readonly INSTALLER_IMAGE="ghcr.io/siderolabs/installer:v1.4.0"

# Upgrade Talos on each IP address
for IP in "${IP_LIST[@]}"; do
  echo "Upgrading Talos on $IP"
  talosctl upgrade -n "$IP" -e "$IP" \
    --image "$INSTALLER_IMAGE" \
    --wait --preserve
done
