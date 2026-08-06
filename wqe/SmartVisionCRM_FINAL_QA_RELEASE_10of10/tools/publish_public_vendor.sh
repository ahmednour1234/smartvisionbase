#!/usr/bin/env bash
set -euo pipefail

# Publishes vendor assets into public/vendor for production deployments.
# Usage:
#   ./tools/publish_public_vendor.sh                # run in Laravel root
#   LARAVEL_ROOT=/path/to/app ./tools/publish_public_vendor.sh

LARAVEL_ROOT="${LARAVEL_ROOT:-.}"

if [ ! -f "$LARAVEL_ROOT/artisan" ]; then
  echo "ERROR: artisan not found. Set LARAVEL_ROOT to your Laravel project root." >&2
  exit 1
fi

cd "$LARAVEL_ROOT"

echo "[1/4] Ensuring storage link..."
php artisan storage:link || true

echo "[2/4] Publishing Filament assets (if installed)..."
php artisan filament:assets || true

echo "[3/4] Publishing Livewire assets (if installed)..."
php artisan vendor:publish --tag=livewire:assets --force || true

echo "[4/4] Caching config/routes/views (recommended for production)..."
php artisan config:cache || true
php artisan route:cache || true
php artisan view:cache || true

echo "Done. public/vendor should now be populated (depending on installed packages)."
