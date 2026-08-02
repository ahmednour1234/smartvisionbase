@if ($section && $section->media_path)
@php
    $locale  = app()->getLocale();
    $title   = $section->title[$locale] ?? 'Promo Title';

    // مسارات موحّدة
    $mediaSrc = asset('public/' . ltrim($section->media_path, '/'));
    $thumbSrc = $section->thumbnail ? asset('public/' . ltrim($section->thumbnail, '/')) : null;

    // Poster افتراضي لو مفيش thumbnail
    $poster = $thumbSrc ?: ($section->media_type === 'image' ? $mediaSrc : asset('public/web/assets/images/black-1x1.png'));
@endphp

<section id="home-promo" class="overflow-hidden" style="margin-top:-10px; background:#fff; position:relative; padding-bottom:30px;">
  <div class="container">

    {{-- العنوان --}}
    <div class="text-center py-3">
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
      #home-promo a { text-decoration:none !important; outline:none; }

      .media-wrap{ width:100%; }
      .hero-media{ width:100%; height:auto; object-fit:contain; display:block; background:#000; }

      /* تأثير حدود متدرّجة لثَمبنايل اليوتيوب */
      .video-thumb-wrap{ position:relative; width:100%; cursor:pointer; }
      .video-thumb-img{ width:100%; height:auto; object-fit:contain; display:block; background:#000; }
      .gradient-border{ position:relative; border-radius:16px; padding:3px;
        background: conic-gradient(from 0deg, #cc252e, #fff, #cc252e);
        animation: gradRotate 6s linear infinite; box-shadow:0 8px 24px rgba(231,55,1,.18);
      }
      .gradient-border .video-thumb-img{ border-radius:13px; background:#000; }
      .yt-play{ position:absolute; inset:0; display:flex; align-items:center; justify-content:center; pointer-events:none; }
      .yt-btn{ width:92px; height:92px; border-radius:50%; background: rgba(0,0,0,0.9);
        box-shadow:0 12px 28px rgba(0,0,0,.28), 0 2px 6px rgba(0,0,0,.18) inset, 0 0 0 6px rgba(255,255,255,.9);
        display:flex; align-items:center; justify-content:center; position:relative;
      }
      .yt-icon{ width:0; height:0; border-left:20px solid #fff; border-top:12px solid transparent; border-bottom:12px solid transparent; margin-left:6px; }

      /* انتقال ناعم للفيديو بعد التحميل */
      .fade-in{ opacity:0; transition:opacity .35s ease; }
      .fade-in.show{ opacity:1; }

      @media (prefers-reduced-motion: reduce){
        .promo-title{ animation:none }
        .gradient-border{ animation:none }
      }
      @media (max-width: 768px) {
        #home-promo img { height: 20vh; object-fit:cover; }
        .gradient-border .video-thumb-img{ background:#fff; }
      }
    </style>

    <div class="media-wrap">
      @if ($section->media_type === 'video')
        {{-- 1) نعرض Poster فقط مبدئيًا --}}
        <img
          class="hero-media" id="promoPoster"
          src="{{ $poster }}"
          alt="Promo Poster"
          loading="lazy" decoding="async" fetchpriority="low"
          style="border-radius:8px;"
        >

        {{-- 2) عنصر الفيديو جاهز، لكن بدون تحميل (src يتحدد لاحقًا) --}}
        <video
          id="promoVideo"
          class="hero-media fade-in"
          playsinline muted loop preload="metadata"
          @if($poster) poster="{{ $poster }}" @endif
          style="border-radius:8px; display:none;"    loading="lazy" decoding="async" fetchpriority="low"
        >
          {{-- ما نحطش <source> هنا علشان ما يبدأش تحميل قبل window load --}}
        </video>

        {{-- 3) سكربت: بعد window load نحط الـ src ونظهر الفيديو --}}
        <script>
          (function(){
            // ننفّذ بعد اكتمال تحميل الصفحة كليًا
            window.addEventListener('load', function(){
              try {
                const video  = document.getElementById('promoVideo');
                const poster = document.getElementById('promoPoster');
                if (!video || !poster) return;

                // احترام وضع توفير البيانات/تقليل الحركة
                const prefersReduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
                const saveData = (navigator.connection && navigator.connection.saveData) ? true : false;
                if (saveData || prefersReduce) {
                  // في الوضع ده، نخلي Poster فقط ولا نحمّل الفيديو
                  return;
                }

                // نخلق <source> ونُسند src في اللحظة دي فقط
                const src = @json($mediaSrc);
                const sourceEl = document.createElement('source');
                sourceEl.src = src;
                sourceEl.type = 'video/mp4';
                video.appendChild(sourceEl);

                // نبدأ التحميل اليدوي ثم العرض عند الجاهزية
                video.load();

                // لما يبقى الفيديو قابل للتشغيل
                const onCanPlay = function(){
                  // نخفي البوستر ونظهر الفيديو بفيد-إن
                  poster.style.display = 'none';
                  video.style.display = 'block';
                  video.classList.add('show');

                  // Autoplay آمن (Muted + playsinline)
                  const p = video.play();
                  if (p && typeof p.catch === 'function') {
                    p.catch(() => { /* بعض المتصفحات قد ترفض، نتجاهل بهدوء */ });
                  }

                  // تنظيف الحدث
                  video.removeEventListener('canplay', onCanPlay);
                };
                video.addEventListener('canplay', onCanPlay);
              } catch(e) { /* no-op */ }
            });
          })();
        </script>

      @elseif($section->media_type === 'image')
        <img class="hero-media"
             src="{{ $mediaSrc }}"
             alt="Promo"
      loading="lazy" decoding="async" fetchpriority="low"
             style="border-radius:8px;">

      @elseif($section->media_type === 'link' && $thumbSrc)
        {{-- لينك خارجي (يوتيوب/فيميو): نحمل الإطار المضمّن فقط عند فتح المودال --}}
        <div class="video-thumb-wrap" data-bs-toggle="modal" data-bs-target="#videoModal" aria-label="Play video">
          <div class="gradient-border">
            <img class="video-thumb-img"
                 src="{{ $thumbSrc }}"
                 alt="Video Thumbnail"
                    loading="lazy" decoding="async" fetchpriority="low">
            <div class="yt-play">
              <div class="yt-btn" title="{{ __('تشغيل الفيديو') }}">
                <span class="yt-icon" aria-hidden="true"></span>
              </div>
            </div>
          </div>
        </div>

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

        <script>
          document.addEventListener('DOMContentLoaded', function () {
            const modal  = document.getElementById('videoModal');
            const iframe = document.getElementById('videoFrame');
            const rawUrl = @json($section->media_path);

            function toEmbed(url){
              try{
                const u = new URL(url, window.location.origin);
                if (u.hostname.includes('youtube.com') || u.hostname.includes('youtu.be')){
                  if (u.searchParams.get('v')) return 'https://www.youtube.com/embed/' + u.searchParams.get('v');
                  const parts = u.pathname.split('/').filter(Boolean);
                  if (u.hostname.includes('youtu.be') && parts[0]) return 'https://www.youtube.com/embed/' + parts[0];
                  if (u.pathname.startsWith('/shorts/') && parts[1]) return 'https://www.youtube.com/embed/' + parts[1];
                }
                if (u.hostname.includes('vimeo.com')){
                  const id = u.pathname.split('/').filter(Boolean).pop();
                  if (id) return 'https://player.vimeo.com/video/' + id;
                }
                return url;
              }catch(e){ return url; }
            }

            modal.addEventListener('show.bs.modal', function () {
              const embed = toEmbed(rawUrl);
              iframe.src = embed + (embed.includes('?') ? '&' : '?') + 'autoplay=1';
            });
            modal.addEventListener('hidden.bs.modal', function () {
              iframe.src = '';
            });
          });
        </script>
      @endif
    </div>

  </div>
</section>
@endif

{{-- Bootstrap (لو مش مضاف في الـ layout) --}}
@once
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
@endonce
