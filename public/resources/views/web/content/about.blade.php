@extends('web.layouts.app')

@section('content')
@php
  use Carbon\Carbon;

  // نضمن الـ $locale
  $locale = $locale ?? app()->getLocale();

  // نكيّب $sections لو جات كـ Collection عادية
  $sec = collect($sections ?? [])->keyBy('id');

  // نتوقع إن الكنترولر مرّر:
  // $aboutSection, $schdule_section, $speakers, $sponsors, $gallieries, $ads, $stats
@endphp

<style>
  html, body { overflow-x:hidden; }

  /* ====== Speaker Card (كما هو مع تحسينات طفيفة) ====== */
  .speaker{ position:relative;border-radius:12px;overflow:hidden;box-shadow:0 10px 30px rgba(0,0,0,.1);
            display:flex;flex-direction:column;cursor:pointer;height:100%;background:#fff;transition:.3s;border:1px solid #e0e0e0;z-index:0; }
  .speaker::before{ content:"";position:absolute;inset:8px;border-radius:10px;border:2px solid rgba(204,37,46,.4);opacity:0;transition:.3s;pointer-events:none;z-index:1; }
  .speaker:hover{ transform:translateY(-6px);box-shadow:0 20px 40px rgba(204,37,46,.2); }
  .speaker:hover::before{ opacity:1; }
  .speaker-img{ background:#fff;padding:15px;display:flex;justify-content:center;align-items:center;overflow:hidden; }
  .speaker-img img{ width:100%;height:auto;object-fit:contain;border-radius:10px;transition:.3s;display:block; }
  .speaker:hover .speaker-img img{ transform:scale(1.05); }
  .speaker-info{ background:#f7f7f7;padding:20px 15px;flex-grow:1;display:flex;flex-direction:column;justify-content:center;text-align:center; }
  .speaker-title{ font-weight:700;font-size:1.5rem;color:#222;margin-bottom:8px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis; }
  .speaker-position{ color:#666;font-size:1rem;margin-bottom:12px;word-wrap:break-word; }
  .speaker-social-list{ list-style:none;padding:0;margin:0 auto;display:flex;justify-content:center;gap:15px;margin-top:auto; }
  .speaker-social-list li a{ color:#cc252e;font-size:18px;transition:color .3s; }
  .speaker-social-list li a:hover{ color:#e84b3a; }
  @media (max-width: 991.98px){ .speaker-img{padding:10px} .speaker-info{padding:0}
    .speaker-title{font-size:1.2rem;white-space:normal} .speaker-position{font-size:.9rem} }
  @media (max-width:576px){ .speaker-img{padding:8px} .speaker-title{font-size:1rem} .speaker-position{font-size:.85rem} }

  /* ====== Stats ====== */
  .modern-stats-section{ background:#FFF;padding:40px 0; }
  .modern-stats-inner{ display:flex;justify-content:center;gap:40px;flex-wrap:wrap;max-width:1080px;margin:0 auto;padding:0 15px; }
  .stat-card{ background:linear-gradient(145deg,#FFF 0%,#FFF 50%, rgba(231,55,1,.3) 100%);
    border:2px solid #fff;border-radius:20px;width:280px;min-height:170px;padding:30px 25px;text-align:center;
    box-shadow:0 8px 25px rgba(0,0,0,.3);transition:.3s;display:flex;flex-direction:column;justify-content:center;align-items:center; }
  .stat-card:hover{ transform:translateY(-10px);box-shadow:0 15px 45px rgba(231,55,1,.6); }
  .stat-number{ font-size:60px;font-weight:900;line-height:1.2;margin-bottom:10px;
    background:linear-gradient(270deg,#000,#E73701,#000);background-size:600% 600%;
    -webkit-background-clip:text;-webkit-text-fill-color:transparent;animation:gradientShift 5s ease infinite; }
  .stat-suffix{ font-size:28px;font-weight:700;color:#cc252e;margin-left:6px;vertical-align:super; }
  .stat-label{ margin-top:12px;font-size:18px;font-weight:600;color:#000;text-transform:uppercase;letter-spacing:1.5px; }
  @keyframes gradientShift{0%{background-position:0% 50%}50%{background-position:100% 50%}100%{background-position:0% 50%}}
  @media (max-width:992px){ .stat-card{width:240px;padding:25px 20px} .stat-number{font-size:46px} .stat-suffix{font-size:24px} }
  @media (max-width:576px){
    .modern-stats-inner{ gap:20px;display:grid;grid-template-columns:repeat(2,1fr);justify-items:center; }
    .modern-stats-inner .stat-card:nth-child(1){ grid-column:span 2;justify-self:center; }
    .stat-card{ aspect-ratio:1/1;width:100%;max-width:160px;padding:12px 10px;border-radius:16px; }
    .stat-number{ font-size:28px;margin-bottom:4px } .stat-suffix{ font-size:16px;margin-left:4px } .stat-label{ font-size:12px;letter-spacing:.5px }
  }

  /* ====== Tabs/Schedule ====== */
  .past-events-title{ color:#E73701;font-size:48px;font-weight:700;margin:0;text-align:center; }

  /* ====== Gallery Slider ====== */
  .gallery-slider-unique .swiper-slide{ height:500px;border-radius:18px;overflow:hidden;display:flex;align-items:center;justify-content:center;background:#000; }
  .gallery-slider-unique .slide-img{ width:100%;height:100%;object-fit:contain;border-radius:18px;background:#000; }
  .gallery-slider-unique .custom-swiper-btn{
    position:absolute;top:50%;transform:translateY(-50%);width:48px;height:48px;border-radius:50%;display:flex;align-items:center;justify-content:center;
    background:rgba(231,55,1,.95);z-index:20;cursor:pointer;transition:transform .2s, background .3s; }
  .gallery-slider-unique .custom-swiper-btn:hover{ transform:translateY(-50%) scale(1.1);background:rgba(200,30,0,1) }
  .gallery-slider-unique .gallery-prev{ left:8px } .gallery-slider-unique .gallery-next{ right:8px }
  @media (max-width:768px){ .gallery-slider-unique .swiper-slide{ height:320px } }

  /* ====== Skeleton (مشترك) ====== */
  .skeleton-wrap{ position:relative; overflow:hidden; border-radius:10px; }
  .skeleton-bg{ position:absolute; inset:0; background:#e9ecef; }
  .skeleton-shimmer{
    position:absolute; inset:0;
    background:linear-gradient(90deg,#e9ecef 0%,#f5f6f7 40%,#e9ecef 80%);
    background-size:200% 100%;
    animation:skeletonMove 1.2s infinite linear;
  }
  @keyframes skeletonMove{ 0%{background-position:200% 0} 100%{background-position:-200% 0} }
  .lazy-swap.is-loaded ~ .skeleton-bg,
  .lazy-swap.is-loaded ~ .skeleton-shimmer{ display:none; }
  .fade-in{ opacity:0; transition:opacity .35s ease } .fade-in.show{ opacity:1 }
</style>

{{-- ====== Breadcrumbs ====== --}}
<section class="breadcrumbs-custom bg-image context-dark"
         style="background-image:url({{ asset('public/web/assets/images/bg-breadcrumbs-01-1894x424.jpg') }});">
  <div class="container"><h3 class="breadcrumbs-custom-title pt-5">About</h3></div>
</section>

{{-- ====== About + Ads (بدون DB داخل Blade) ====== --}}
<section class="container-fluid section section-lg bg-default mt-5 mb-5">
  <div class="row">
    <div class="{{ !empty($ads) && count($ads) ? 'col-lg-8' : 'col-12' }} col-md-12">
      <div class="container">
        <div class="row align-items-center">
          <div class="col-md-6 mb-4 mb-md-0">
            <h3>{{ $aboutSection->title[$locale] ?? '' }}</h3>
            <p style="color:#000;">{{ $aboutSection->description[$locale] ?? '' }}</p>
          </div>
          <div class="col-md-6">
            <div class="img-separated">
              @php
                $aboutMedia = !empty($aboutSection?->media_path) ? asset('public/'.ltrim($aboutSection->media_path,'/')) : null;
                // Placeholder SVG
                $placeholder = 'data:image/svg+xml;utf8,' . rawurlencode('<svg xmlns="http://www.w3.org/2000/svg" width="800" height="500"><rect width="100%" height="100%" fill="#e9ecef"/></svg>');
                $thumb = !empty($aboutSection?->thumbnail) ? asset('public/'.ltrim($aboutSection->thumbnail,'/')) : $placeholder;
              @endphp

              @if (($aboutSection->media_type ?? null) === 'image' && $aboutMedia)
                <div class="skeleton-wrap" style="width:100%;max-width:100%;">
                  <img class="lazy-swap"
                       src="{{ $placeholder }}"
                       data-src="{{ $aboutMedia }}"
                       alt="About Image"
                       decoding="async"
                       fetchpriority="low"
                       style="max-width:100%;height:auto;border-radius:8px;object-fit:cover;">
                  <span class="skeleton-bg" aria-hidden="true"></span>
                  <span class="skeleton-shimmer" aria-hidden="true"></span>
                </div>
                <noscript><img src="{{ $aboutMedia }}" alt="About Image" class="img-fluid rounded shadow"></noscript>

              @elseif (($aboutSection->media_type ?? null) === 'video' && $aboutMedia)
                {{-- Poster أولاً، والفيديو مؤجل بالكامل --}}
                <img id="aboutPoster" src="{{ $thumb }}" alt="About Poster"
                     style="width:100%;height:auto;object-fit:cover;border-radius:8px;">
                <video id="aboutVideo" class="fade-in" playsinline muted loop preload="metadata"
                       poster="{{ $thumb }}"
                       style="width:100%;height:auto;object-fit:cover;border-radius:8px;display:none;"></video>
                <script>
                  (function(){
                    function initLazyAboutVideo(){
                      const video  = document.getElementById('aboutVideo');
                      const poster = document.getElementById('aboutPoster');
                      if (!video || !poster) return;

                      const saveData = (navigator.connection && navigator.connection.saveData) ? true : false;
                      const reduce   = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
                      if (saveData || reduce) return;

                      const io = new IntersectionObserver((entries, obs) => {
                        entries.forEach(entry => {
                          if (entry.isIntersecting) {
                            const src = @json($aboutMedia);
                            const source = document.createElement('source');
                            source.src = src; source.type = 'video/mp4';
                            video.appendChild(source);
                            video.load();
                            const onCanPlay = () => {
                              poster.style.display = 'none';
                              video.style.display  = 'block';
                              requestAnimationFrame(()=>video.classList.add('show'));
                              const p = video.play(); if (p && p.catch) p.catch(()=>{});
                              video.removeEventListener('canplay', onCanPlay);
                            };
                            video.addEventListener('canplay', onCanPlay);
                            obs.unobserve(video);
                          }
                        });
                      }, { root:null, rootMargin:'150px', threshold:0.1 });

                      io.observe(video);
                    }
                    if (document.readyState === 'complete'){
                      initLazyAboutVideo();
                    } else {
                      window.addEventListener('load', initLazyAboutVideo, { once:true });
                    }
                  })();
                </script>
              @endif
            </div>
          </div>
        </div>
      </div>
    </div>

    @if(!empty($ads) && count($ads))
      <div class="col-lg-4 col-md-12 d-flex flex-column" style="border-left:1px solid #ddd; padding-left:25px;">
        <aside class="p-3">
          @foreach($ads as $ad)
            @php
              $adImg = !empty($ad->img) ? asset($ad->img) : null;
              $placeholderAd = 'data:image/svg+xml;utf8,' . rawurlencode('<svg xmlns="http://www.w3.org/2000/svg" width="800" height="300"><rect width="100%" height="100%" fill="#e9ecef"/></svg>');
            @endphp
            <div class="ad-box mb-4 shadow-sm border rounded overflow-hidden">
              <a href="{{ $ad->link ?? '#' }}" target="_blank" rel="noopener">
                <div class="skeleton-wrap" style="width:100%;max-height:300px;">
                  <img class="lazy-swap"
                       src="{{ $placeholderAd }}"
                       @if($adImg) data-src="{{ $adImg }}" @endif
                       alt="Ad Image"
                       class="img-fluid w-100"
                       style="object-fit:cover;width:100%;max-height:300px;">
                  <span class="skeleton-bg" aria-hidden="true"></span>
                  <span class="skeleton-shimmer" aria-hidden="true"></span>
                </div>
              </a>
            </div>
          @endforeach
        </aside>
      </div>
    @endif
  </div>
</section>

{{-- ====== Speakers ====== --}}
@if(isset($sec[4]))
  @include('web.content.sections.speaker', ['section' => $sec[4], 'speakers' => $speakers])
@endif

{{-- ====== Stats ====== --}}
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
            <circle cx="12" cy="12" r="10" stroke="#E73701" stroke-width="2" />
            <path d="M6 15L10 9L14 13L18 7" stroke="#E73701" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
          </svg>
        </div>
        <h3 class="stat-number" data-target="{{ $stat['value'] }}">0</h3>
        @if(!empty($stat['suffix'])) <span class="stat-suffix">{{ $stat['suffix'] }}</span> @endif
        <p class="stat-label">{{ $stat['label'] }}</p>
      </div>
    @endforeach
  </div>
</section>

{{-- ====== Schedule (Tabs) ====== --}}
@php
  $groupedSchedules = [];
  foreach ($schedules as $schedule) {
    $date = Carbon::parse($schedule->start_datetime)->format('Y-m-d');
    $groupedSchedules[$date][] = $schedule;
  }
  ksort($groupedSchedules);

  $navClasses = ['nav-link-secondary-darker','nav-link-purple-heart','nav-link-primary','nav-link-secodanry'];
  $dayNames   = [__('First Day'), __('Second Day'), __('Third Day'), __('Fourth Day')];
@endphp

<section class="section section-lg bg-default text-center">
  <div class="container">
    <h6>{{ $schdule_section->title[$locale] ?? '' }}</h6>
    <h3 class="mt-3 gre-title">{{ $schdule_section->description[$locale] ?? 'Event Agenda' }}</h3>

    <div class="tabs-custom tabs-horizontal tabs-corporate" id="tabs-1">
      <ul class="nav nav-tabs" id="scheduleTabs" role="tablist">
        @foreach ($groupedSchedules as $date => $daySchedules)
          @php
            $index = $loop->index;
            $class = $navClasses[$index % count($navClasses)];
            $dayLabel = $dayNames[$index] ?? __('Day').' '.($index + 1);
            $tabId = 'tabs-1-'.$index;
          @endphp
          <li class="nav-item" role="presentation">
            <a class="nav-link {{ $class }} {{ $loop->first ? 'active' : '' }}"
               href="#{{ $tabId }}" data-toggle="tab" role="tab" data-triangle=".nav-link-overlay">
              <span class="nav-link-overlay"></span>
              <span class="nav-link-cite">{{ $dayLabel }}</span>
              <span class="nav-link-title">{{ Carbon::parse($date)->translatedFormat('j F Y') }}</span>
            </a>
          </li>
        @endforeach
      </ul>

      <div class="tab-content wow fadeIn">
        @foreach ($groupedSchedules as $date => $daySchedules)
          @php $tabId = 'tabs-1-'.$loop->index; @endphp
          <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}" id="{{ $tabId }}">
            <div class="card-group-custom card-group-corporate" role="tablist">
              @foreach ($daySchedules as $schedule)
                @php
                  $logo = !empty($schedule->logo) ? asset('public/'.$schedule->logo) : null;
                  $phLogo = 'data:image/svg+xml;utf8,' . rawurlencode('<svg xmlns="http://www.w3.org/2000/svg" width="122" height="122"><rect width="100%" height="100%" fill="#e9ecef"/></svg>');
                @endphp
                <article class="card card-custom card-corporate">
                  <div class="card-header" role="tab">
                    <div class="card-title">
                      <a class="collapsed" data-toggle="collapse" href="#collapse-{{ $schedule->id }}" aria-expanded="false" role="button">
                        <span class="schedule-classic">
                          <span class="unit unit-spacing-md align-items-center d-block d-md-flex">
                            <span class="unit-left">
                              <span class="schedule-classic-img skeleton-wrap" style="width:122px;height:122px;border-radius:8px;overflow:hidden;">
                                <img class="lazy-swap" src="{{ $phLogo }}" @if($logo) data-src="{{ $logo }}" @endif alt="" width="122" height="122" style="object-fit:contain;border-radius:8px;">
                                <span class="skeleton-bg" aria-hidden="true"></span>
                                <span class="skeleton-shimmer" aria-hidden="true"></span>
                              </span>
                            </span>
                            <span class="unit-body">
                              <span class="schedule-classic-content">
                                <span class="schedule-classic-time">
                                  {{ Carbon::parse($schedule->start_datetime)->format('h:i A') }} to
                                  {{ Carbon::parse($schedule->end_datetime)->format('h:i A') }}
                                </span>
                                <span class="schedule-classic-title heading-4">{{ $schedule->{'title_'.$locale} }}</span>
                                <span class="schedule-classic-author" style="color:#000;">{{ $schedule->{'description_'.$locale} }}</span>
                              </span>
                            </span>
                          </span>
                        </span>
                      </a>
                    </div>
                  </div>
                  <div class="collapse" id="collapse-{{ $schedule->id }}">
                    <div class="card-body">
                      <p style="color:#000;">{{ $schedule->{'description_'.$locale} }}</p>
                      <div class="unit unit-spacing-xxs">
                        <div class="unit-left">
                          <svg class="svg-icon-sm svg-icon-primary" role="img">
                            <use xlink:href="{{ asset('public/images/svg/sprite.svg#earth-globe') }}"></use>
                          </svg>
                        </div>
                        <div class="unit-body">
                          <h5>{{ __('Where') }}</h5>
                          <p class="font-secondary">{{ $schedule->{'location_'.$locale} }}</p>
                        </div>
                      </div>
                    </div>
                  </div>
                </article>
              @endforeach
            </div>
          </div>
        @endforeach

        <div class="text-center">
          <a class="button button-secondary box-with-triangle-right wow fadeScale mt-2"
             href="{{ route('web.schdule') }}" data-triangle=".button-overlay">
            <span>More Schdule</span>
            <span class="button-overlay"></span>
          </a>
        </div>
      </div>
    </div>
  </div>
</section>

{{-- ====== Sponsors (include جاهز — سكشنك المحسّن بالرعاة) ====== --}}
@if(isset($sec[7]))
  @include('web.content.sections.sponsor', ['section' => $sec[7], 'sponsors' => $sponsors])
@endif

{{-- ====== Gallery ====== --}}
@if(isset($sec[6]))
  @include('web.content.sections.gallery', ['section' => $sec[6], 'gallieries' => $gallieries])
@endif

{{-- ====== Voting ====== --}}
@if(isset($sec[10]) && ($sec[10]->is_active ?? false))
  @include('web.content.sections.voting')
@endif

{{-- ====== Swiper & Counters ====== --}}
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.css"/>
<script src="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.js"></script>

<script>
  // Counters (IO)
  document.addEventListener('DOMContentLoaded', function () {
    const statCards = document.querySelectorAll('.stat-card');
    const observer = new IntersectionObserver((entries, obs) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          const card = entry.target;
          if (card.classList.contains('animated')) return;
          card.classList.add('animated');
          const counter = card.querySelector('.stat-number');
          const target = +counter.getAttribute('data-target');
          const duration = 1500; const start = performance.now();
          const step = (now) => {
            const p = Math.min((now - start)/duration, 1);
            const eased = 1 - Math.pow(1 - p, 3);
            counter.textContent = Math.floor(target * eased).toLocaleString();
            if (p < 1) requestAnimationFrame(step); else counter.textContent = target.toLocaleString();
          };
          requestAnimationFrame(step);
          obs.unobserve(card);
        }
      });
    }, { threshold: 0.35 });
    statCards.forEach(c => observer.observe(c));
  });

  // Swiper safe init
  document.addEventListener('DOMContentLoaded', function () {
    const uniqueEl = document.querySelector('.gallery-slider-unique .uniqueGallerySwiper');
    if (uniqueEl) {
      new Swiper(uniqueEl, {
        loop:true, spaceBetween:24, speed:800, grabCursor:true, centeredSlides:true,
        autoplay:{ delay:3000, disableOnInteraction:false }, slidesPerView:1,
        navigation:{ nextEl:'.gallery-slider-unique .gallery-next', prevEl:'.gallery-slider-unique .gallery-prev' }
      });
    }
    const galleryEl = document.querySelector('.gallerySwiper');
    if (galleryEl) {
      new Swiper(galleryEl, {
        loop:true, spaceBetween:24, speed:800, grabCursor:true,
        navigation:{ nextEl:'.swiper-button-next', prevEl:'.swiper-button-prev' },
        breakpoints:{ 0:{slidesPerView:1}, 576:{slidesPerView:2}, 768:{slidesPerView:3}, 992:{slidesPerView:4} }
      });
      if (window.lightGallery && document.querySelector('.gallerySwiper .swiper-wrapper')) {
        const opts = { selector: 'a[data-lightgallery="item"]', speed: 500 };
        if (window.lgZoom) opts.plugins = [lgZoom];
        lightGallery(document.querySelector('.gallerySwiper .swiper-wrapper'), opts);
      }
    }
  });

  // ====== Lazy loader (صور about/ads/schedule) — يبدأ بعد اكتمال الصفحة ======
  (function(){
    function bootLazySwap(){
      const imgs = document.querySelectorAll('img.lazy-swap[data-src]');
      if (!imgs.length) return;
      const hasIO = 'IntersectionObserver' in window;

      const loadImg = (img) => {
        const real = img.getAttribute('data-src');
        if (!real) return;
        img.onload  = () => img.classList.add('is-loaded');
        img.onerror = () => img.classList.add('is-loaded');
        img.src = real;
        img.removeAttribute('data-src');
      };

      if (hasIO) {
        const io = new IntersectionObserver((entries, obs) => {
          entries.forEach(entry => {
            if (entry.isIntersecting) { loadImg(entry.target); obs.unobserve(entry.target); }
          });
        }, { root:null, rootMargin:'150px', threshold:0.1 });
        imgs.forEach(img => io.observe(img));
      } else {
        // fallback تدريجي
        let i = 0; const step = () => {
          const img = imgs[i++]; if (!img) return;
          loadImg(img);
          if (i < imgs.length) (window.requestIdleCallback ? requestIdleCallback(step) : setTimeout(step, 16));
        }; step();
      }
    }
    if (document.readyState === 'complete') bootLazySwap();
    else window.addEventListener('load', bootLazySwap, { once:true });
  })();
</script>
@endsection
