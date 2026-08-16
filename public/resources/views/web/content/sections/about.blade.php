{{-- Section: Biggest 2019 Digital Conference --}}
@php
    /** @var \App\Models\HomeSection|null $section */
    $section = \App\Models\HomeSection::where('is_active', true)->where('id', 3)->first();

    $isVideo   = $section && $section->media_type === 'video';
    $isImage   = $section && $section->media_type === 'image';
    $thumbUrl  = $section && $section->thumbnail ? asset($section->thumbnail) : null;

    // مسار الميديا (داخلي أو خارجي)
    $mediaPath = $section ? $section->media_path : null;
    $mediaSrc  = $mediaPath
        ? (\Illuminate\Support\Str::startsWith($mediaPath, ['http://', 'https://']) ? $mediaPath : asset('public/'.$mediaPath))
        : null;

    $videoId   = $section ? ('home-section-video-'.$section->id) : null;
@endphp

@if ($section)
    {{-- ===== HEAD PRELOADS (لو فيديو) ===== --}}
    @push('styles')
        @if($isVideo && $mediaSrc)
            <link rel="preload" as="video" href="{{ $mediaSrc }}" type="video/mp4">
        @endif
    @endpush

    <section class="section section-lg bg-default wow fadeIn mt-5 mb-5">
        <div class="container">
            <div class="row row-30 justify-content-center">
                <div class="col-md-10 col-lg-6 col-xl-5">
                    <h3 class="heading-lg-postfix-15">{{ $section->title[app()->getLocale()] ?? '' }}</h3>
                    <p style="color:black;">{{ $section->description[app()->getLocale()] ?? '' }}</p>

                    <a class="button button-primary" href="{{ route('web.becomesponsor') }}" data-triangle=".button-overlay">
                        <span>Become Sponsor</span>
                        <span class="button-overlay"></span>
                    </a>
                </div>

                <div class="col-md-10 col-lg-6 col-xl-7 text-md-right">
                    <div class="images-box">
                        <div class="images-box-item images-box-item-right">
                            <div class="wow fadeScale">
                                @if ($isImage && $mediaSrc)
                                    <img
                                        src="{{ $mediaSrc }}"
                                        alt="About Image"
                                        style="max-width:100%;height:auto;display:block;border-radius:8px;"
                                        loading="eager" decoding="async" fetchpriority="high">
                                @elseif ($isVideo && $mediaSrc)
                                    <video
                                        id="{{ $videoId }}"
                                        class="home-section-video"
                                        autoplay
                                        muted
                                        loop
                                        playsinline
                                        preload="metadata"
                                        @if($thumbUrl) poster="{{ $thumbUrl }}" @endif
                                        style="width:100%;height:auto;object-fit:cover;display:block;border-radius:8px;">
                                        <source src="{{ $mediaSrc }}" type="video/mp4">
                                        {{ __('home_sections.your_browser_does_not_support_video') }}
                                    </video>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

            </div> {{-- /row --}}
        </div> {{-- /container --}}
    </section>

    {{-- ===== JS: تشغيل/إيقاف الفيديو عند الظهور ===== --}}
    @push('scripts')
    @once
    <script>
    (function () {
      function onReady(fn){
        if(document.readyState !== 'loading'){ fn(); }
        else{ document.addEventListener('DOMContentLoaded', fn, {once:true}); }
      }

      onReady(function(){
        var vids = document.querySelectorAll('.home-section-video');
        if(!('IntersectionObserver' in window) || !vids.length) return;

        var io = new IntersectionObserver(function(entries){
          entries.forEach(function(entry){
            var v = entry.target;
            try{
              if(entry.isIntersecting){ v.play && v.play().catch(function(){}); }
              else{ v.pause && v.pause(); }
            }catch(_){}
          });
        }, { threshold: 0.15 });

        vids.forEach(function(v){ io.observe(v); });
      });
    })();
    </script>
    @endonce
    @endpush
@endif
