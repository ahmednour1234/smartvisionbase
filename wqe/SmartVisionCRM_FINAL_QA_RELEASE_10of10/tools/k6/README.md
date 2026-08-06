# k6 smoke (Leads list/search)

This is a **basic** load/smoke test for the API endpoints:
- `GET /api/leads` (listing)
- `GET /api/leads?q=...` (search)

## Run locally

1) Get a token (example):

```bash
BASE_URL="http://localhost:8000"
TOKEN=$(curl -s -X POST "$BASE_URL/api/auth/login" \
  -H 'Content-Type: application/json' \
  -d '{"email":"admin@smartvision.test","password":"password"}' | jq -r '.token')
```

2) Run k6:

```bash
k6 run \
  -e BASE_URL="$BASE_URL" \
  -e TOKEN="$TOKEN" \
  -e VUS=10 \
  -e DURATION=30s \
  tools/k6/leads_list_search.js
```

## Run via Docker (if k6 isn't installed)

```bash
docker run --rm -i \
  -e BASE_URL="$BASE_URL" \
  -e TOKEN="$TOKEN" \
  -e VUS=10 \
  -e DURATION=30s \
  -v "$PWD:/work" -w /work \
  grafana/k6 run tools/k6/leads_list_search.js
```
