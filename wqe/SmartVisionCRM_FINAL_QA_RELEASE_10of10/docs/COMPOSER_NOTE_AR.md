# ملاحظة مهمة بخصوص composer.json / composer.lock

- تم إضافة ملف **composer.json** في جذر الحزمة حتى يكون لديك **قائمة الـ Packages المطلوبة** (Dependencies) بشكل واضح.
- ملف **composer.lock** الموجود في هذه النسخة هو **Placeholder** فقط (غير مُولّد من Composer) لأن هذه الحزمة ليست مشروع Laravel كامل مُثبّت عليها الاعتمادات داخل هذا الأرشيف.

## للحصول على composer.lock الحقيقي (بنسخ وإصدارات دقيقة)
1) قم بدمج/تطبيق محتوى `backend/overlay` على مشروع Laravel الأساسي لديك.
2) من جذر مشروع Laravel على السيرفر أو جهاز التطوير شغّل:

- `composer update`

بعدها سيتولّد **composer.lock** الحقيقي ويعكس كل الـ Packages والإصدارات التي تم تثبيتها فعليًا.
