<!DOCTYPE html>
<html class="wide" lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, height=device-height, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Affiliate & Influencers Summit Dubai 2025</title>
  <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">

  
  <link rel="dns-prefetch" href="https://www.googletagmanager.com">
  <link rel="dns-prefetch" href="https://analytics.tiktok.com">
  <link rel="dns-prefetch" href="https://connect.facebook.net">

  
  <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Barlow%7CBarlow+Condensed:300,400,500,600,700,900&display=swap"
        media="print" onload="this.media='all'">
  <noscript>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Barlow%7CBarlow+Condensed:300,400,500,600,700,900&display=swap">
  </noscript>

  
  <link rel="icon" href="<?php echo e(asset('public/1754488784-LOGO AFFILIATE-06 1.png')); ?>" type="image/x-icon">

  
  <script>
    (function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
      new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
      j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
      'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
    })(window,document,'script','dataLayer','GTM-K5HL8J9K');
  </script>

  
  <?php
    function ver($path) {
      $full = public_path($path);
      return asset($path).(file_exists($full)?'?v='.filemtime($full):'');
    }
  ?>

  
  <link rel="preload" href="<?php echo e(ver('public/web/assets/css/bootstrap.css')); ?>" as="style" onload="this.onload=null;this.rel='stylesheet'">
  <link rel="preload" href="<?php echo e(ver('public/web/assets/css/fonts.css')); ?>"    as="style" onload="this.onload=null;this.rel='stylesheet'">
  <link rel="preload" href="<?php echo e(ver('public/web/assets/css/style.css')); ?>"    as="style" onload="this.onload=null;this.rel='stylesheet'">
  <noscript>
    <link rel="stylesheet" href="<?php echo e(ver('public/web/assets/css/bootstrap.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(ver('public/web/assets/css/fonts.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(ver('public/web/assets/css/style.css')); ?>">
  </noscript>

  
  <link rel="preload" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css"
        as="style" onload="this.onload=null;this.rel='stylesheet'">
  <noscript>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
  </noscript>

  
  <?php $pixels = \App\Models\Pixel::where('active', true)->get(); ?>
  <script>
    window.__appPixels = <?php echo json_encode($pixels->map(fn($p)=>['name'=>$p->name, 'id'=>$p->pixel_id]), 512) ?>;
    window.addEventListener('load', function () {
      (window.__appPixels||[]).forEach(function(p){
        try {
          if (p.name === 'Facebook') {
            !(function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?
            n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;
            n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;
            t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)
            })(window, document,'script','https://connect.facebook.net/en_US/fbevents.js');
            fbq('init', p.id); fbq('track','PageView');
          } else if (p.name === 'TikTok') {
            !function (w, d, t) {
              w.TiktokAnalyticsObject = t; var ttq = w[t] = w[t] || [];
              ttq.methods = ["page","track","identify","instances","debug","on","off","once","ready","alias","group","enableCookie","disableCookie"];
              ttq.setAndDefer=function(t,e){t[e]=function(){t.push([e].concat([].slice.call(arguments,0)))}};for (var i=0;i<ttq.methods.length;i++) ttq.setAndDefer(ttq, ttq.methods[i]);
              ttq.load=function(e,n){var i="https://analytics.tiktok.com/i18n/pixel/events.js";
                ttq._i=ttq._i||{}; ttq._i[e]=[]; ttq._i[e]._u=i; ttq._t=ttq._t||{}; ttq._t[e]=+new Date;
                ttq._o=ttq._o||{}; ttq._o[e]=n||{}; var o=d.createElement("script"); o.type="text/javascript"; o.async=!0;
                o.src=i+"?sdkid="+e+"&lib="+t; var a=d.getElementsByTagName("script")[0]; a.parentNode.insertBefore(o,a) };
            }(window, document, 'ttq');
            ttq.load(p.id); ttq.page();
          }
        } catch(e){}
      });
    });
  </script>
