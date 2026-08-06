# SmartVision CRM — Laravel + Filament + Livewire + Permissions + Translations (READY ZIP)

هذه الحزمة **جاهزة للتثبيت** (Ready-to-install) وتقوم بتحويل الـ CRM إلى:
- Laravel
- Filament (يعتمد على Livewire)
- Roles/Permissions (Spatie)
- Translations (Arabic / English)
- **Sanctum** (Token Mode + SPA Cookie Mode)
- **Secure Headers** (CSP/HSTS/Frame-Options…)
- CI بسيط (PHPUnit + PHP lint) + (اختياري) Playwright smoke

> ملاحظة مهمة: لا يوجد `vendor/` داخل الـ ZIP (لأن Laravel/Composer يثبت الحزم وقت التشغيل).

---

## 1) المتطلبات
- PHP 8.2+
- Composer
- MySQL 8+

---

## 2) التثبيت (أمر واحد)
من داخل فولدر المشروع بعد فك الضغط:

```bash
bash tools/setup_backend_filament.sh
```

السكربت سيقوم بـ:
1) إنشاء Laravel جديد داخل `backend/app`
2) تثبيت Filament + Spatie Permission + Sanctum
3) نسخ الـ Overlay (Models/Migrations/Seeders/Filament Resources/Translations/Tests)
4) تشغيل migrations + seeders

---

## 3) بيانات الدخول الافتراضية
- URL: `/admin`
- Email: `admin@smartvision.local`
- Password: `Admin@12345`

**ضروري تغيير كلمة المرور بعد أول دخول.**

يمكنك تعديلها عبر متغيرات البيئة قبل تشغيل السكربت:
- `SV_DEFAULT_ADMIN_EMAIL`
- `SV_DEFAULT_ADMIN_PASSWORD`

---

## 4) الصلاحيات والتحكم بالوصول
- admin: صلاحيات كاملة
- staff: يرى Leads الخاصة به فقط + إدارة الاجتماعات الخاصة به

---

## 5) قاعدة البيانات
يتم إنشاء الجداول من خلال Migration تقوم باستيراد `schema_core.sql` الموجودة داخل:
`backend/overlay/database/sql/schema_core.sql`

---

## 6) ملاحظات تشغيل على سيرفر
بعد التثبيت:
- اربط DocumentRoot على `backend/app/public`
- اضبط `.env` داخل `backend/app` لبيانات قاعدة البيانات

---

## 7) Auth (Token + Cookie)
### A) Token Mode (مناسب لتطبيقات Mobile أو Integrations)
- Endpoint: `POST /api/auth/login`
- يرجع: `token`
- استخدم: `Authorization: Bearer <token>`

### B) Sanctum SPA Cookie Mode (HttpOnly Cookie)
مناسب لـ Web SPA (Next.js/Vue/React) ويعمل بـ **HttpOnly session cookie** + CSRF.

الـ Flow:
1) `GET /sanctum/csrf-cookie`
2) `POST /auth/login-cookie` (JSON: email/password)
3) استدعاءات API تكون على `auth:sanctum` (بدون Bearer token)
4) `POST /auth/logout-cookie`

إعدادات مهمة في `.env`:
- `FRONTEND_ORIGIN` (لـ CORS)
- `CORS_SUPPORTS_CREDENTIALS=true`
- `SANCTUM_STATEFUL_DOMAINS` (يجب أن يشمل دومين الـ SPA)
- `SESSION_DOMAIN` + `SESSION_SECURE_COOKIE` حسب HTTPS

---

## 8) Secure Headers (CSP / HSTS / Frame-Options)
Middleware: `App\Http\Middleware\SecureHeaders`

مفعّل على:
- `/admin` (Filament Panel)
- جميع `web routes`
- جميع `api routes`

يمكن التحكم عبر `.env`:
- `SECURE_HEADERS_ENABLE_CSP=true|false`
- `SECURE_HEADERS_CSP_REPORT_ONLY=true|false`
- `SECURE_HEADERS_ENABLE_HSTS=true|false` (يُفعّل فقط عند HTTPS)

---

## 9) الاختبارات
تم إضافة اختبارات Feature داخل:
`backend/overlay/tests/Feature`

تشغيلها بعد التثبيت:
```bash
cd backend/app
php artisan test
```

---

## 10) CI (GitHub Actions)
Workflow جاهز:
- `.github/workflows/ci.yml` (يعمل: phpunit + php lint)

اختياري:
- `.github/workflows/playwright-smoke.yml` (تشغيل يدوي على Staging)
  - يطلب `base_url` ثم يفتح `/admin/login` ويتأكد أن الصفحة تعمل.
