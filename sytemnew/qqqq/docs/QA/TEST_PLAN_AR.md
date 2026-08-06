# خطة اختبار شاملة (CRM V12)

> الهدف: تأكيد الجودة قبل تشغيل Production (وظائف + صلاحيات + سلامة بيانات + أداء + أمن)

## 1) Scope
- لوحتين Filament:
  - Admin: /admin
  - Employee: /employee
- Entities: Users, Companies (Leads), Events, Countries, Packages, JobRuns

## 2) بيانات اختبار (Test Data)
- Admin: س / password
- أنشئ 3 مستخدمين Sales + 1 Manager
- أنشئ 2 Events + 2 Packages + 2 Countries
- Leads:
  - عربي: "شركة النور للتجارة"
  - إنجليزي: "Smart Vision LLC"
  - Mixed: "الشركة 123"

## 3) Smoke (أساسي)
- فتح /admin/login و /employee/login
- فتح كل Resources بدون Errors
- إنشاء/تعديل/حذف سجل واحد من كل Resource

## 4) RBAC (صلاحيات)
### Admin Panel
- Sales يحاول يدخل /admin => ممنوع (redirect/403)
- Manager يدخل /admin => مسموح
### Employee Panel
- Sales يدخل /employee => مسموح
- is_active=false لأي دور => ممنوع الدخول

## 5) Company Rules
- Normalization:
  - اسم عربي => normalized_company_name != فارغ
  - اسم كله رموز => يرفض برسالة واضحة
- Duplicate:
  - إدخال نفس الاسم مع اختلاف مسافات/رموز => يرفض (DB unique + Exception)
- Limit:
  - Sales يملك 60 lead => الإنشاء lead رقم 61 يرفض
- Locked Leads:
  - Sales A يحاول Edit lead مملوك لـ Sales B => ممنوع
  - Sales A يشوف lead Unowned و يقدر claim

## 6) Claim (Concurrency)
- Case: User A و User B يضغطوا Claim لنفس lead في نفس الثانية:
  - نتيجة متوقعة: واحد فقط ينجح، الآخر رسالة "Already claimed"

## 7) Security
- Broken Access Control:
  - فتح رابط edit مباشرة لlead غير مملوك => 403
- CSRF:
  - تغيير بيانات عبر POST بدون token => فشل
- Session:
  - Logout يبطل الجلسة
- Brute Force:
  - توصية: تفعيل rate limit لصفحة login

## 8) Performance (Staging)
- Seed 100k leads:
  - php artisan db:seed --class=PerformanceSeeder
- مقاييس:
  - p95 (List + Search + Claim)
  - CPU/RAM أثناء التصفح
- تحسينات مطبقة:
  - unique على normalized_company_name
  - indexes: owner_id, status, next_followup_date

## 9) Exit Criteria (قبول نهائي)
- نجاح Smoke + RBAC + Claim Concurrency
- عدم وجود Duplicate في DB
- p95 مناسب بعد الـ Indexes
