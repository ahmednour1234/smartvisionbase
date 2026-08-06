@php
    use App\Models\Setting;
    use Carbon\Carbon;

    $setting = Setting::first();

    // ✅ هات أقرب Event قادم (أولاً)، لو مفيش هات آخر واحد (عشان مايبقاش null)
    $now = now();
    $event = \App\Models\Event::query()
        ->orderByRaw("CASE WHEN event_date >= ? THEN 0 ELSE 1 END", [$now])
        ->orderBy('event_date', 'asc')
        ->first();

    // ✅ date_active: default 1
    $dateActive = (int)($event->date_active ?? 1);

    // ✅ لو date_active = 0 => ممنوع parse للتاريخ بالكامل
    $eventDT = null;
    $eventDate = null;

    // انتهى/Coming/نشط؟
    $eventEnded = false;      // past or no date (only when active)
    $eventComing = false;     // date_active=0

    if ($dateActive === 0) {
        $eventComing = true;
        $eventEnded = false; // مش ended، دي coming
    } else {
        // date_active = 1 => نسمح بالـ parse
        $eventDT = optional($event)->event_date ? Carbon::parse($event->event_date) : null;
        $eventDate = $eventDT ? $eventDT->format('Y-m-d H:i:s') : null;

        // انتهى أو غير موجود؟
        $eventEnded = !$eventDT || $eventDT->isPast();
    }

    // رقم واتساب
    $whatsappNumber = optional($setting)->phone ?? '';
    $waClean = preg_replace('/\D+/', '', $whatsappNumber); // أرقام فقط
