#!/usr/bin/env bash
set -euo pipefail

echo "== Smart Vision CRM: Production Deploy =="

if [[ ! -f artisan ]]; then
  echo "ERROR: Run this script from the project root (where artisan exists)." >&2
  exit 1
fi

if ! command -v composer >/dev/null 2>&1; then
  echo "ERROR: composer not found. Install Composer then re-run." >&2
  exit 1
fi

# 1) Environment
if [[ ! -f .env ]]; then
  if [[ -f .env.example ]]; then
    cp .env.example .env
    echo "Created .env from .env.example (please edit DB/APP_URL values)."
  else
    echo "ERROR: .env.example missing." >&2
    exit 1
  fi
fi

# 2) Dependencies
composer install --no-dev --optimize-autoloader

# 3) App key
php artisan key:generate --force

# 4) Migrations
php artisan migrate --force

# 5) Storage symlink
php artisan storage:link || true

# 6) Filament assets
php artisan filament:assets || true

# 7) Caches
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "DONE. If using a scheduler, set a cron for: php artisan schedule:run"
