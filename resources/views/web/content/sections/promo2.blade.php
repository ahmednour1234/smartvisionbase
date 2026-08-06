@if ($section && $section->media_path)
@php
    $locale   = app()->getLocale();
    $title    = $section->title[$locale] ?? 'Promo Title';

    // مسارات وسائط آمنة بدون /public
    $mediaSrc = asset(ltrim($section->media_path, '/'));
    $thumbSrc = $section->thumbnail ? asset(ltrim('public/'.$section->thumbnail, '/')) : null;
@endphp

<section id="home-promo" class="overflow-hidden" style="margin-top:-10px; background:#fff; position:relative;">
  {{-- ✅ العنوان --}}
  <div class="container text-center py-3">
    <h2 class="promo-title"
        style="
          font-size:60px; font-weight:900; line-height:1.2; margin-bottom:10px;
          background:linear-gradient(270deg,#000000,#E73701,#000000);
          background-size:600% 600%;
          -webkit-background-clip:text; -webkit-text-fill-color:transparent;
          animation:gradientShift 5s ease infinite;
        ">
      {{ $title }}
    </h2>
  </div>

  <style>
    @keyframes gradientShift {
      0%{background-position:0% 50%} 50%{background-position:100% 50%} 100%{background-position:0% 50%}
    }
    /* لا روابط تحتها خط */
    #home-promo a, #home-promo a:hover, #home-promo a:focus, #home-promo a:active{
      text-decoration:none !important; outline:none;
    }

    /* مقاسات وسائط الـ Hero */
    .hero-media{ width:100%; height:90vh; object-fit:cover; display:block }
    @media (max-width:768px){
      .hero-media{ height:50vh }
    }

    /* زر دائرة YouTube */
    .yt-play{
      position:absolute; inset:0; display:flex; align-items:center; justify-content:center; pointer-events:none;
    }
    .yt-btn{
      width:92px; height:92px; border-radius:50%;
      background:#ff0000; /* YouTube red */
      box-shadow:0 12px 28px rgba(0,0,0,.28), 0 2px 6px rgba(0,0,0,.18) inset, 0 0 0 6px rgba(255,255,255,.25);
      display:flex; align-items:center; justify-content:center;
      transform:scale(1); transition:transform .18s ease, box-shadow .18s ease;
      position:relative; pointer-events:auto; cursor:pointer;
    }
    .yt-btn::after{
      content:""; position:absolute; inset:-6px; border-radius:50%;
      box-shadow:0 0 0 0 rgba(255,0,0,.35); animation:ytPulse 1.6s ease-out infinite;
    }
    @keyframes ytPulse{
      0%{ box-shadow:0 0 0 0 rgba(255,0,0,.35) }
      70%{ box-shadow:0 0 0 16px rgba(255,0,0,0) }
      100%{ box-shadow:0 0 0 0 rgba(255,0,0,0) }
    }
    .yt-btn:hover{ transform:scale(1.04) }
    .yt-icon{
      width:0; height:0; border-left:20px solid #fff; border-top:12px solid transparent; border-bottom:12px solid transparent;
      margin-left:6px;
    }

    /* غلاف الصورة القابلة للنقر */
    .video-thumb-wrap{ position:relative; width:100%; height:90vh; cursor:pointer }
    .video-thumb-img{ width:100%; height:100%; object-fit:cover; display:block }
    @media (max-width:768px){
      .video-thumb-wrap{ height:50vh }
    }
  </style>

  {{-- ✅ نوع الوسائط --}}
  @if ($section->media_type === 'video')
    <video class="hero-media" autoplay muted loop playsinline>
      <source src="{{ $mediaSrc }}" type="video/mp4">
      {{ __('home_sections.your_browser_does_not_support_video') }}
    </video>

  @elseif($section->media_type === 'image')
    <img class="hero-media" src="{{  'public/'.$mediaSrc }}" alt="Promo">

  @elseif($section->media_type === 'link' && $thumbSrc)
    {{-- ✅ صورة + زر تشغيل دائرة YouTube --}}
    <div class="video-thumb-wrap" data-bs-toggle="modal" data-bs-target="#videoModal" aria-label="Play video">
      <img class="video-thumb-img" src="{{ $thumbSrc }}" alt="Video Thumbnail">
      <div class="yt-play">
        <div class="yt-btn" title="{{ __('تشغيل الفيديو') }}">
          <span class="yt-icon" aria-hidden="true"></span>
        </div>
      </div>
    </div>

    {{-- ✅ Modal --}}
    <div class="modal fade" id="videoModal" tabindex="-1" aria-labelledby="videoModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content bg-transparent border-0">
          <div class="modal-body p-0">
            <div class="ratio ratio-16x9">
              <iframe id="videoFrame"
                      src=""
                      title="Embedded Video"
                      frameborder="0"
                      allow="autoplay; fullscreen; picture-in-picture"
                      allowfullscreen></iframe>
            </div>
          </div>
        </div>
      </div>
    </div>

    {{-- ✅ Script --}}
    <script>
      document.addEventListener('DOMContentLoaded', function () {
        const modal  = document.getElementById('videoModal');
        const iframe = document.getElementById('videoFrame');
        const rawUrl = @json($section->media_path);

        function toEmbed(url){
          try{
            // Normalize
            const u = new URL(url, window.location.origin);

            // YouTube: watch?v=, youtu.be, shorts
            if (u.hostname.includes('youtube.com') || u.hostname.includes('youtu.be')){
              // watch?v=
              if (u.searchParams.get('v')){
                return 'https://www.youtube.com/embed/' + u.searchParams.get('v');
              }
              // youtu.be/<id>
              const parts = u.pathname.split('/').filter(Boolean);
              if (u.hostname.includes('youtu.be') && parts[0]){
                return 'https://www.youtube.com/embed/' + parts[0];
              }
              // /shorts/<id>
              if (u.pathname.startsWith('/shorts/') && parts[1]){
                return 'https://www.youtube.com/embed/' + parts[1];
              }
            }

            // Vimeo
            if (u.hostname.includes('vimeo.com')){
              const id = u.pathname.split('/').filter(Boolean).pop();
              if (id) return 'https://player.vimeo.com/video/' + id;
            }

            // Fallback
            return url;
          }catch(e){
            return url;
          }
        }

        modal.addEventListener('show.bs.modal', function () {
          iframe.src = toEmbed(rawUrl) + (toEmbed(rawUrl).includes('?') ? '&' : '?') + 'autoplay=1';
        });

        modal.addEventListener('hidden.bs.modal', function () {
          iframe.src = '';
        });
      });
    </script>
  @endif
</section>
@endif

{{-- ✅ Bootstrap 5 (لو مش محمِّله قبل كده) --}}
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
