<!DOCTYPE html>
<html class="wide" lang="en">
<head>
    <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','GTM-KNKDM7V9');</script>

  <title>Smart Vision Summit Dubai</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, height=device-height, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">

  <!-- Favicon -->
  <link rel="icon" href="{{ asset('public/1759844413-egypt 2025-02 11-01.png') }}" type="image/x-icon">

  <!-- Fonts from Google -->
  <link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Barlow%7CBarlow+Condensed:300,400,500,600,700,900">

  <!-- Local Assets -->
  <link rel="stylesheet" href="{{ asset('public/web/assets/css/bootstrap.css') }}?v={{ time() }}">
  <link rel="stylesheet" href="{{ asset('public/web/assets/css/fonts.css') }}?v={{ time() }}">
  <link rel="stylesheet" href="{{ asset('public/web/assets/css/style.css') }}?v={{ time() }}">
  @php
$pixels = \App\Models\Pixel::where('active', true)->get();
@endphp

@foreach($pixels as $pixel)
    @if($pixel->name === 'Facebook')
        <!-- Facebook Pixel -->
        <script>
          fbq('init', '{{ $pixel->pixel_id }}');
          fbq('track', 'PageView');
        </script>
        <noscript>
          <img height="1" width="1" style="display:none"
          src="https://www.facebook.com/tr?id={{ $pixel->pixel_id }}&ev=PageView&noscript=1"/>
        </noscript>
    @endif

    @if($pixel->name === 'TikTok')
        <!-- TikTok Pixel -->
        <script>
          !function (w, d, t) {
              w.TiktokAnalyticsObject = t;
              var ttq = w[t] = w[t] || [];
              ttq.methods = ["page", "track", "identify", "instances", "debug", "on", "off", "once", "ready", "alias", "group", "enableCookie", "disableCookie"];
              ttq.setAndDefer = function (t, e) {
                  t[e] = function () {
                      t.push([e].concat(Array.prototype.slice.call(arguments, 0)))
                  }
              };
              for (var i = 0; i < ttq.methods.length; i++) ttq.setAndDefer(ttq, ttq.methods[i]);
              ttq.instance = function (t) {
                  var e = ttq._i[t] || [];
                  for (var n = 0; n < ttq.methods.length; n++) ttq.setAndDefer(e, ttq.methods[n]);
                  return e
              };
              ttq.load = function (e, n) {
                  var i = "https://analytics.tiktok.com/i18n/pixel/events.js";
                  ttq._i = ttq._i || {};
                  ttq._i[e] = [];
                  ttq._i[e]._u = i;
                  ttq._t = ttq._t || {};
                  ttq._t[e] = +new Date;
                  ttq._o = ttq._o || {};
                  ttq._o[e] = n || {};
                  var o = document.createElement("script");
                  o.type = "text/javascript";
                  o.async = !0;
                  o.src = i + "?sdkid=" + e + "&lib=" + t;
                  var a = document.getElementsByTagName("script")[0];
                  a.parentNode.insertBefore(o, a)
              };

              ttq.load('{{ $pixel->pixel_id }}');
              ttq.page();
          }(window, document, 'ttq');
        </script>
    @endif
@endforeach

</head>
<body>
    <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-KNKDM7V9"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
  <!-- Preloader -->
  <!--<div class="preloader">-->
  <!--  <div class="preloader-body">-->
  <!--    <div class="cssload-container">-->
  <!--      <div class="cssload-speeding-wheel"></div>-->
  <!--    </div>-->
  <!--    <p>Loading...</p>-->
  <!--  </div>-->
  <!--</div>-->

  <!-- Main Page Content -->
  <div class="page">

    {{-- Header --}}
    @include('web.layouts.header')

    {{-- Page Content --}}
    @yield('content')

    {{-- Footer --}}
    @include('web.layouts.footer')

  </div>

  {{-- Global Snackbars --}}
  <div class="snackbars" id="form-output-global"></div>

  {{-- SVG Gradients --}}
  <div class="block-with-svg-gradients">
    <svg xmlns="http://www.w3.org/2000/svg">
      <defs>
        <lineargradient id="svg-gradient-primary" x1="0%" y1="100%" x2="100%" y2="0%">
          <stop offset="0%" style="stop-color:rgb(130,46,168);stop-opacity:1"></stop>
          <stop offset="100%" style="stop-color:rgb(217,14,144);stop-opacity:1"></stop>
        </lineargradient>
      </defs>
    </svg>
  </div>
