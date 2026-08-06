#!/usr/bin/env bash
set -euo pipefail

BASE_URL="${BASE_URL:-http://localhost:8000}"
TOKEN="${TOKEN:-}"
VUS="${VUS:-10}"
DURATION="${DURATION:-30s}"

if command -v k6 >/dev/null 2>&1; then
  BASE_URL="$BASE_URL" TOKEN="$TOKEN" VUS="$VUS" DURATION="$DURATION" k6 run "$(dirname "$0")/k6/leads_list_search.js"
  exit 0
fi

echo "k6 not found; running via Docker..."

docker run --rm -i \
  -e BASE_URL="$BASE_URL" \
  -e TOKEN="$TOKEN" \
  -e VUS="$VUS" \
  -e DURATION="$DURATION" \
  grafana/k6 run - < "$(dirname "$0")/k6/leads_list_search.js"
