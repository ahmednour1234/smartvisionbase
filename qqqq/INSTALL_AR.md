# SMART VISION CRM V12 — مشروع Laravel 12 كامل + Filament 3

هذه الحزمة تحتوي على **مشروع Laravel كامل** (بدون vendor) + **Filament Panels** + **CRM Modules** جاهز للرفع والعمل على الخادم.

## المتطلبات
- PHP 8.2+
- Composer
- MySQL / MariaDB / PostgreSQL

## 1) تثبيت الحزم
داخل مجلد المشروع بعد فك الضغط:
```bash
composer install --no-interaction --prefer-dist --optimize-autoloader
cp .env.example .env
php artisan key:generate
```

## 2) إعداد قاعدة البيانات
- عدّل إعدادات DB داخل `.env`
ثم:
```bash
php artisan migrate:fresh --seed
```

## 3) الروابط
- لوحة الإدارة: `/admin`
- لوحة الموظف: `/employee`
- فاتورة Proforma (للـ Admin/Manager): `/docs/proforma/{company_id}`

## 4) بيانات الدخول الافتراضية
- Email: `admin@smartvisioneg.com`
- Password: `password`

## 5) Seeder للأداء (اختياري)
لإنشاء بيانات ضخمة للاختبار (افتراضي 100k شركة + 100 Sales):
```bash
SEED_PERFORMANCE=1 php artisan migrate:fresh --seed
```

## ملاحظات مهمة
- `vendor/` و `.env` لا تأتي داخل الـ ZIP (تتولد على الخادم عبر composer + نسخ env).
