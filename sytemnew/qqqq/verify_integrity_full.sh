#!/usr/bin/env bash
set -euo pipefail

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"

cd "$ROOT_DIR"

echo "== Smart Vision CRM V12 Full - Integrity Check =="
echo "Root: $ROOT_DIR"

required=(
  "artisan"
  "bootstrap/app.php"
  "bootstrap/providers.php"
  "public/index.php"
  "composer.json"
  "routes/web.php"
  "routes/console.php"
  "app/Providers/Filament/AdminPanelProvider.php"
  "app/Providers/Filament/EmployeePanelProvider.php"
  "app/Models/Meeting.php"
  "app/Http/Controllers/DocController.php"
  "resources/views/docs/proforma.blade.php"
  "resources/views/docs/contract.blade.php"
  "app/Filament/Admin/Resources/UserResource.php"
  "app/Filament/Admin/Resources/MeetingResource.php"
  "app/Filament/Employee/Resources/MyCompanyResource.php"
  "database/migrations/2026_01_01_000000_master_schema_v11.php"
  "database/migrations/2026_01_01_000002_meetings.php"
)

missing=0
for p in "${required[@]}"; do
  if [[ ! -e "$p" ]]; then
    echo "MISSING: $p"
    missing=1
  fi
done

if [[ "$missing" -eq 1 ]]; then
  echo "FAILED: Missing required files."
  exit 2
fi

if command -v sha256sum >/dev/null 2>&1; then
  echo "Checking SHA256..."
  sha256sum -c CHECKSUMS.sha256
  echo "OK: All files match checksums."
else
  echo "WARN: sha256sum not found; skipping checksum verification."
fi

echo "PASSED."
