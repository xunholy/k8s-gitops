#!/bin/bash

# List of IPs to iterate through
IP_LIST="192.168.50.111 192.168.50.112 192.168.50.113"

# Iterate through the list of IPs and run the command
for IP in $IP_LIST; do
  echo "Running command on $IP"
  talosctl reboot -n $IP -e 192.168.50.111
done
