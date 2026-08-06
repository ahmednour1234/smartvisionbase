
<!DOCTYPE html>
<html lang="en">
@php
    use App\Models\Event;
    use App\Models\HomeSection;
    use App\Models\Setting;
    use App\Models\Blog;
    use Carbon\Carbon;

    // === جلب البيانات ===
    $event = Event::first();
    $home_slider  = HomeSection::where('is_active', true)->where('id', 1)->first();
    $settings     = Setting::first();
    $blogs        = Blog::latest()->take(3)->get(); // آخر ٣ مقالات
    $locale       = app()->getLocale();

    // حماية من القيم الفارغة
    $eventName    = $event->name_en ?? 'Our Special Event';
    $location     = $event->location ?? 'Event Venue';
    $mainImage    = $event->main_image ?? '';
    $mainImageUrl = 'https://smartvisionexpo.com/storage/app/public/' . ltrim($mainImage, '/');

    // === التاريخ والوقت ===
    $start = Carbon::parse($event->event_date ?? now());
    $end   = Carbon::parse($event->end_date ?? $event->event_date ?? now());

    if ($start->format('Y-m') === $end->format('Y-m')) {
        $formattedDate = $start->format('d') . ' - ' . $end->format('d F Y');
    } elseif ($start->format('Y') === $end->format('Y')) {
        $formattedDate = $start->format('d F') . ' - ' . $end->format('d F Y');
    } else {
        $formattedDate = $start->format('d F Y') . ' - ' . $end->format('d F Y');
    }
    $formattedTime = $start->format('h:i A');

    // ألوان وهوية بسيطة
    $primary = '#2c4470';  // أزرق داكن
    $accent  = '#95bb48';  // أخضر مبهج

    // اسم ملف PDF
    $pdfFileName = 'Invitation-' . \Illuminate\Support\Str::slug($eventName, '-') . '-' . now()->format('Ymd-His') . '.pdf';
@endphp

<head>
  <meta charset="UTF-8">
  <meta http-equiv="x-ua-compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>You're Invited</title>
  <style>
    /* الحاوية التي سيتم تحويلها إلى PDF (A4 تقريبًا @96dpi) */
    .pdf-sheet{
      width: 794px;
      min-height: 1123px;
      margin: 0 auto 24px;
      background:#ffffff;
    }

    /* HERO بدون أي قص للصورة */
    .hero{
      position: relative;
      width: 100%;
      overflow: hidden; /* لحماية حدود العنصر، بدون قص للصورة نفسها */
    }
    .hero-img{
      width: 100%;
      height: auto;      /* يخلي الصورة تُعرض كاملة بحسب نسبتها */
      display: block;
    }
    .hero-overlay{
      position: absolute;
      inset: 0;
      background: rgba(0,0,0,.55);
      display: flex;
      align-items: center;
      justify-content: center;
      text-align: center;
      padding: clamp(40px, 8vh, 90px) 24px;
    }
    .hero-inner{
      color: #fff;
      max-width: 800px;
      margin: 0 auto;
    }
    .hero-inner h1{
      font-size: clamp(28px, 3.2vw, 44px);
      font-weight: 700;
      text-transform: uppercase;
      margin: 0 0 10px;
      color:#fff;
    }
    .hero-inner h2{
      font-size: clamp(18px, 2.2vw, 26px);
      font-weight: 400;
      margin: 0 0 15px;
      color:#fff;
    }
    .hero-inner p{
      font-size: clamp(14px, 1.6vw, 18px);
      margin: 0 0 6px;
      color:#fff;
      opacity:.95;
    }

    /* تنسيق بسيط للزر في أسفل الصفحة (خارج منطقة الـ PDF) */
    .pdf-actions{
      text-align:center;
      padding: 6px 16px 40px;
    }
    #downloadBtn{
      padding: 12px 18px;
      border:0; border-radius: 10px; cursor:pointer;
      background: {{ $accent }}; color:#0b1a2b; font-weight:700;
      box-shadow:0 8px 30px -12px rgba(0,0,0,.35)
    }
    #downloadBtn:hover{ filter:brightness(0.95) }

    img{border:0; outline:none; text-decoration:none; display:block;}
    table{border-collapse:collapse;}
    .preheader{display:none!important;visibility:hidden;opacity:0;color:transparent;height:0;width:0;mso-hide:all;overflow:hidden;}
    .page-wrap{direction: rtl}
    @media print{ .pdf-actions{display:none} }
  </style>
