@extends('web.layouts.app')

@section('content')

@php
    use Carbon\Carbon;

    $locale = app()->getLocale();

    // Safely parse dates
    $start = !empty($event->event_date) ? Carbon::parse($event->event_date) : null;
    $end   = !empty($event->end_date)   ? Carbon::parse($event->end_date)   : $start; // fallback

    // If end before start, swap to avoid negative ranges
    if ($start && $end && $end->lt($start)) {
        [$start, $end] = [$end, $start];
    }

    $rangeText = '';
    $timeText  = '';

    if ($start) {
        $startDay   = $start->translatedFormat('d');
        $endDay     = $end->translatedFormat('d');
        $startMonth = $start->translatedFormat('F');
        $endMonth   = $end->translatedFormat('F');
        $startYear  = $start->translatedFormat('Y');
        $endYear    = $end->translatedFormat('Y');

        if ($end && $start->isSameDay($end)) {
            $rangeText = "{$startDay} {$startMonth} {$startYear}";
        } elseif ($end && $startYear === $endYear && $startMonth === $endMonth) {
            $rangeText = "{$startDay}–{$endDay} {$startMonth} {$startYear}";
        } elseif ($end && $startYear === $endYear) {
            $rangeText = "{$startDay} {$startMonth} – {$endDay} {$endMonth} {$startYear}";
        } elseif ($end) {
            $rangeText = "{$startDay} {$startMonth} {$startYear} – {$endDay} {$endMonth} {$endYear}";
        } else {
            $rangeText = "{$startDay} {$startMonth} {$startYear}";
        }

        // Optional time text
        $sTime = $start->translatedFormat('h:i A');
        $eTime = $end?->translatedFormat('h:i A');
        if ($sTime && $eTime) {
            $timeText = $sTime . ' – ' . $eTime;
        }
    }

    // ===== Meta Pixel / CAPI toggles =====
    $pixelId = config('services.meta.pixel_id'); // .env META_PIXEL_ID
    $hasPixel = !empty($pixelId);
    $testCode = env('META_TEST_EVENT_CODE');     // optional