@endphp

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Global Financial Markets Awards London</title>

    <!-- favicons Icons -->
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('public/'.$setting->img) }}?v={{ time() }}" />
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('public/GFMA_logo_FINAAAAAALLLL_111-01.png') }}?v={{ time() }}" />
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('public/GFMA_logo_FINAAAAAALLLL_111-01.png') }}?v={{ time() }}" />
    <link rel="manifest" href="{{ asset('public/web/assets/images/favicons/site.webmanifest') }}?v={{ time() }}" />

    <!-- fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,100..1000;1,9..40,100..1000&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100..900&display=swap" rel="stylesheet">

    <!-- CSS Files -->
    <link rel="stylesheet" href="{{ asset('public/web/assets/vendors/bootstrap/css/bootstrap.min.css') }}?v={{ time() }}" />
    <link rel="stylesheet" href="{{ asset('public/web/assets/vendors/animate/animate.min.css') }}?v={{ time() }}" />
    <link rel="stylesheet" href="{{ asset('public/web/assets/vendors/animate/custom-animate.css') }}?v={{ time() }}" />
    <link rel="stylesheet" href="{{ asset('public/web/assets/vendors/fontawesome/css/all.min.css') }}?v={{ time() }}" />
    <link rel="stylesheet" href="{{ asset('public/web/assets/vendors/jarallax/jarallax.css') }}?v={{ time() }}" />
    <link rel="stylesheet" href="{{ asset('public/web/assets/vendors/jquery-magnific-popup/jquery.magnific-popup.css') }}?v={{ time() }}" />
    <link rel="stylesheet" href="{{ asset('public/web/assets/vendors/odometer/odometer.min.css') }}?v={{ time() }}" />
    <link rel="stylesheet" href="{{ asset('public/web/assets/vendors/swiper/swiper.min.css') }}?v={{ time() }}" />
    <link rel="stylesheet" href="{{ asset('public/web/assets/vendors/eventflow-icons/style.css') }}?v={{ time() }}">
    <link rel="stylesheet" href="{{ asset('public/web/assets/vendors/owl-carousel/owl.carousel.min.css') }}?v={{ time() }}" />
    <link rel="stylesheet" href="{{ asset('public/web/assets/vendors/owl-carousel/owl.theme.default.min.css') }}?v={{ time() }}" />
    <link rel="stylesheet" href="{{ asset('public/web/assets/vendors/bootstrap-select/css/bootstrap-select.min.css') }}?v={{ time() }}" />
    <link rel="stylesheet" href="{{ asset('public/web/assets/vendors/nice-select/nice-select.css') }}?v={{ time() }}" />
    <link rel="stylesheet" href="{{ asset('public/web/assets/vendors/jquery-ui/jquery-ui.css') }}?v={{ time() }}" />

    <!-- Template styles -->
    <link rel="stylesheet" href="{{ asset('public/web/assets/css/eventflow.css') }}?v={{ time() }}" />
    <link rel="stylesheet" href="{{ asset('public/web/assets/css/eventflow-responsive.css') }}?v={{ time() }}" />

    <style>
        a{ text-decoration:none; }
        body{ color:white; }

        .countdown-timer, .countdown-timer span{ font-family:"Roboto",sans-serif!important; }
        @media (max-width:767px){
          .countdown-timer, .countdown-timer span{ font-family:"Roboto",sans-serif!important; }
        }

        .countdown-section{
          position:fixed; bottom:0; width:100%;
          background:linear-gradient(90deg,#FFE986 0%,#C48127 100%);
          color:black; padding:15px 0; z-index:9999;
          font-family:"Roboto",sans-serif!important;
        }
        .countdown-container{ display:flex; flex-wrap:wrap; justify-content:space-around; align-items:center; gap:20px; }
        .countdown-text{ font-size:17px; text-align:center; direction:ltr; }
        .countdown-text a{ background-color:#25d366; padding:17px; border:none; font-size:20px; }
        .countdown-text .bi-whatsapp{ color:#FFF; }

        .countdown-timer span{ margin:0 10px; }
        .action-buttons{ display:flex; gap:20px; flex-wrap:wrap; justify-content:center; }

        .custom-button-white,.custom-button-red{
          padding:10px 20px; font-size:16px; font-weight:700; border-radius:8px;
          text-transform:uppercase; text-decoration:none; transition:.3s;
        }
        .custom-button-white{ background:#fff; color:var(--e-global-color-primary,#000); }
        .custom-button-white:hover{ background:var(--e-global-color-primary,#ffe986); color:#000; }
        .custom-button-red{ background:var(--e-global-color-primary,#ffe986); color:black; }
        .custom-button-red:hover{ background:#fff; color:var(--e-global-color-primary,#000); }

        .whatsapp-icon{
          display:inline-flex; align-items:center; justify-content:center;
          width:30px; height:30px; border:2px solid #25d366; background:transparent; color:#25d366;
          border-radius:50%; font-size:22px; transition:.3s;
        }
        .whatsapp-icon:hover{ background:#25d366; color:#fff; text-decoration:none; }

        /* ended: كبّر الخط ووسّطه */
        .countdown-section.ended .countdown-timer{
          font-size:28px; font-weight:800; letter-spacing:.5px;
          display:inline-block; padding:6px 10px;
        }
        .countdown-section.ended .action-buttons{ display:none !important; }

        /* ✅ coming: نفس فكرة ended */
        .countdown-section.coming .countdown-timer{
          font-size:28px; font-weight:900; letter-spacing:.6px;
          display:inline-block; padding:6px 10px;
        }
        .countdown-section.coming .action-buttons{ display:none !important; }

        @media (max-width:767px){
          .countdown-section{ padding:5px 0; }
          .countdown-container{ justify-content:center; gap:20px; text-align:center; }
          .countdown-text{ font-size:14px; font-weight:bold; }
          .countdown-text a{ padding:15px; }
          .countdown-timer span{ margin:0 3px; }
          .action-buttons{ gap:9px; }
          .whatsapp-icon{ width:24px; height:24px; font-size:22px; }
          .countdown-section.ended .countdown-timer{ font-size:22px; }
          .countdown-section.coming .countdown-timer{ font-size:22px; }
        }
    </style>
</head>

<body class="body-bg-color-1">
  <div class="page-wrapper">
    @if(session('toastr'))
      <script>
        document.addEventListener("DOMContentLoaded", function () {
          toastr["{{ session('toastr.type') }}"]("{{ session('toastr.message') }}");
        });
      </script>
    @endif

    {{-- Header --}}
    @include('web.layouts.header')

    {{-- Page Content --}}
    @yield('content')

    {{-- Footer --}}
    @include('web.layouts.footer')
  </div>

  {{-- Mobile nav / search --}}
  <div class="mobile-nav__wrapper">
    <div class="mobile-nav__overlay mobile-nav__toggler"></div>
    <div class="mobile-nav__content">
      <div class="mobile-nav__container"></div>
    </div>
  </div>

  <div class="search-popup">
    <div class="search-popup__overlay search-toggler"></div>
    <div class="search-popup__content">
      <form action="#">
        <label for="search" class="sr-only">search here</label>
        <input type="text" id="search" placeholder="Search Here..." />
        <button type="submit" aria-label="search submit" class="thm-btn">
          <i class="fas fa-search"></i>
        </button>
      </form>
    </div>
  </div>

  <a href="#" data-target="html" class="scroll-to-target scroll-to-top"><i class="fas fa-arrow-up"></i></a>

  {{-- ====== Countdown Bar ====== --}}
  <div class="countdown-section
              {{ $eventComing ? 'coming' : ($eventEnded ? 'ended' : '') }}"
       id="countdownSection">

    <div class="container countdown-container">
      <div class="countdown-text">
        <span id="countdown-timer" class="countdown-timer">
          @if($eventComing)
            {{ __('Coming Soon') }}
          @elseif($eventEnded)
            {{ __('Event has ended') }}
          @endif
        </span>

        @if($waClean)
          <a href="https://wa.me/{{ $waClean }}" target="_blank" class="whatsapp-icon" aria-label="WhatsApp" rel="noopener">
            <i class="bi bi-whatsapp"></i>
          </a>
        @endif
      </div>

      {{-- الأزرار تظهر فقط لو (مش ended) و (مش coming) --}}
      @if(!$eventEnded && !$eventComing)
        <div class="action-buttons" id="countdownActions">
          <a href="{{ route('web.voting') }}" class="custom-button-white">{{ __('Voting') }}</a>
          <a href="{{ route('web.becomesponsor') }}" class="custom-button-red">{{ __('become_sponsor') }}</a>
        </div>
      @endif
    </div>
  </div>

  {{-- ====== Countdown Script ====== --}}
  <script>
    (function(){
      const comingServer = @json($eventComing);
      const endedServer  = @json($eventEnded);
      const eventDateStr = @json($eventDate); // null لو coming أو مفيش تاريخ
      const timerEl   = document.getElementById('countdown-timer');
      const sectionEl = document.getElementById('countdownSection');
      const actionsEl = document.getElementById('countdownActions');

      function setComing(){
        sectionEl.classList.remove('ended');
        if (!sectionEl.classList.contains('coming')) sectionEl.classList.add('coming');
        if (actionsEl) actionsEl.style.display = 'none';
        if (timerEl) timerEl.textContent = "{{ __('Coming Soon') }}";
      }

      function setEnded(){
        sectionEl.classList.remove('coming');
        if (!sectionEl.classList.contains('ended')) sectionEl.classList.add('ended');
        if (actionsEl) actionsEl.style.display = 'none';
        if (timerEl) timerEl.textContent = "{{ __('Event has ended') }}";
      }

      // ✅ Coming أعلى أولوية
      if (comingServer) {
        setComing();
      } else if (endedServer || !eventDateStr) {
        setEnded();
      } else {
        const target = new Date(eventDateStr).getTime();
        if (isNaN(target)) { setEnded(); return; }

        function tick(){
          const now = Date.now();
          const diff = target - now;

          if (diff <= 0) { setEnded(); return; }

          const d = Math.floor(diff/86400000);
          const h = Math.floor((diff%86400000)/3600000);
          const m = Math.floor((diff%3600000)/60000);
          const s = Math.floor((diff%60000)/1000);

          if (timerEl) {
            timerEl.innerHTML = `
              <span>${d} {{ __('days') }}</span>
              <span>${h} {{ __('hours') }}</span>
              <span>${m} {{ __('minutes') }}</span>
              <span>${s} {{ __('seconds') }}</span>
            `;
          }
        }

        tick();
        setInterval(tick, 1000);
      }

      // إخفاء الشريط عند أسفل الصفحة
      window.addEventListener('scroll', () => {
        const atBottom = (window.scrollY + window.innerHeight) >= (document.documentElement.scrollHeight - 10);
        sectionEl.style.display = atBottom ? 'none' : 'block';
      }, {passive:true});
    })();
  </script>

  <!-- LightGallery / Icons -->
  <link href="https://cdn.jsdelivr.net/npm/lightgallery@2.7.1/css/lightgallery-bundle.min.css" rel="stylesheet" />
  <script src="https://cdn.jsdelivr.net/npm/lightgallery@2.7.1/lightgallery.umd.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/lightgallery@2.7.1/plugins/zoom/lg-zoom.umd.min.js"></script>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

  <script>
    document.addEventListener('DOMContentLoaded', function () {
      const gallery = document.getElementById('gallery-auto-scroll');
      if (gallery && window.lightGallery) {
        lightGallery(gallery, { selector:'a[data-lightgallery="item"]', plugins:[lgZoom], speed:500 });
      }
    });
  </script>

  <!-- Vendor Scripts -->
  <script src="{{ asset('public/web/assets/vendors/jquery/jquery-3.6.0.min.js') }}?v={{ time() }}"></script>
  <script src="{{ asset('public/web/assets/vendors/bootstrap/js/bootstrap.bundle.min.js') }}?v={{ time() }}"></script>
  <script src="{{ asset('public/web/assets/vendors/jarallax/jarallax.min.js') }}?v={{ time() }}"></script>
  <script src="{{ asset('public/web/assets/vendors/jquery-ajaxchimp/jquery.ajaxchimp.min.js') }}?v={{ time() }}"></script>
  <script src="{{ asset('public/web/assets/vendors/jquery-appear/jquery.appear.min.js') }}?v={{ time() }}"></script>
  <script src="{{ asset('public/web/assets/vendors/jquery-magnific-popup/jquery.magnific-popup.min.js') }}?v={{ time() }}"></script>
  <script src="{{ asset('public/web/assets/vendors/jquery-validate/jquery.validate.min.js') }}?v={{ time() }}"></script>
  <script src="{{ asset('public/web/assets/vendors/odometer/odometer.min.js') }}?v={{ time() }}"></script>
  <script src="{{ asset('public/web/assets/vendors/swiper/swiper.min.js') }}?v={{ time() }}"></script>
  <script src="{{ asset('public/web/assets/vendors/wnumb/wNumb.min.js') }}?v={{ time() }}"></script>
  <script src="{{ asset('public/web/assets/vendors/wow/wow.js') }}?v={{ time() }}"></script>
  <script src="{{ asset('public/web/assets/vendors/isotope/isotope.js') }}?v={{ time() }}"></script>
  <script src="{{ asset('public/web/assets/vendors/owl-carousel/owl.carousel.min.js') }}?v={{ time() }}"></script>
  <script src="{{ asset('public/web/assets/vendors/bootstrap-select/js/bootstrap-select.min.js') }}?v={{ time() }}"></script>
  <script src="{{ asset('public/web/assets/vendors/jquery-ui/jquery-ui.js') }}?v={{ time() }}"></script>
  <script src="{{ asset('public/web/assets/vendors/nice-select/jquery.nice-select.min.js') }}?v={{ time() }}"></script>
  <script src="{{ asset('public/web/assets/vendors/countdown/countdown.min.js') }}?v={{ time() }}"></script>
  <script src="{{ asset('public/web/assets/vendors/marque/marquee.min.js') }}?v={{ time() }}"></script>
  <script src="{{ asset('public/web/assets/vendors/sidebar-content/jquery-sidebar-content.js') }}?v={{ time() }}"></script>

  <!-- Template Script -->
  <script src="{{ asset('public/web/assets/js/eventflow.js') }}?v={{ time() }}"></script>
</body>
</html>
