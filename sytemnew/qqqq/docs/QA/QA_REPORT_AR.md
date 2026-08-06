# تقرير اختبار شامل — Smart Vision CRM V12 (Laravel 12 + Filament 3)

**تاريخ التقرير (UTC):** 2026-02-06

## 1) نطاق الاختبار
يغطي هذا التقرير:
- إدارة المستخدمين (Admin/Manager/Sales) + حالة التفعيل `is_active`
- إدارة العملاء المحتملين (Companies/Leads): إنشاء/تعديل/بحث/Claim/توزيع + منع التكرار + حد 60 Lead لكل Sales
- إعدادات الماستر: Events / Countries / Packages
- سجل المهام: Job Runs + أمر يومي (Cron/Scheduler)

> ملاحظة: هذا التقرير يتضمن **نتائج مراجعة كود ثابتة** + **خطة اختبار تنفيذية**. أرقام الأداء (p95) تحتاج تشغيل الاختبارات على بيئة Staging لأن بيئة التنفيذ ليست ضمن هذا الملف.

---

## 2) نتائج “جاهزية الملفات” (File Completeness)
✅ المشروع يحتوي ملفات Laravel الأساسية (بدون vendor) + كل ملفات الـ CRM.

**تم إصلاح Blockers في هذا الإصدار:**
- إصلاح خطأ Syntax في `app/Models/Company.php` (كان يمنع تشغيل المشروع).
- إضافة مجلدات Employee Panel الناقصة + صفحة Dashboard للموظف.

- **إجمالي الملفات:** 94
- **ملفات PHP:** 65

**التحقق على الخادم**
```bash
sha256sum -c CHECKSUMS.sha256
bash verify_integrity_full.sh
```

**ليست “ملفات ناقصة”:**
- `vendor/` يتولد من `composer install`
- `.env` يتولد من `.env.example`
- `composer.lock` يتولد بعد أول `composer install` (اختياري لكن مستحسن)

---

## 3) فحص الجودة (Code Quality / Architecture)
### ✅ نقاط قوة
- **منع التكرار Hard Guard:** Unique على `normalized_company_name` في الـ DB.
- **دعم العربية/Unicode:** تطبيع الاسم باستخدام `\p{L}` و `\p{N}` مع `u` modifier.
- **Claim ذري:** تحديث Atomic بشرط `owner_id IS NULL` + شرط حد 60 تحت التزامن.
- **Indexes للأداء:** على owner_id / status / next_followup_date وغيرها.

### ⚠️ ملاحظات قابلة للتحسين (ليست blockers)
1) **رسائل الأخطاء**: Exceptions في Model تظهر كـ error عام داخل Filament.
   - الأفضل تحويلها إلى Validation errors (User-friendly).
2) **Claim شرط 60**: `whereRaw` يعمل، لكن يُفضَّل نقل حد 60 إلى Trigger/Constraint أو Job queue عند التضخم.
3) **is_active**: تم تطبيقه في `canAccessPanel()` (جيد). يُستحسن أيضاً إضافة Middleware عام لو فيه endpoints خارج Filament.

---

## 4) اختبار وظيفي شامل (Functional Test Suite)
### 4.1 Smoke Tests (حرجة)
- فتح `/` ⇒ Redirect إلى `/admin`
- Login Admin ⇒ فتح جميع Resources
- Login Sales ⇒ فتح `/employee` فقط

### 4.2 RBAC / صلاحيات
- Sales ممنوع من `/admin` (403 أو redirect login)
- Manager/Admin قادرين على `/admin`
- مستخدم `is_active=false` ممنوع من اللوحتين

### 4.3 Companies / Leads
- إنشاء Lead باسم عربي: مثال "شركة النور" ⇒ يتم الحفظ + normalized غير فارغ
- Duplicate: إدخال "شركة النور" ثم "شـركة! النور" ⇒ يجب رفض الثانية (Unique + Guard)
- Sales:
  - يرى Owned + Unowned فقط
  - لا يستطيع Edit لغير Owned
- Claim:
  - Claim لـ Unowned ⇒ تصبح Owned له + status يصبح contacted
  - **Concurrent Claim**: مستخدمين يحاولوا Claim نفس الـ Lead ⇒ واحد فقط ينجح (Atomic)

### 4.4 حد 60 Lead
- Sales عنده 60 Lead ⇒ Create جديد يجب يفشل برسالة واضحة
- Sales عنده 59 Lead ⇒ Claim/Create يسمح
- Concurrency: 2 Claims في نفس اللحظة مع 59 Lead ⇒ يجب ألا يتجاوز 60

### 4.5 Master Data
- CRUD Events/Countries/Packages
- التحقق من العلاقات في Lead (event_id/package_id/country_id)

---

## 5) اختبار أمان شامل (Security)
### 5.1 Broken Access Control
- Sales يحاول فتح URL edit لLead مش Owned ⇒ يجب منع (403 / hide action + policy)
- Sales يحاول حذف Lead مش Owned ⇒ يجب منع

### 5.2 Session / Authentication
- Logout يبطل الجلسة
- Session fixation: تأكد من تدوير session على login (Laravel default)
- Rate limiting: مستحسن إضافة throttle لصفحة login (Nginx/Laravel)

### 5.3 Data Integrity
- Unique Constraint يمنع duplicates تحت الضغط (اختبار حقيقي بعمليتين متزامنتين insert)
- تأكد من عدم وجود Mass Assignment غير مقصود (guarded=[] مستخدم، مناسب لـ Filament لكن راقب)

---

## 6) اختبار أداء شامل (Performance)
### 6.1 بيانات الاختبار
- 100,000 Companies
- 100 Sales users
- 50 Events / 250 Countries / 20 Packages

استخدم:
```bash
SEED_PERFORMANCE=1 php artisan migrate:fresh --seed
```

### 6.2 قياسات مطلوبة (p95)
- Employee: List My Leads
- Employee: Search company_name
- Claim
- Admin: List Companies + Filters

### 6.3 مراقبة
- Laravel Telescope أو Debugbar (Staging فقط)
- MySQL slow query log
- EXPLAIN على queries الأكثر تكراراً
- CPU/RAM و DB connections

### 6.4 أهداف قبول (Exit Criteria)
- p95 List/Search <= 800ms على Staging متوسط
- Claim atomically بدون duplicates
- لا يوجد N+1 ظاهر في Filament tables

---

## 7) قائمة العيوب الحرجة (إذا ظهرت في التنفيذ)
- فشل `composer install` (غالباً بسبب إصدار PHP أو extensions)
- مشكلة صلاحيات storage/bootstrap/cache على السيرفر
- عدم تشغيل scheduler (cron) ⇒ لا يتم تسجيل JobRuns

---

## 8) توصيات قبل Production
- تفعيل HTTPS + إعدادات session secure
- ضبط `APP_ENV=production` و `APP_DEBUG=false`
- تشغيل:
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```
