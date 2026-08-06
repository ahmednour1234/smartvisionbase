# ملاحظة مهمة: مجلد public/vendor

مجلد `public/vendor` **يتولد تلقائيًا** على السيرفر بعد تثبيت حزم Composer وتشغيل أوامر نشر/بناء الأصول (Assets) لبعض الحزم مثل Filament و Livewire.

## المطلوب على السيرفر (داخل جذر مشروع Laravel حيث يوجد artisan)
1) تثبيت الاعتمادات للإنتاج:
- `composer install --no-dev --optimize-autoloader`

2) توليد `public/vendor`:
- `php artisan filament:assets`
- `php artisan vendor:publish --tag=livewire:assets --force`

## سكربت مساعد
تم إضافة سكربت:
- `tools/publish_public_vendor.sh`

يمكن تشغيله مباشرة من جذر مشروع Laravel أو تحديد المسار عبر:
- `LARAVEL_ROOT=/path/to/app ./tools/publish_public_vendor.sh`