</head>
<body>
  
  <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-K5HL8J9K" height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>

  <div class="page">
    
    <?php echo $__env->make('web.layouts.header', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    
    <?php echo $__env->yieldContent('content'); ?>

    
    <?php echo $__env->make('web.layouts.footer', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
  </div>

  
  <div class="snackbars" id="form-output-global"></div>

  
  <div class="block-with-svg-gradients" aria-hidden="true">
    <svg xmlns="http://www.w3.org/2000/svg">
      <defs>
        <linearGradient id="svg-gradient-primary" x1="0%" y1="100%" x2="100%" y2="0%">
          <stop offset="0%" style="stop-color:#822EA8;stop-opacity:1"></stop>
          <stop offset="100%" style="stop-color:#D90E90;stop-opacity:1"></stop>
        </linearGradient>
      </defs>
    </svg>
  </div>

  <?php
    $event   = \App\Models\Event::where('event_date', '>', now())->orderBy('event_date')->first();
    $setting = \App\Models\Setting::first();
    $whatsappNumber = $setting?->phone; // تأكد أنه بدون + وعلامات
    $eventDate = optional($event)->event_date ? \Carbon\Carbon::parse($event->event_date)->format('Y-m-d H:i:s') : null;
  ?>

  <style>
    .countdown-section {
      position: fixed; bottom: 0; width: 100%;
      background: linear-gradient(to right, #000000, #cc252e);
      color: #fff; padding: 15px 0; z-index: 9999;
    }
    .countdown-container { display:flex; flex-wrap:wrap; justify-content:space-between; align-items:center; gap:20px; }
    .countdown-text { font-size:15px; font-weight:bold; text-align:center; direction:ltr; }
    .countdown-text a { background-color:#25d366; padding:17px; border:none; font-size:20px; }
    .countdown-text .bi-whatsapp { color:#FFF } /* لون أيقونة البوتستراب داخل زر العدّاد */

    .countdown-timer span { margin:0 10px; }
    .action-buttons { display:flex; gap:20px; flex-wrap:wrap; justify-content:center; }

    .custom-button-white,.custom-button-red {
      padding:10px 20px; font-size:13px; font-weight:700; border-radius:8px; text-transform:uppercase; text-decoration:none; transition:.3s;
    }
    .custom-button-white { background:#fff; color: var(--e-global-color-primary, #cc252e); }
    .custom-button-white:hover { background: var(--e-global-color-primary, #cc252e); color:#fff; }
    .custom-button-red { background: var(--e-global-color-primary, #cc252e); color:#fff; }
    .custom-button-red:hover { background:#fff; color: var(--e-global-color-primary, #cc252e); }

    /* زر دائري عائم (لو استخدمته لاحقًا) */
    #whatsapp-float {
      position: fixed; bottom: 90px; right: 15px;
      background-color: #25d366; color:#fff; padding:12px 14px; border-radius:50%;
      font-size:20px; z-index:10000;
    }

    /* زر واتساب داخل شريط العدّاد */
    .whatsapp-icon{
      display:inline-flex; align-items:center; justify-content:center;
      width:30px; height:30px; border:2px solid #cc252e;
      background-color:transparent; color:#25d366; border-radius:50%;
      font-size:22px; transition:all .3s ease-in-out; text-decoration:none;
    }
    .whatsapp-icon:hover{ background-color:#cc252e; color:#fff; }

    @media (max-width: 767px) {
      .countdown-section { padding: 5px 0; }
      .countdown-container { justify-content:center; gap:20px; text-align:center; }
      .countdown-text { font-size:14px; font-weight:bold; text-align:center; direction:ltr; }
      .countdown-text a { padding:20px; }
      .countdown-timer span { margin:0 2px; }
      .action-buttons { gap:9px; }
      .whatsapp-icon{
        width:24px; height:24px; border:2px solid #25d366; color:#25d366; font-size:20px;
      }
    }
  </style>

  
  <?php if($eventDate): ?>
    <div class="countdown-section" id="countdownSection">
      <div class="container countdown-container">
        <div class="countdown-text">
          <span id="countdown-timer" class="countdown-timer" aria-live="polite"></span>
          <?php if($whatsappNumber): ?>
            <a href="https://wa.me/<?php echo e(preg_replace('/\D+/', '', $whatsappNumber)); ?>" target="_blank" rel="noopener"
               class="whatsapp-icon" aria-label="WhatsApp">
              <i class="bi bi-whatsapp" aria-hidden="true"></i>
            </a>
          <?php endif; ?>
        </div>
        <div class="action-buttons">
          <a href="<?php echo e(route('web.register')); ?>" class="custom-button-white"><?php echo e(__('register_now')); ?></a>
          <a href="<?php echo e(route('web.becomesponsor')); ?>" class="custom-button-red"><?php echo e(__('become_sponsor')); ?></a>
        </div>
      </div>
    </div>
  <?php endif; ?>

  
  <link rel="preload" as="style" href="https://cdn.jsdelivr.net/npm/lightgallery@2.7.1/css/lightgallery-bundle.min.css" />
  <script defer>
    document.addEventListener('DOMContentLoaded', function () {
      // إخفاء شريط العد التنازلي عند الوصول لأسفل الصفحة
      const cd = document.getElementById('countdownSection');
      if (cd) {
        window.addEventListener('scroll', () => {
          const atBottom = (window.scrollY + window.innerHeight) >= (document.documentElement.scrollHeight - 10);
          cd.style.display = atBottom ? 'none' : 'block';
        }, {passive:true});
      }

      // عدّاد الوقت
      <?php if($eventDate): ?>
      (function(){
        const eventDate = new Date("<?php echo e($eventDate); ?>").getTime();
        const el = document.getElementById('countdown-timer');
        if (!el) return;
        function tick(){
          const now = Date.now(), diff = eventDate - now;
          if (diff <= 0) { el.textContent = "<?php echo e(__('event_started')); ?>"; return; }
          const d = Math.floor(diff/86400000);
          const h = Math.floor((diff%86400000)/3600000);
          const m = Math.floor((diff%3600000)/60000);
          const s = Math.floor((diff%60000)/1000);
          el.innerHTML = `<span>${d} <?php echo e(__('days')); ?></span><span>${h} <?php echo e(__('hours')); ?></span><span>${m} <?php echo e(__('minutes')); ?></span><span>${s} <?php echo e(__('seconds')); ?></span>`;
        }
        tick(); setInterval(tick, 1000);
      })();
      <?php endif; ?>

      // تحميل LightGallery عند ظهور الجاليري
      const gallery = document.getElementById('gallery-auto-scroll');
      if (gallery && 'IntersectionObserver' in window) {
        const loadLG = () => {
          const lgCss = document.querySelector('link[href*="lightgallery-bundle.min.css"]');
          if (lgCss) lgCss.rel = 'stylesheet';
          const s1 = document.createElement('script'); s1.defer = true;
          s1.src = "https://cdn.jsdelivr.net/npm/lightgallery@2.7.1/lightgallery.umd.min.js";
          const s2 = document.createElement('script'); s2.defer = true;
          s2.src = "https://cdn.jsdelivr.net/npm/lightgallery@2.7.1/plugins/zoom/lg-zoom.umd.min.js";
          s2.onload = () => {
            // @ts-ignore
            window.lightGallery && window.lgZoom && window.lightGallery(gallery, {selector:'a[data-lightgallery="item"]', plugins:[window.lgZoom], speed:500});
          };
          document.body.appendChild(s1); document.body.appendChild(s2);
        };
        const io = new IntersectionObserver((entries) => {
          if (entries.some(e=>e.isIntersecting)) { loadLG(); io.disconnect(); }
        }, {rootMargin:'200px'});
        io.observe(gallery);
      } else if (gallery) {
        const s1 = document.createElement('script'); s1.defer = true;
        s1.src = "https://cdn.jsdelivr.net/npm/lightgallery@2.7.1/lightgallery.umd.min.js";
        const s2 = document.createElement('script'); s2.defer = true;
        s2.src = "https://cdn.jsdelivr.net/npm/lightgallery@2.7.1/plugins/zoom/lg-zoom.umd.min.js";
        document.body.appendChild(s1); document.body.appendChild(s2);
      }

      // ====== Fallback لأيـقونة واتساب لو ملف الأيقونات ما اتحملش ======
      (function ensureWhatsappIcon(){
        const el = document.querySelector('.whatsapp-icon .bi-whatsapp');
        if (!el) return;
        // لو محتوى :before فاضي → غالبًا الخط مش متحمّل
        const style = window.getComputedStyle(el, '::before');
        const content = style && style.getPropertyValue('content');
        if (!content || content === 'none' || content === '""') {
          el.outerHTML = '<svg class="bi bi-whatsapp" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 16 16" aria-hidden="true" focusable="false"><path fill="currentColor" d="M13.601 2.326A7.94 7.94 0 0 0 8.012.003C3.589.003.003 3.589.003 8.012c0 1.41.368 2.787 1.067 3.998L0 16l4.113-1.07a7.96 7.96 0 0 0 3.9 1.004h.003c4.423 0 8.009-3.586 8.009-8.009a7.95 7.95 0 0 0-2.424-5.599m-5.589 12.27a6.65 6.65 0 0 1-3.396-.93l-.244-.145-2.438.634.65-2.375-.159-.244a6.665 6.665 0 1 1 5.587 3.06m3.64-4.992c-.2-.1-1.177-.58-1.36-.646-.183-.067-.317-.1-.45.1-.133.2-.517.646-.634.78-.117.133-.233.15-.433.05-.2-.1-.84-.31-1.6-.99-.591-.526-.989-1.175-1.105-1.375-.117-.2-.012-.308.088-.408.09-.089.2-.233.3-.35.1-.117.133-.2.2-.333.067-.133.034-.25-.017-.35-.05-.1-.45-1.083-.616-1.483-.162-.39-.327-.338-.45-.343l-.383-.007c-.133 0-.35.05-.533.25-.183.2-.7.683-.7 1.666s.717 1.935.817 2.067c.1.133 1.41 2.154 3.413 3.02.477.206.85.329 1.14.421.479.152.915.13 1.26.079.384-.057 1.177-.48 1.343-.943.167-.466.167-.867.116-.943-.05-.075-.183-.116-.384-.216"/></svg>';
        }
      })();
    });
  </script>

  
  <script defer src="<?php echo e(ver('public/web/assets/js/core.min.js')); ?>"></script>
  <script defer src="<?php echo e(ver('public/web/assets/js/script.js')); ?>"></script>
</body>
</html>
<?php /**PATH /home/u552947370/domains/affiliatesummitglobal.com/public_html/resources/views/web/layouts/app.blade.php ENDPATH**/ ?>