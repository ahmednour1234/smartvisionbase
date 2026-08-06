# دليل قياس الأداء (Staging)

## تجهيز
1) php artisan migrate:fresh --seed
2) php artisan db:seed --class=PerformanceSeeder

## قياس يدوي سريع
- افتح /employee > My Portfolio
- جرّب search باسم Company 99999
- جرّب Claim لlead غير مملوك

## قياس p95 (مقترح)
- استخدم k6 أو JMeter:
  - سيناريو List
  - سيناريو Search
  - سيناريو Claim
- استهدف 200 مستخدم متزامن (Sales)

## مراقبة
- Laravel Telescope أو MySQL slow query log
- راقب N+1 queries في جداول Filament
