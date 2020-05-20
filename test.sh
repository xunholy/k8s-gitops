#!/usr/bin/env bash
set -eou pipefail

test() {
  curl -LIsw '%{http_code}' http://www.example.org -o /dev/null
}

RESPONSE=$(test)
echo "Response Code: $RESPONSE"