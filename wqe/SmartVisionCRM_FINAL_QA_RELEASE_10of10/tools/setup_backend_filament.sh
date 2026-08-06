#!/usr/bin/env bash
set -euo pipefail

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
APP_DIR="$ROOT_DIR/backend/app"
OVERLAY_DIR="$ROOT_DIR/backend/overlay"

echo "== SmartVision CRM | Laravel + Filament setup =="

if ! command -v composer >/dev/null 2>&1; then
  echo "ERROR: composer is required." >&2
  exit 1
fi
if ! command -v php >/dev/null 2>&1; then
  echo "ERROR: php is required." >&2
  exit 1
fi

mkdir -p "$ROOT_DIR/backend"

if [ ! -f "$APP_DIR/artisan" ]; then
  echo "[1/6] Creating fresh Laravel app in: $APP_DIR"
  rm -rf "$APP_DIR"
  composer create-project laravel/laravel "$APP_DIR" --no-interaction
else
  echo "[1/6] Laravel app already exists: $APP_DIR"
fi

cd "$APP_DIR"

echo "[2/6] Installing dependencies (Filament + Spatie Permission)"
composer require filament/filament:^3.0 spatie/laravel-permission:^6.0 laravel/sanctum --no-interaction

echo "[3/6] Applying overlay (Models/Migrations/Seeders/Filament/Translations)"
# Use rsync if available, else fallback to cp
if command -v rsync >/dev/null 2>&1; then
  rsync -a "$OVERLAY_DIR/" "$APP_DIR/"
else
  cp -R "$OVERLAY_DIR/"* "$APP_DIR/"
fi

echo "[4/6] Ensuring app providers (Filament panel provider)"
# Register Filament panel provider in bootstrap/providers.php (Laravel 11+), fallback to config/app.php (Laravel 10)
if [ -f "$APP_DIR/bootstrap/providers.php" ]; then
  php -r '
  $path = "bootstrap/providers.php";
  $c = file_get_contents($path);
  if (strpos($c, "App\\\\Providers\\\\Filament\\\\AdminPanelProvider") === false) {
    $c = preg_replace(
      "/return \[/",
      "return [\n    App\\\\Providers\\\\Filament\\\\AdminPanelProvider::class,",
      $c,
      1
    );
    file_put_contents($path, $c);
  }
  '
else
  # Laravel <=10
  php -r '
  $path = "config/app.php";
  $c = file_get_contents($path);
  if (strpos($c, "App\\\\Providers\\\\Filament\\\\AdminPanelProvider") === false) {
    $c = preg_replace(
      "/App\\\\Providers\\\\RouteServiceProvider::class,/",
      "App\\\\Providers\\\\RouteServiceProvider::class,\n        App\\\\Providers\\\\Filament\\\\AdminPanelProvider::class,",
      $c,
      1
    );
    file_put_contents($path, $c);
  }
  '
fi

# Ensure APP_LOCALE exists for Arabic support
if grep -q "^APP_LOCALE=" .env; then
  true
else
  echo "APP_LOCALE=en" >> .env
fi
if grep -q "^APP_FALLBACK_LOCALE=" .env; then
  true
else
  echo "APP_FALLBACK_LOCALE=en" >> .env
fi

echo "[5/6] Running migrations & seeders"
php artisan key:generate --force

# Sanctum token auth support (API)
php artisan vendor:publish --provider="Laravel\\Sanctum\\SanctumServiceProvider" --tag="sanctum-migrations" --force || true
# Sanctum config (SPA cookie mode)
echo "==> Publishing Sanctum config (SPA cookie auth)"
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider" --tag="sanctum-config" --force || true


php artisan migrate --force
php artisan db:seed --force

echo "[6/6] Done. Access Filament Admin Panel: /admin"
echo "Default Admin: ${SV_DEFAULT_ADMIN_EMAIL:-admin@smartvision.local}"
echo "Default Password: ${SV_DEFAULT_ADMIN_PASSWORD:-Admin@12345}"

echo "IMPORTANT: Change password after first login."