</head>ْ
<body style="margin:0; padding:0; background-color:#f5f7fb; color:#000;">

<!-- المنطقة التي سنحوّلها إلى PDF -->
<div id="pdf-content" class="pdf-sheet">

<!-- Preheader -->
<div class="preheader">
  You're invited to {{ $eventName }} on {{ $formattedDate }} at {{ $location }}.
</div>

@if($event->active_blade_email == 15 && !empty($event->text_email))
  {{-- =========== بريد مخصّص من لوحة التحكم =========== --}}
  @php
      $emailHtml = $event->text_email;
      $dearText  = "<p style='font-size:18px; line-height:1.7; color:#333; margin:0 0 14px;'>Dear " . e($client->name ?? 'Guest') . ",</p>";
      if (preg_match('/<img[^>]*>/i', $emailHtml)) {
          $emailHtml = preg_replace('/(<img[^>]*>)/i', '$1' . $dearText, $emailHtml, 1);
      } else {
          $emailHtml = $dearText . $emailHtml;
      }
  @endphp

  <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="background:#f5f7fb;">
    <tr>
      <td align="center" style="padding:30px 16px;">
        <table role="presentation" width="680" cellpadding="0" cellspacing="0" border="0" style="width:680px; max-width:100%;">
          <!-- نص بدون كارد -->
          <tr>
            <td style="padding:0 8px 18px;">
              {!! $emailHtml !!}
            </td>
          </tr>
          <!-- QR بعد الداتا -->
          <tr>
            <td align="center" style="padding:6px 8px 28px;">
              <img src="{{ $qrImageUrl }}" alt="QR Code" width="220" style="max-width:220px; height:auto; border-radius:10px;" crossorigin="anonymous">
              <div style="font-size:13px; color:#6b7280; margin-top:8px;">
                Show this code at the entrance.
              </div>
            </td>
          </tr>
          {{-- لا نعرض المدونات في حالة البريد المخصّص --}}
        </table>
      </td>
    </tr>
  </table>