@php
  $event = \App\Models\Event::where('event_date', '>', now())->orderBy('event_date')->first();
    $setting = \App\Models\Setting::first();

  $whatsappNumber = $setting->phone; // رقم الواتساب
@endphp

 @php
  use Carbon\Carbon;
  $eventDate = \Carbon\Carbon::parse($event->event_date)->format('Y-m-d H:i:s');
@endphp

<style>
  .countdown-section {
    position: fixed;
    bottom: 0;
    width: 100%;
background: linear-gradient(to right, #000000, #E73701);
    color: white;
    padding: 15px 0;
    z-index: 9999;
  }

  .countdown-container {
    display: flex;
    flex-wrap: wrap;
    justify-content: space-between;
    align-items: center;
    gap: 20px;
  }

  .countdown-text {
    font-size: 15px;
    font-weight: bold;
    text-align: center;
    direction: ltr;
  }
  .countdown-text a {
    background-color: #25d366;
    padding: 17px;
    border: none;
    font-size: 20px;
  }

    .countdown-text .bi-whatsapp {
      color: #FFF
    }

  .countdown-timer span {
    margin: 0 10px;
  }
  .action-buttons {
    display: flex;
    gap: 20px;
    flex-wrap: wrap;
    justify-content: center;
  }

  .custom-button-white,
  .custom-button-red {
    padding: 10px 20px;
    font-size: 13px;
    font-weight: 700;
    border-radius: 8px;
    text-transform: uppercase;
    text-decoration: none;
    transition: 0.3s ease-in-out;
  }

  .custom-button-white {
    background-color: #ffffff;
    color: var(--e-global-color-primary, #E73701);
  }

  .custom-button-white:hover {
    background-color: var(--e-global-color-primary, #E73701);
    color: #fff;
  }

  .custom-button-red {
    background-color: var(--e-global-color-primary, #E73701);
    color: white;
  }

  .custom-button-red:hover {
    background-color: #fff;
    color: var(--e-global-color-primary, #E73701);
  }

  #whatsapp-float {
    position: fixed;
    bottom: 90px;
    right: 15px;
    background-color: #25d366;
    color: #fff;
    padding: 12px 14px;
    border-radius: 50%;
    font-size: 20px;
    z-index: 10000;
  }
    .custom-button-green {
    background-color: #25d366;
    color: white;
    padding: 10px 20px;
    font-size: 13px;
    font-weight: 700;
    border-radius: 8px;
    text-transform: uppercase;
    text-decoration: none;
    transition: 0.3s ease-in-out;
  }

  .custom-button-green:hover {
    background-color: #1ebe5d;
    color: white;
  }
 .whatsapp-icon {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 30px;
  height: 30px;
  border: 2px solid #25d366;
  background-color: transparent;
  color: #25d366;
  border-radius: 50%;
  font-size: 22px;
  transition: all 0.3s ease-in-out;
}

.whatsapp-icon:hover {
  background-color: #25d366;
  color: #fff;
  text-decoration: none;
}
@media (max-width: 767px) {
  .countdown-section {
    padding: 5px 0;
  }
  .countdown-container {
  display: flex;
  flex-wrap: wrap;
  justify-content: center; /* ← هذا يجعل العناصر في المنتصف أفقيًا */
  align-items: center;
  gap: 20px;
  text-align: center; /* لتوسيط النص أيضًا */
}
  .countdown-text {
    font-size: 14px;
    font-weight: bold;
    text-align: center;
    direction: ltr;
}
.countdown-text a {
  padding: 20px;
}
  .countdown-timer span {
    margin: 0 2px;
  }
  .action-buttons {
    display: flex;
    gap: 9px;
    flex-wrap: wrap;
    justify-content: center;
}

.whatsapp-icon {
    display: inline-flex
;
    align-items: center;
    justify-content: center;
    width: 24px;
    height: 24px;
    padding-start:5px;
    border: 2px solid #25d366;
    background-color: transparent;
    color: #25d366;
    border-radius: 50%;
    font-size: 22px;
    transition: all 0.3s ease-in-out;
}
}

</style>

{{-- ✅ شريط العد التنازلي --}}
<div class="countdown-section" id="countdownSection">
  <div class="container countdown-container">
    <div class="countdown-text">
      <span id="countdown-timer" class="countdown-timer"></span>
      <a href="https://wa.me/{{ $whatsappNumber }}" target="_blank" class="whatsapp-icon" aria-label="WhatsApp">
        <i class="bi bi-whatsapp"></i>
      </a>
    </div>

    <div class="action-buttons">
      <a href="{{ route('web.register') }}" class="custom-button-white">
        {{ __('register_now') }}
      </a>
      <a href="{{ route('web.becomesponsor') }}" class="custom-button-red">
        {{ __('become_sponsor') }}
      </a>
    </div>
  </div>
</div>



{{-- ✅ سكربت العد التنازلي --}}
<script>
  const eventDate = new Date("{{ $eventDate }}").getTime();
  const countdownEl = document.getElementById("countdown-timer");

  function updateCountdown() {
    const now = new Date().getTime();
    const distance = eventDate - now;

    if (distance < 0) {
      countdownEl.innerHTML = "{{ __('event_started') }}";
      return;
    }

    const days = Math.floor(distance / (1000 * 60 * 60 * 24));
    const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
    const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
    const seconds = Math.floor((distance % (1000 * 60)) / 1000);

    countdownEl.innerHTML = `
      <span>${days} {{ __('days') }}</span>
      <span>${hours} {{ __('hours') }}</span>
      <span>${minutes} {{ __('minutes') }}</span>
      <span>${seconds} {{ __('seconds') }}</span>
    `;
  }

  updateCountdown();
  setInterval(updateCountdown, 1000);
</script>

<script>
  document.addEventListener("DOMContentLoaded", function () {
    window.addEventListener('scroll', function () {
      const scrollTop = window.scrollY || document.documentElement.scrollTop;
      const windowHeight = window.innerHeight;
      const fullHeight = document.documentElement.scrollHeight;

      // هل المستخدم وصل إلى الأسفل؟
      if (scrollTop + windowHeight >= fullHeight - 10) {
        document.getElementById("countdownSection").style.display = "none";
      } else {
        // يرجعه إذا طلع فوق
        document.getElementById("countdownSection").style.display = "block";
      }
    });
  });
</script>

<!-- LightGallery -->
<link href="https://cdn.jsdelivr.net/npm/lightgallery@2.7.1/css/lightgallery-bundle.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/lightgallery@2.7.1/lightgallery.umd.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/lightgallery@2.7.1/plugins/zoom/lg-zoom.umd.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

<script>
    document.addEventListener('DOMContentLoaded', function () {
        lightGallery(document.getElementById('gallery-auto-scroll'), {
            selector: 'a[data-lightgallery="item"]',
            plugins: [lgZoom],
            speed: 500,
        });
    });
</script>
<!-- Scripts -->
  <script src="{{ asset('public/web/assets/js/core.min.js') }}"></script>
  <script src="{{ asset('public/web/assets/js/script.js') }}"></script>
</body>
</html>
