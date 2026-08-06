# دليل تجهيز ورفع Smart Vision CRM (Laravel 12 + Filament 3)

## إصلاحات تم تنفيذها داخل الـZIP
- إصلاح خطأ Syntax في: `app/Models/Company.php` كان يمنع تشغيل المشروع.
- إضافة هيكل Employee Panel الناقص:
  - إنشاء: `app/Filament/Employee/Pages` و `app/Filament/Employee/Widgets`
  - إضافة Dashboard: `app/Filament/Employee/Pages/Dashboard.php`

## متطلبات السيرفر
- PHP 8.2+
- Composer
- MySQL 8 (أو MariaDB مكافئ)
- Node.js (اختياري فقط لو عندك Frontend build)

## خطوات التشغيل (Production)
داخل مجلد المشروع على السيرفر:

```bash
cp .env.example .env
# عدّل إعدادات DB و APP_URL

composer install --no-dev --optimize-autoloader
php artisan key:generate
php artisan migrate --force
php artisan storage:link

# Filament assets (حسب احتياجك)
php artisan filament:assets

php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## ملاحظة مهمة
هذا الـZIP هو **Source Project** (بدون مجلد `vendor`).
لاعتباره "جاهز تشغيل" يجب تنفيذ `composer install` على السيرفر/البيئة.
