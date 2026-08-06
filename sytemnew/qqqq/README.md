# Smart Vision CRM V12 (Full Laravel 12 + Filament 3 Project)

## Requirements
- PHP >= 8.2
- Composer
- MySQL/MariaDB (or PostgreSQL)

## Install (Server)
```bash
composer install --no-interaction --prefer-dist --optimize-autoloader
cp .env.example .env
php artisan key:generate
php artisan migrate:fresh --seed
php artisan serve --host=0.0.0.0 --port=8000
```

### URLs
- Admin Panel: `/admin`
- Employee Panel: `/employee`
- Proforma Invoice (Admin/Manager): `/docs/proforma/{company_id}`

### Default Admin
- Email: `admin@smartvisioneg.com`
- Password: `password`

## Notes
- `vendor/`, `node_modules/`, `.env` are generated on the server (not shipped in ZIP).