@endphp

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.css"/>
<style>
  :root{ --brand:#E73701; --brand-2:#FF552E; --brand-dark:#cc252e; --ink:#222; --muted:#666; --bg:#f9f9f9; }
  a, a:hover, a:focus, a:active, a:visited, a:focus-visible { text-decoration:none !important; }

  .swiper-slider-1 { height:min(100svh,100vh); max-height:min(100svh,100vh); }
  @media (max-width:767px){ .swiper-slider-1{ height:min(75svh,75vh); max-height:min(75svh,75vh);} }

  .section-swiper-absoulte{ height:min(100svh,100vh); max-height:min(100svh,100vh); position:relative; overflow:hidden; display:flex; align-items:center; justify-content:center; }
  @media (max-width:767px){ .section-swiper-absoulte{ height:min(75svh,75vh); max-height:min(75svh,75vh);} }

  .responsive-title{ font-size:clamp(2.25rem,6vw,6rem) !important; line-height:1.1; }
  .promo-title{ font-size:clamp(2rem,9vw,3.5rem) !important; }
  @media (max-width:576px){ .promo-title{ font-size:2.8rem !important; } }

  .modern-stats-section{ padding:40px 0; background:#fff; }
  .modern-stats-inner{ display:grid; grid-template-columns:repeat(auto-fit,minmax(200px,1fr)); gap:20px; }
  .stat-card{ background:#fff; border:1px solid #eee; border-radius:16px; padding:22px; text-align:center; transition:transform .3s, box-shadow .3s; will-change:transform; }
  .stat-card:hover{ transform:translateY(-8px); box-shadow:0 15px 45px rgba(204,37,46,.25); }
  .stat-icon{ margin-bottom:8px; }
  .stat-number{ font-weight:800; font-size:clamp(28px,6vw,44px); color:var(--ink); display:inline-block; }
  .stat-suffix{ font-weight:800; color:var(--ink); margin-inline-start:4px; }
  .stat-label{ color:var(--muted); margin:0; }

  .gallery-auto-scroll,.sponsor-auto-scroll{ overflow:hidden;width:100%;position:relative; }
  .gallery-track,.sponsor-track{ display:flex;width:max-content;gap:24px;padding-block:10px;will-change:transform; }
  .gallery-track{ animation:scroll-horizontal 60s linear infinite; }
  .sponsor-track{ animation:sponsor-scroll 50s linear infinite; gap:40px;align-items:center;justify-content:center;margin-top:40px; }
  .thumb-wrapper{ flex:0 0 auto;width:300px;height:400px;border-radius:12px;overflow:hidden;position:relative; }
  .thumb-wrapper img{ width:100%;height:100%;object-fit:cover;display:block;border-radius:12px;box-shadow:0 4px 12px rgba(0,0,0,.3); }
  @keyframes scroll-horizontal{ from{transform:translateX(0)} to{transform:translateX(-50%)} }
  @keyframes sponsor-scroll{ from{transform:translateX(0)} to{transform:translateX(-50%)} }
  .sponsor-item{ flex:0 0 auto;width:190px;height:190px;display:flex;align-items:center;justify-content:center;background:linear-gradient(to right,#000,#5b090973);border-radius:12px;padding:10px; }
  .sponsor-item img{ max-width:100%;max-height:90%;object-fit:contain;border-radius:10px; }
  .gallery-wrapper{ overflow-x:auto;-webkit-overflow-scrolling:touch;scroll-behavior:smooth; }
  .gallery-scroll-container{ display:flex;gap:20px;width:max-content; }
  .gallery-thumb{ flex:0 0 auto;width:calc(25% - 15px);border-radius:8px;overflow:hidden;box-shadow:0 2px 6px rgba(0,0,0,.1);transition:transform .3s; }
  .gallery-thumb img{ width:100%;height:auto;display:block; }
  .gallery-thumb:hover{ transform:scale(1.05); }
  .swiper-slide{ height:300px;display:flex;align-items:center;justify-content:center; }
  .swiper-slide img{ width:400px;height:400px;object-fit:cover;border-radius:8px;box-shadow:0 2px 8px rgba(0,0,0,.1);transition:transform .3s; }
  .swiper-slide img:hover{ transform:scale(1.03); }
  .gallery-grid-mobile{ display:grid;grid-template-columns:repeat(2,1fr);gap:15px;padding-bottom:30px; }
  .gallery-grid-mobile .gallery-item img{ width:100%;height:250px;border-radius:8px;box-shadow:0 2px 8px rgba(0,0,0,.1);object-fit:cover; }

  .speaker{ position:relative;border-radius:12px;overflow:hidden;box-shadow:0 10px 30px rgba(0,0,0,.1);display:flex;flex-direction:column;cursor:pointer;height:100%;background:#fff;transition:box-shadow .3s, transform .3s;border:1px solid #e0e0e0;z-index:0; }
  .speaker::before{ content:"";position:absolute;inset:8px;border-radius:10px;border:2px solid rgba(204,37,46,.4);opacity:0;transition:opacity .3s;pointer-events:none;z-index:1; }
  .speaker:hover{ transform:translateY(-6px);box-shadow:0 20px 40px rgba(204,37,46,.2); }
  .speaker:hover::before{ opacity:1; }
  .speaker-img{ background:#fff;padding:15px;display:flex;justify-content:center;align-items:center;overflow:hidden; }
  .speaker-img img{ width:100%;height:auto;object-fit:contain;border-radius:10px;transition:transform .3s;display:block; }
  .speaker:hover .speaker-img img{ transform:scale(1.05); }
  .speaker-info{ background:#f7f7f7;padding:20px 15px;flex-grow:1;display:flex;flex-direction:column;justify-content:center;text-align:center; }
  .speaker-title{ font-weight:700;font-size:1.5rem;color:#222;margin-bottom:8px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis; }
  .speaker-position{ color:#666;font-size:1rem;margin-bottom:12px;word-wrap:break-word; }
  .speaker-social-list{ list-style:none;padding:0;margin:0 auto;display:flex;justify-content:center;gap:15px;margin-top:auto; }
  .speaker-social-list li a{ color:var(--brand-dark);font-size:18px;transition:color .25s; }
  .speaker-social-list li a:hover{ color:#e84b3a; }
  @media (max-width:991.98px){ .speaker-img{padding:10px} .speaker-info{padding:0} .speaker-title{font-size:1.2rem;white-space:normal} .speaker-position{font-size:.95rem} }
  @media (max-width:576px){ .speaker-img{padding:8px} .speaker-title{font-size:1rem} .speaker-position{font-size:.875rem} }
  @media (max-width:767px){ .speaker-card{display:none} .speaker-card:nth-child(-n+4){display:block} }

  .breadcrumbs-custom{ background-size:cover;background-position:center; }
  @media (max-width:991.98px){ .breadcrumbs-custom{height:350px !important} .breadcrumbs-custom-title{font-size:28px;padding-top:150px} }

  .past-events-section{ background:#f4f2ee !important;padding:80px 0;position:relative;overflow:hidden }
  .past-events-section::before{ content:'';position:absolute;inset:0;background:url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="50" cy="50" r="1" fill="%23ffffff" opacity="0.03"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');pointer-events:none }
  .past-events-title{ font-size:48px;font-weight:700;margin:0;text-align:center;position:relative;z-index:2;text-shadow:0 2px 10px rgba(0,0,0,.3);background:linear-gradient(270deg,#000,var(--brand),#000);background-size:600% 600%;-webkit-background-clip:text;-webkit-text-fill-color:transparent;animation:gradientShift 5s ease infinite }
  @keyframes gradientShift{0%{background-position:0% 50%}50%{background-position:100% 50%}100%{background-position:0% 50%}}
  .past-events-title::after{ content:'';position:absolute;bottom:-10px;left:50%;transform:translateX(-50%);width:100px;height:3px;background:linear-gradient(90deg,var(--brand),var(--brand-2));border-radius:2px }
  .past-events-grid{ display:grid;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));gap:20px;margin-top:60px;position:relative;z-index:2 }
  .past-event-item{ position:relative;border-radius:15px;overflow:hidden;background:#333;transition:transform .3s, box-shadow .3s }
  .past-event-item:hover{ transform:translateY(-5px);box-shadow:0 15px 35px rgba(231,55,1,.3) }
  .past-event-link{ display:block;position:relative }
  .past-event-image-container{ position:relative;width:100%;height:200px;overflow:hidden }
  .past-event-image{ width:100%;height:100%;object-fit:cover;transition:transform .4s }
  .past-event-item:hover .past-event-image{ transform:scale(1.1) }
  .past-event-overlay{ position:absolute;inset:0;background:linear-gradient(135deg,rgba(231,55,1,.8),rgba(255,85,46,.8));opacity:0;display:flex;align-items:center;justify-content:center;transition:opacity .3s }
  .past-event-item:hover .past-event-overlay{ opacity:1 }
  .past-event-zoom-icon{ transform:scale(.8);transition:transform .3s }
  .past-event-item:hover .past-event-zoom-icon{ transform:scale(1) }
  .past-events-btn{ background:linear-gradient(135deg,var(--brand),var(--brand-2));color:#fff;padding:15px 35px;border-radius:50px;font-weight:600;font-size:16px;display:inline-flex;align-items:center;transition:all .3s;border:none;box-shadow:0 5px 20px rgba(231,55,1,.3) }
  .past-events-btn:hover{ background:linear-gradient(135deg,#d63301,#e64a2e);transform:translateY(-2px);box-shadow:0 8px 25px rgba(231,55,1,.4);color:#fff }

  .gallery-slider-unique .swiper-slide{ height:500px;border-radius:18px;overflow:hidden;display:flex;align-items:center;justify-content:center;background:#000 }
  .gallery-slider-unique .slide-link{ width:100%;height:100%;display:flex }
  .gallery-slider-unique .slide-img{ width:100%;height:100%;object-fit:contain;border-radius:18px;background:#000 }
  .gallery-slider-unique .custom-swiper-btn{ position:absolute;top:50%;transform:translateY(-50%);width:48px;height:48px;border-radius:50%;display:flex;align-items:center;justify-content:center;background:rgba(231,55,1,.95);z-index:20;cursor:pointer;transition:transform .2s, background .3s }
  .gallery-slider-unique .custom-swiper-btn:hover{ transform:translateY(-50%) scale(1.1);background:rgba(200,30,0,1) }
  .gallery-slider-unique .gallery-prev{ left:8px } .gallery-slider-unique .gallery-next{ right:8px }
  @media (max-width:768px){ .gallery-slider-unique .swiper-slide{ height:320px } }

  .btn-sm,.btn-group-sm>.btn{ padding:.25rem .5rem;font-size:16px;line-height:1.5;border-radius:.2rem }
  @media (prefers-reduced-motion:reduce){ .gallery-track,.sponsor-track{ animation:none } }
  .rd-navbar-static .rd-nav-link{ text-decoration: none; }
  a, a:hover, a:focus, a:active, a:visited, a:focus-visible { text-decoration: none !important; }
  a { color: rgba(var(--bs-link-color-rgb), var(--bs-link-opacity, 1)); text-decoration: none; }
</style>
@endpush

@if ($home_slider)
<section class="section section-swiper-absoulte context-dark text-center">
  <div class="section-swiper-content">
    <div class="container">
      <div class="row justify-content-lg-center">
        <div class="col-lg-10 homecontent">
          @php
            $title = $home_slider->title[$locale] ?? '';
            $description = $home_slider->description[$locale] ?? '';
          @endphp

          @if($description)
            <h4 class="text-spacing-200 mb-3">{{ $description }}</h4>
          @endif

          @if($title)
            <h1 class="responsive-title fw-bold" style="letter-spacing:-.5px;">{{ $title }}</h1>
          @endif

          <ul class="list-inline list-inline-md mt-4">
            <li>
              <div class="unit unit-spacing-xs align-items-center">
                <div class="unit-left line-height-reset">
                  <svg class="svg-icon-sm svg-icon-primary" role="img" aria-hidden="true">
                    <use xlink:href="{{ asset('public/web/assets/images/svg/sprite.svg#small-calendar') }}"></use>
                  </svg>
                </div>
                <div class="unit-body">
                  <h5 class="text-spacing-100 m-0">
                    <span class="big">
                      <time datetime="{{ $event->event_date }}">{{ $rangeText }}@if($timeText) | {{ $timeText }} @endif</time>
                    </span>
                  </h5>
                </div>
              </div>
            </li>
            <li>
              <div class="unit unit-spacing-xs align-items-center">
                <div class="unit-left line-height-reset">
                  <svg class="svg-icon-sm svg-icon-primary" role="img" aria-hidden="true">
                    <use xlink:href="{{ asset('public/web/assets/images/svg/sprite.svg#earth-globe') }}"></use>
                  </svg>
                </div>
                <div class="unit-body">
                  <h5 class="text-spacing-100 m-0"><span class="big">{{ $event->location ?? '' }}</span></h5>
                </div>
              </div>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </div>

  <div class="swiper-container swiper-slider swiper-slider-1" data-loop="true" data-simulate-touch="false" data-autoplay="8500" data-direction="horizontal" data-effect="fade">
    <div class="swiper-wrapper">
      @if ($home_slider->media_type === 'image' && !empty($home_slider->media_path))
        <div class="swiper-slide with-dark-overlay" data-slide-bg="{{ asset('public/'.$home_slider->media_path) }}"></div>
        <noscript>
          <img src="{{ asset('public/'.$home_slider->media_path) }}" alt="hero" style="width:100%;height:auto;display:block;" loading="lazy" decoding="async" fetchpriority="low"/>
        </noscript>
      @elseif($home_slider->media_type === 'video' && !empty($home_slider->media_path))
        <div class="swiper-slide">
          <video autoplay muted loop playsinline preload="metadata" style="width:100%; height:min(100svh,100vh); object-fit:cover; display:block;">
            <source src="{{ asset('public/'.$home_slider->media_path) }}" type="video/mp4">
            {{ __('home_sections.your_browser_does_not_support_video') }}
          </video>
        </div>
      @endif
    </div>
  </div>
</section>
@endif

@php
  use App\Models\HomeSection;
  $sectionvote = HomeSection::find(10);
@endphp

@foreach ($sections as $section)
  @switch($section->id)
    @case(2)
      @include('web.content.sections.promo', ['section' => $section])
      @break
    @case(3)
      @include('web.content.sections.about', ['section' => $section, 'event' => $event])
      @break
    @case(4)
      @include('web.content.sections.speaker', ['section' => $section, 'speakers' => $speakers])
      @break
    @case(5)
      @include('web.content.sections.schedule', ['section' => $section, 'schedules' => $schedules])
      @break
    @case(6)
      @include('web.content.sections.floor')
      @include('web.content.sections.gallery', ['section' => $section, 'gallieries' => $gallieries])
      @break
    @case(7)
      @include('web.content.sections.sponsor', ['section' => $section, 'sponsors' => $sponsors])
      @break
    @case(8)
      @include('web.content.sections.blog', ['section' => $section, 'blogs' => $blogs])
      @break
    @case(9)
      @include('web.content.sections.promo2', ['section' => $section])
      @break
    @case(10)
      @if($sectionvote && $sectionvote->is_active==1)
        @include('web.content.sections.voting', ['section' => $section])
      @endif
      @break
    @case(11)
      @php
        $stats = $stats ?? [
          ['label' => __('Attendance'), 'value' => 30000, 'suffix' => '+'],
          ['label' => __('Speakers'),   'value' => 250,   'suffix' => '+'],
          ['label' => __('Sponsors'),   'value' => 300,   'suffix' => '+'],
        ];
      @endphp
      <section class="modern-stats-section" aria-label="Statistics">
        <div class="container modern-stats-inner">
          @foreach($stats as $stat)
            <div class="stat-card">
              <div class="stat-icon" aria-hidden="true" role="img" aria-label="check icon">
                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                  <circle cx="12" cy="12" r="10" stroke="var(--brand-dark)" stroke-width="2" />
                  <path d="M6 15L10 9L14 13L18 7" stroke="var(--brand-dark)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
              </div>
              <h3 class="stat-number" data-target="{{ $stat['value'] }}">0</h3>
              @if(!empty($stat['suffix']))
                <span class="stat-suffix">{{ $stat['suffix'] }}</span>
              @endif
              <p class="stat-label">{{ $stat['label'] }}</p>
            </div>
          @endforeach
        </div>
      </section>
      @include('web.content.sections.special', ['section' => $section, 'special_guests' => $special_guests])
      @break
  @endswitch
@endforeach

@once
<script src="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.js" defer></script>

{{-- Meta Pixel only if configured --}}
@if($hasPixel)
<!-- Meta Pixel Code -->
<script>
  !function(f,b,e,v,n,t,s)
  {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
  n.callMethod.apply(n,arguments):n.queue.push(arguments)};
  if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
  n.queue=[];t=b.createElement(e);t.defer=true;t.src=v;s=b.getElementsByTagName(e)[0];
  s.parentNode.insertBefore(t,s)}(window, document,'script','https://connect.facebook.net/en_US/fbevents.js');
  fbq('init', '{{ $pixelId }}');
</script>
<noscript><img height="1" width="1" style="display:none" src="https://www.facebook.com/tr?id={{ $pixelId }}&ev=PageView&noscript=1"/></noscript>
<!-- End Meta Pixel Code -->
@endif

<script>
  document.addEventListener('DOMContentLoaded', function(){
    // counters
    const statCards = document.querySelectorAll('.stat-card');
    if (statCards.length){
      const io = new IntersectionObserver((entries, obs) => {
        entries.forEach(entry => {
          if(entry.isIntersecting){
            const counter = entry.target.querySelector('.stat-number');
            const target = +counter.getAttribute('data-target');
            let startTs = null; const duration = 1500;
            const step = ts => {
              if(!startTs) startTs = ts;
              const p = Math.min((ts - startTs)/duration, 1);
              const eased = 1 - Math.pow(1 - p, 3);
              counter.textContent = Math.floor(target * eased).toLocaleString();
              if (p < 1) requestAnimationFrame(step); else counter.textContent = target.toLocaleString();
            };
            requestAnimationFrame(step);
            obs.unobserve(entry.target);
          }
        });
      }, { root:null, rootMargin:'120px', threshold:0.25 });
      statCards.forEach(c => io.observe(c));
    }

    // gallery sliders
    if (document.querySelector('.gallery-slider-unique .uniqueGallerySwiper')){
      new Swiper('.gallery-slider-unique .uniqueGallerySwiper', {
        loop:true, spaceBetween:24, speed:800, grabCursor:true, centeredSlides:true,
        autoplay: (window.matchMedia('(prefers-reduced-motion: reduce)').matches ? false : { delay:3000, disableOnInteraction:false }),
        slidesPerView:1,
        navigation:{ nextEl:'.gallery-slider-unique .gallery-next', prevEl:'.gallery-slider-unique .gallery-prev' }
      });
    }

    if (document.querySelector('.gallerySwiper')){
      new Swiper('.gallerySwiper', {
        loop:true, spaceBetween:20, speed:700, grabCursor:true,
        navigation:{ nextEl:'.button-next', prevEl:'.button-prev' },
        breakpoints:{ 320:{slidesPerView:1}, 576:{slidesPerView:2}, 768:{slidesPerView:3}, 992:{slidesPerView:4} }
      });
      try{
        if (typeof lightGallery === 'function'){
          lightGallery(document.querySelector('.gallerySwiper .swiper-wrapper'), {
            selector: 'a[data-lightgallery="item"]',
            plugins: (typeof lgZoom !== 'undefined' ? [lgZoom] : []),
            speed: 500,
          });
        }
      }catch(e){}
    }

    // ======= Meta Pixel + CAPI Tracking =======
    const getCookie = (name) => {
      const m = document.cookie.match('(^|;)\\s*' + name + '\\s*=\\s*([^;]+)');
      return m ? m.pop() : '';
    };
    const uuidv4 = () => {
      if (crypto?.randomUUID) return crypto.randomUUID();
      return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, c => {
        const r = Math.random()*16|0, v = c === 'x' ? r : (r&0x3|0x8);
        return v.toString(16);
      });
    };

    const payloadBase = {
      fbp: getCookie('_fbp') || undefined,
      fbc: getCookie('_fbc') || undefined,
      @if(!empty($testCode))
      test_event_code: '{{ $testCode }}',
      @endif
    };

    // 1) PageView
    const pvId = uuidv4();
    @if($hasPixel)
      try { fbq('track', 'PageView', {}, {eventID: pvId}); } catch(e){}
    @endif
    fetch('{{ url('/api/meta/capi') }}', {
      method: 'POST',
      headers: { 'Content-Type':'application/json', 'Accept':'application/json' },
      body: JSON.stringify(Object.assign({}, payloadBase, {
        event_id: pvId,
        event_name: 'PageView'
      }))
    }).catch(()=>{});

    // 2) ViewContent
    const vcId = uuidv4();
    @if($hasPixel)
      try { fbq('track', 'ViewContent', {
        content_name: @json($event->title[$locale] ?? ($event->title ?? 'Event')),
        content_type: 'event',
      }, {eventID: vcId}); } catch(e){}
    @endif
    fetch('{{ url('/api/meta/capi') }}', {
      method: 'POST',
      headers: { 'Content-Type':'application/json', 'Accept':'application/json' },
      body: JSON.stringify(Object.assign({}, payloadBase, {
        event_id: vcId,
        event_name: 'ViewContent',
        content_name: @json($event->title[$locale] ?? ($event->title ?? 'Event')),
        value: 0,
        currency: 'USD'
      }))
    }).catch(()=>{});
  });
</script>
@endonce
@endsection
