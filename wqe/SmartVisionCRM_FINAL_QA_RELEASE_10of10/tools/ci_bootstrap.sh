#!/usr/bin/env bash
set -euo pipefail

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
APP_DIR="$ROOT_DIR/backend/app"
OVERLAY_DIR="$ROOT_DIR/backend/overlay"

echo "==> CI bootstrap: Creating Laravel app in: $APP_DIR"
rm -rf "$APP_DIR"
mkdir -p "$ROOT_DIR/backend"

composer create-project laravel/laravel "$APP_DIR" --no-interaction

cd "$APP_DIR"

echo "==> Installing deps (Filament, Spatie Permission, Sanctum)"
composer require filament/filament:^3.0 spatie/laravel-permission:^6.0 laravel/sanctum --no-interaction

# Pint (Laravel's formatter) - might already exist in newer Laravel skeletons, but ensure availability
composer require laravel/pint --dev --no-interaction || true

# Publish Sanctum config (optional; safe if tag doesn't exist)
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider" --tag="sanctum-config" --force || true

echo "==> Applying overlay"
rsync -a "$OVERLAY_DIR/" "$APP_DIR/"

# Prepare .env
if [[ -f .env.example && ! -f .env ]]; then
  cp .env.example .env
fi

php artisan key:generate --force

# CI DB (MySQL service)
php -r '
$env = file_get_contents(".env");
$repl = [
  "DB_CONNECTION" => "mysql",
  "DB_HOST" => getenv("DB_HOST") ?: "127.0.0.1",
  "DB_PORT" => getenv("DB_PORT") ?: "3306",
  "DB_DATABASE" => getenv("DB_DATABASE") ?: "smartvision_crm",
  "DB_USERNAME" => getenv("DB_USERNAME") ?: "root",
  "DB_PASSWORD" => getenv("DB_PASSWORD") ?: "root",
  "APP_URL" => getenv("APP_URL") ?: "http://127.0.0.1:8000",
  "FRONTEND_ORIGIN" => getenv("FRONTEND_ORIGIN") ?: "http://127.0.0.1:3000",
  "CORS_SUPPORTS_CREDENTIALS" => "true",
  "SANCTUM_STATEFUL_DOMAINS" => getenv("SANCTUM_STATEFUL_DOMAINS") ?: "127.0.0.1,localhost,127.0.0.1:3000,localhost:3000",
  "SESSION_DOMAIN" => getenv("SESSION_DOMAIN") ?: "127.0.0.1",
  "SESSION_SAME_SITE" => "lax",
  "SESSION_SECURE_COOKIE" => "false",
];
foreach ($repl as $k => $v) {
  if (preg_match("/^{$k}=.*/m", $env)) {
    $env = preg_replace("/^{$k}=.*/m", "{$k}={$v}", $env);
  } else {
    $env .= "\n{$k}={$v}";
  }
}
file_put_contents(".env", $env);
'

echo "==> Waiting for MySQL..."
php -r '
$host=getenv("DB_HOST")?:"127.0.0.1";
$port=getenv("DB_PORT")?:"3306";
$db=getenv("DB_DATABASE")?:"smartvision_crm";
$user=getenv("DB_USERNAME")?:"root";
$pass=getenv("DB_PASSWORD")?:"root";
$dsn="mysql:host=$host;port=$port;dbname=$db";
$deadline=time()+60;
while (true) {
  try {
    new PDO($dsn, $user, $pass);
    break;
  } catch (Throwable $e) {
    if (time() > $deadline) { fwrite(STDERR, "MySQL not ready: {$e->getMessage()}\n"); exit(1);} 
    usleep(500000);
  }
}
'

echo "==> Migrating + seeding"
php artisan migrate --force
php artisan db:seed --force

echo "==> Lint (php -l)"
find app routes config database tests -name '*.php' -print0 | xargs -0 -n1 php -l >/dev/null

if [[ -x ./vendor/bin/pint ]]; then
  echo "==> Pint (format check)"
  ./vendor/bin/pint --test
fi

echo "==> Running PHPUnit"
php artisan test --no-interaction

echo "==> CI bootstrap done"
