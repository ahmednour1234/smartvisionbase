# SmartVision CRM — نسخة نهائية مُراجَعة من QA (9/10)

> تاريخ المراجعة: 2026-01-21

هذه النسخة تدمج تحسينات QA الخاصة بالأداء/الأمان/الصلاحيات التي طُرحت سابقًا، مع مراجعة سريعة لأكثر أسباب "الصفحات البيضاء" شيوعًا في Laravel/Filament.

## 1) تحسينات الأداء

### 1.1 كاش للـ Lookups
- Endpoint: `GET /api/lookups`
- تم إضافة كاش (TTL قابل للضبط) + هيدر:
  - `X-Lookups-Cache: HIT|MISS`
- دعم `?refresh=1` (للأدمن فقط) لتحديث الكاش يدويًا.
- تم إضافة Invalidation تلقائي للكاش بعد Store/Update/Delete من:
  - `LookupAdminController`

### 1.2 Cursor Pagination للـ Leads (اختياري)
- Endpoint: `GET /api/leads`
- يتم استخدام `cursorPaginate()` عند وجود `cursor` أو تفعيل `CRM_USE_CURSOR_PAGINATION=true`.
- الهدف: تجنب بطء OFFSET على قواعد بيانات كبيرة.

## 2) تحسينات الأمان

### 2.1 إخفاء وجود السجلات (تقليل Enumeration)
- عند تفعيل `CRM_HIDE_EXISTENCE=true`:
  - في حالات عدم امتلاك الصلاحية يتم إرجاع `404 Not Found` بدل `403 Forbidden` في المسارات الحساسة (Leads/Activities/Meetings).
- هذا يقلل إشارات وجود السجلات للمهاجمين.

## 3) تحسينات الصلاحيات (Meetings)

- افتراضيًا:
  - Admin: كامل الصلاحيات.
  - Creator: تعديل/حذف الاجتماع.
- تم إضافة خيار:
  - `CRM_MEETINGS_ALLOW_LEAD_OWNER_EDIT=true`
  - يسمح لمالك الـLead (sales_rep) بتعديل/حذف الاجتماعات المرتبطة بالـLead حتى لو لم يكن هو المنشئ.
- تم تطبيق القاعدة في:
  - `MeetingController` + `Meeting` model (enforcement)

## 4) إعدادات ENV

أضف/عدّل:

```env
CRM_LOOKUPS_CACHE_TTL=900
CRM_LOOKUPS_CACHE_KEY=crm:lookups:all:v1

CRM_HIDE_EXISTENCE=true

CRM_MEETINGS_ALLOW_LEAD_OWNER_EDIT=true

CRM_USE_CURSOR_PAGINATION=false
```

## 5) قائمة تحقق QA قبل الإصدار (تقلل "الصفحات البيضاء")

> صفحات بيضاء غالبًا تكون بسبب: CSP/Assets/Vite/CORS/500 errors أو JS console errors.

### 5.1 تحقق من الـLogs
- `storage/logs/laravel.log` (خصوصًا 500 errors)
- تأكد أن `APP_DEBUG=false` في الإنتاج.

### 5.2 تحقق من Console (Browser DevTools)
- افتح `/admin` وتأكد لا توجد أخطاء:
  - `Content Security Policy` violations
  - `Failed to load resource`
  - `CORS` errors

### 5.3 تحقق من Sanctum + CORS
- نفّذ:
  1) `GET /sanctum/csrf-cookie`
  2) `POST /auth/login-cookie`
  3) استدعاء API محمي `GET /api/auth/me`
- تأكد من:
  - `SANCTUM_STATEFUL_DOMAINS`
  - `SESSION_DOMAIN`
  - `config/cors.php`

### 5.4 Smoke Test سريع
- `/api/lookups` (يجب أن ترى `X-Lookups-Cache`)
- `/api/leads?per_page=20`
- `/api/leads?per_page=20&cursor=...` (بعد أول استدعاء هتحصل على next_cursor)
- إنشاء Meeting ثم تعديل/حذف وفقًا لدور المستخدم

## 6) الملفات التي تم تعديلها

- `backend/overlay/config/crm.php` (جديد)
- `backend/overlay/app/Http/Controllers/Api/LookupController.php`
- `backend/overlay/app/Http/Controllers/Api/Admin/LookupAdminController.php`
- `backend/overlay/app/Http/Controllers/Api/LeadController.php`
- `backend/overlay/app/Http/Controllers/Api/LeadActivityController.php`
- `backend/overlay/app/Http/Controllers/Api/MeetingController.php`
- `backend/overlay/app/Models/Meeting.php`
- `backend/overlay/app/Http/Requests/Api/Leads/LeadIndexRequest.php`