@else
  @php
      $emailHtml = $event->text_email;
      $dearText  = "<p style='font-size:18px; line-height:1.7; color:#333; margin:0 0 14px;'>Dear " . e($client->name ?? 'Guest') . ",</p>";
      if (preg_match('/<img[^>]*>/i', $emailHtml)) {
          $emailHtml = preg_replace('/(<img[^>]*>)/i', '$1' , $emailHtml, 1);
      } else {
          $emailHtml =   $emailHtml;
      }
  @endphp
  {{-- =========== الدعوة الافتراضية مع صورة كاملة بدون قص =========== --}}
  <div class="hero">
    <img class="hero-img" src="{{ $mainImageUrl }}" alt="Event main image" crossorigin="anonymous">

  </div>

  <!-- محتوى بسيط بدون كارد -->
  <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="background:#f5f7fb;">
    <tr>
      <td align="center" style="padding:26px 16px 6px;">
        <table role="presentation" width="680" cellpadding="0" cellspacing="0" border="0" style="width:680px; max-width:100%;">
          <tr>
            <td style="padding:0 8px 12px;">
              <p style="margin:0 0 14px; font-size:18px; line-height:1.7; color:#111827;">
                Dear {{ $client->name ?? 'Guest' }},
              </p>
              <p style="margin:0 0 12px; font-size:16px; line-height:1.75; color:#374151;">
                            {!! $emailHtml !!}

              </p>
            </td>
          </tr>

          <!-- QR بعد الداتا -->
          <tr>
            <td align="center" style="padding:10px 8px 8px;">
              <img src="{{ $qrImageUrl }}" alt="QR Code" width="220" style="max-width:220px; height:auto; border-radius:10px;" crossorigin="anonymous">
              <div style="font-size:13px; color:#6b7280; margin-top:8px;">
                Show this code at the entrance.
              </div>
            </td>
          </tr>

          <!-- BLOGS: ثلاثة تحت بعض -->
          @if(isset($blogs) && $blogs->count())
            <tr>
              <td style="padding:18px 8px 8px;">
                <h2 style="margin:0 0 10px; font-size:18px; line-height:1.4; color:#111827;">Latest Articles</h2>
              </td>
            </tr>

            @foreach ($blogs as $blog)
              @php
                $blogTitle = $blog->{'title_' . $locale} ?? ($blog->title_en ?? 'Read more');
                $blogName  = $blog->{'name_' . $locale}  ?? '';
                $blogDesc  = \Illuminate\Support\Str::limit(strip_tags($blog->{'description_' . $locale} ?? ''), 140);
                $blogDate  = optional($blog->created_at)->format('d M, Y');
                $blogImg   = asset('public/'.$blog->image);
              @endphp

              <tr>
                <td style="padding:10px 0;">
                  <table role="presentation" width="680" cellpadding="0" cellspacing="0" border="0" style="width:680px; max-width:100%; background:#ffffff; border:1px solid #e9eef5; border-radius:14px; overflow:hidden;">
                    <tr>
                      <td style="padding:0;">
                        <img src="{{ $blogImg }}" alt="Blog image" width="680" style="width:100%; height:auto;" crossorigin="anonymous">
                      </td>
                    </tr>
                    <tr>
                      <td style="padding:14px 16px 16px;">
                        <div style="font-size:13px; color:#6b7280; margin:0 0 4px;">
                          <span>{{ $blogName }}</span> &middot; <time datetime="{{ optional($blog->created_at)->toDateString() }}">{{ $blogDate }}</time>
                        </div>
                        <h3 style="margin:0 0 8px; font-size:18px; line-height:1.4; color:#111827;">
                          <a href="{{ route('web.blog.show',[$blog->id]) }}" style="color:#111827; text-decoration:none;">{{ $blogTitle }}</a>
                        </h3>
                        <p style="margin:0; font-size:15px; line-height:1.7; color:#374151;">
                          {{ $blogDesc }}
                        </p>
                      </td>
                    </tr>
                  </table>
                </td>
              </tr>
            @endforeach
          @endif

        </table>
      </td>
    </tr>
  </table>
@endif

{{-- =========================
     أيقونات التواصل الاجتماعي
========================== --}}
@php
    $socials = [
        'facebook'  => 'facebook.png',
        'twitter'   => 'twitter.png',
        'linkedin'  => 'linkedin.png',
        'youtube'   => 'youtube.png',
        'instagram' => 'instagram.png',
        'x'         => 'x.png',
    ];
    $baseUrl = 'https://toptrustedfxbrokers.com/public/Socials/';
@endphp

@if($settings)
  <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="background:#f5f7fb;">
    <tr>
      <td align="center" style="padding:10px 16px 0;">
        <table role="presentation" width="680" cellpadding="0" cellspacing="0" border="0" style="width:680px; max-width:100%; background:transparent;">
          <tr>
            <td align="center" style="padding:16px;">
              @foreach ($socials as $field => $icon)
                @if (!empty($settings->$field))
                  <a href="{{ $settings->$field }}" target="_blank" style="display:inline-block; margin:0 6px;">
                    <img src="{{ $baseUrl . $icon }}" alt="{{ $field }} icon" width="36" style="width:36px; height:36px;" crossorigin="anonymous">
                  </a>
                @endif
              @endforeach
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
@endif

<!-- Footer -->
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="background:#f5f7fb;">
  <tr>
    <td align="center" style="padding:16px 16px 36px;">
      <table role="presentation" width="680" cellpadding="0" cellspacing="0" border="0" style="width:680px; max-width:100%;">
        <tr>
          <td align="center" style="font-size:12px; color:#6b7280;">
            &copy; {{ now()->year }} Smart Vision. All rights reserved.
          </td>
        </tr>
      </table>
    </td>
  </tr>
</table>

</div><!-- /#pdf-content -->
<!-- زر التحميل تحت خالص (خارج الـ PDF) -->
<!-- زر التحميل تحت خالص (خارج الـ PDF) -->

</body>
</html>

