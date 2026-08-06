<!DOCTYPE html>
<html lang="ar" dir="rtl">
@php
    use App\Models\Event;
    use App\Models\HomeSection;
    use App\Models\Setting;
    use Carbon\Carbon;

    $event = Event::where('id', $eventId)->first();
    $home_slider = HomeSection::where('is_active', true)->where('id', 1)->first();
    $settings = Setting::first();

    $start = Carbon::parse($event->event_date ?? now());
    $end = Carbon::parse($event->end_date ?? $event->event_date);

    if ($start->format('Y-m') === $end->format('Y-m')) {
        $formattedDate = $start->format('d') . ' - ' . $end->translatedFormat('d F Y');
    } elseif ($start->format('Y') === $end->format('Y')) {
        $formattedDate = $start->translatedFormat('d F') . ' - ' . $end->translatedFormat('d F Y');
    } else {
        $formattedDate = $start->translatedFormat('d F Y') . ' - ' . $end->translatedFormat('d F Y');
    }

    $formattedTime = $start->translatedFormat('h:i A');
@endphp

<head>
    <meta charset="UTF-8">
    <title>دعوتك المجانية</title>
</head>

<body style="margin:0; background-color:#ffffff; color:#000;" dir="rtl">
<div style="direction: rtl; text-align: right; font-family:'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;" dir="rtl">
@if($event->active_blade_email == 1 && !empty($event->text_email_ar))
@php
    $emailHtml = $event->text_email_ar;

    // النص اللي هنضيفه بعد أول صورة
    $dearText = "<p style='font-size:18px; line-height:1.7; color:#333;' dir='rtl'>
        عزيزي/عزيزتي  $client->name ,
    </p>";

    // حقن النص بعد أول صورة <img> أو <figure>
    $emailHtml = preg_replace(
        '/(<img[^>]*>)/i',
        '$1' . $dearText,
        $emailHtml,
        1 // نستبدل أول occurrence فقط
    );
@endphp

<div style="max-width:800px; margin:40px auto; padding:0 30px; font-size:17px; line-height:1.7; color:#333; text-align: right;" dir="rtl">
    {!! $emailHtml !!}
</div>

    <div style="text-align:center; margin: 30px 0;" dir="rtl">
        <img src="{{ $qrImageUrl }}" alt="رمز الاستجابة السريعة"
             style="max-width: 220px; border-radius: 12px; border: 8px solid #fff; background: #fff; box-shadow: 0 12px 30px rgba(0,0,0,0.3);">
    </div>

@else

    @php
        $mainImageUrl = 'https://dashboardusers.iqbrandx.com/public/' . ltrim($event->main_image, '/');
    @endphp

    <!-- Hero Section -->
  <div style="position:relative; direction:rtl;">
  <!-- الصورة كاملة بدون قص -->
  <img src="{{ $mainImageUrl }}" alt="" style="width:100%; height:auto; display:block;">

  <!-- الطبقة اللي فوق الصورة (نفس المساحة بالظبط) -->
  <div style="
       position:absolute; inset:0;
       background: rgba(0,0,0,0.6);
       display:flex; align-items:center;
       text-align:right;">
    <div style="width:100%; max-width:800px; margin-left:auto; padding:70px 30px; color:#fff;">
      <h1 style="font-size:38px; font-weight:bold; margin:0 0 10px;">دعوتك المجانية</h1>
      <h2 style="font-size:22px; font-weight:normal; margin:0 0 15px;">
        {{ $event->name_ar ?? 'انضم إلينا لاكتشاف مستقبل الفعاليات' }}
      </h2>
      <p style="font-size:16px; opacity:.95; margin:0 0 5px;">
        {{ $formattedDate }} - {{ $formattedTime }}
      </p>
      <p style="font-size:16px; opacity:.95; margin:0;">
        {{ $event->location ?? 'مكان الفعالية' }}
      </p>
    </div>
  </div>
</div>


    <!-- Main Content -->
    <div style="max-width:800px; margin:40px auto; padding:0 30px; text-align:right;">
      <p style="font-size:18px; line-height:1.7; color:#333;" dir="rtl">
  عزيزي/عزيزتي {{ $client->name }},
</p>

<p style="font-size:17px; line-height:1.7; color:#333;" dir="rtl">
  يسعدنا دعوتك لحضور فعاليتنا القادمة بتاريخ <strong>{{ $formattedDate }}</strong> في <strong>{{ $event->location ?? 'مكان الفعالية' }}</strong>.
</p>

<p style="font-size:17px; line-height:1.7; color:#333;" dir="rtl">
  انضم إلى نخبة من الخبراء والمبدعين لاستكشاف مستقبل التسويق الرقمي وتنظيم الفعاليات.
</p>

<p style="font-size:17px; line-height:1.7; color:#333;" dir="rtl">
  هذه الدعوة خاصة بك. يُرجى إحضار رمز الاستجابة السريعة (QR) التالي للدخول:
</p>

        <div style="text-align:center; margin: 30px 0;">
            <img src="{{ $qrImageUrl }}" alt="رمز الاستجابة السريعة"
                 style="max-width: 220px; border-radius: 12px; border: 8px solid #fff; background: #fff; box-shadow: 0 12px 30px rgba(0,0,0,0.3);">
        </div>
    </div>

@endif

<!-- Social Icons -->
@php
    $socials = [
        'facebook'  => 'facebook.png',
        'twitter'   => 'twitter.png',
        'linkedin'  => 'linkedin.png',
        'youtube'   => 'youtube.png',
        'instagram' => 'instagram.png',
        'x'         => 'x.png',
    ];
    $baseUrl = 'https://toptrustedfxbrokers.iqbrandx.com/public/Socials/';
@endphp

@if($settings)
    <div style="margin-top:40px; display:flex; justify-content:center; gap:15px; flex-wrap:wrap; direction: rtl;">
        @foreach ($socials as $field => $icon)
            @if (!empty($settings->$field))
                <a href="{{ $settings->$field }}" target="_blank" style="display:inline-block;">
                    <img src="{{ $baseUrl . $icon }}"
                         alt="أيقونة {{ $field }}"
                         style="width:35px; height:35px; display:block; border:0;" />
                </a>
            @endif
        @endforeach
    </div>
@endif

<!-- Footer -->
<div style="text-align:center; font-size:13px; color:#888; margin:40px 0 20px; direction: rtl;">
    &copy; {{ now()->year }} سمارت فيجن. جميع الحقوق محفوظة.
</div>

</div>
</body>
</html>
