<?php if($section && $section->media_path): ?>
<?php
    $locale = app()->getLocale();
    $title  = $section->title[$locale] ?? 'Promo Title';

    // المسار الخام كما هو من الداتابيس
    $rawPath = trim($section->media_path);

    // هل هو لينك خارجي؟ (يوتيوب / فيميو / أي شيء آخر)
    $isExternal = str_starts_with($rawPath, 'http://') || str_starts_with($rawPath, 'https://');

    // لو مش لينك خارجي نعتبره ملف داخل public
    $mediaSrc = $isExternal ? $rawPath : asset('public/' . ltrim($rawPath, '/'));

    // الثَمبنايل (إن وجدت)
    $thumbSrc = null;
    if (!empty($section->thumbnail)) {
        $thumbRaw = trim($section->thumbnail);
        $thumbSrc = (str_starts_with($thumbRaw, 'http://') || str_starts_with($thumbRaw, 'https://'))
            ? $thumbRaw
            : asset('public/' . ltrim($thumbRaw, '/'));
    }

    // Poster افتراضي
    $poster = $thumbSrc ?: ($section->media_type === 'image'
                ? $mediaSrc
                : asset('public/web/assets/images/black-1x1.png'));

    // نحاول نطلع embed URL لو يوتيوب/فيميو
    $embedUrl = null;
    $host     = parse_url($mediaSrc, PHP_URL_HOST);

    if ($host && (str_contains($host, 'youtube.com') || str_contains($host, 'youtu.be'))) {
        $query = parse_url($mediaSrc, PHP_URL_QUERY) ?: '';
        parse_str($query, $qs);
        $videoId = $qs['v'] ?? null;

        // https://youtu.be/ID أو shorts
        if (!$videoId) {
            $path  = trim(parse_url($mediaSrc, PHP_URL_PATH) ?? '', '/');
            $parts = explode('/', $path);
            if (str_contains($host, 'youtu.be') && !empty($parts[0])) {
                $videoId = $parts[0];
            } elseif (str_starts_with($path, 'shorts/') && !empty($parts[1])) {
                $videoId = $parts[1];
            }
        }

        if (!empty($videoId)) {
            $embedUrl = 'https://www.youtube.com/embed/' . $videoId;
        }
    } elseif ($host && str_contains($host, 'vimeo.com')) {
        $path  = trim(parse_url($mediaSrc, PHP_URL_PATH) ?? '', '/');
        $parts = explode('/', $path);
        $videoId = end($parts);
        if (!empty($videoId)) {
            $embedUrl = 'https://player.vimeo.com/video/' . $videoId;
        }
    }
?>

<section id="home-promo" class="overflow-hidden" style="margin-top:-10px; background:#fff; position:relative; padding-bottom:30px;">
    <div class="container">

        
        <div class="text-center py-3">
            <h2 class="promo-title"
                style="
                    font-size:60px; font-weight:900; line-height:1.2; margin-bottom:10px;
                    background:linear-gradient(270deg,#000000,#E73701,#000000);
                    background-size:600% 600%;
                    -webkit-background-clip:text; -webkit-text-fill-color:transparent;
                    animation:gradientShift 5s ease infinite;
                ">
                <?php echo e($title); ?>

            </h2>
        </div>

        <style>
     
            #home-promo a { text-decoration:none !important; outline:none; }

            .media-wrap{ width:100%; }
            .hero-media{
                width:100%;
                height:auto;
                object-fit:contain;
                display:block;
                background:#000;
                border-radius:8px;
            }

            .video-thumb-wrap{ position:relative; width:100%; cursor:pointer; }
            .video-thumb-img{
                width:100%;
                height:auto;
                object-fit:cover;
                display:block;
                border-radius:13px;
            }
            .gradient-border{
                position:relative;
                border-radius:16px;
                padding:3px;
                background: conic-gradient(from 0deg, #cc252e, #ffffff, #cc252e);
                animation: gradRotate 6s linear infinite;
                box-shadow:0 8px 24px rgba(231,55,1,.18);
            }
            .yt-play{
                position:absolute;
                inset:0;
                display:flex;
                align-items:center;
                justify-content:center;
                pointer-events:none;
            }
            .yt-btn{
                width:92px;
                height:92px;
                border-radius:50%;
                background: rgba(0,0,0,0.9);
                box-shadow:0 12px 28px rgba(0,0,0,.28),
                           0 2px 6px rgba(0,0,0,.18) inset,
                           0 0 0 6px rgba(255,255,255,.9);
                display:flex;
                align-items:center;
                justify-content:center;
            }
            .yt-icon{
                width:0;
                height:0;
                border-left:20px solid #fff;
                border-top:12px solid transparent;
                border-bottom:12px solid transparent;
                margin-left:6px;
            }


            @media (prefers-reduced-motion: reduce){
                .promo-title{ animation:none }
                .gradient-border{ animation:none }
            }
            @media (max-width: 768px) {
                #home-promo img.hero-media,
                #home-promo .video-thumb-img{
                    max-height: 45vh;
                    object-fit:cover;
                }
                .promo-title{
                    font-size:34px !important;
                }
            }
        </style>

        <div class="media-wrap">

            
            <?php if($section->media_type === 'image'): ?>
                <img
                    class="hero-media"
                    src="<?php echo e($mediaSrc); ?>"
                    alt="Promo Image"
                    loading="lazy" decoding="async" fetchpriority="low"
                >

            
            <?php elseif(in_array($section->media_type, ['video', 'link']) && $embedUrl): ?>
                <div class="video-thumb-wrap" data-bs-toggle="modal" data-bs-target="#homePromoVideoModal"
                     aria-label="Play video">
                    <div class="gradient-border">
                        <img
                            class="video-thumb-img"
                            src="<?php echo e($poster); ?>"
                            alt="Video Thumbnail"
                            loading="lazy" decoding="async" fetchpriority="low"
                        >
                        <div class="yt-play">
                            <div class="yt-btn" title="<?php echo e(__('Play video')); ?>">
                                <span class="yt-icon" aria-hidden="true"></span>
                            </div>
                        </div>
                    </div>
                </div>

                
                <div class="modal fade" id="homePromoVideoModal" tabindex="-1"
                     aria-labelledby="homePromoVideoLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-xl">
                        <div class="modal-content bg-transparent border-0">
                            <div class="modal-body p-0">
                                <div class="ratio ratio-16x9">
                                    <iframe
                                        id="homePromoVideoFrame"
                                        src=""
                                        title="Promo Video"
                                        frameborder="0"
                                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                                        allowfullscreen
                                    ></iframe>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <script>
                    document.addEventListener('DOMContentLoaded', function () {
                        var modal  = document.getElementById('homePromoVideoModal');
                        var iframe = document.getElementById('homePromoVideoFrame');
                        var baseEmbed = <?php echo json_encode($embedUrl, 15, 512) ?>;

                        modal.addEventListener('show.bs.modal', function () {
                            var url = baseEmbed + (baseEmbed.includes('?') ? '&' : '?') + 'autoplay=1&mute=1';
                            iframe.src = url;
                        });

                        modal.addEventListener('hidden.bs.modal', function () {
                            iframe.src = '';
                        });
                    });
                </script>

            
            <?php elseif($section->media_type === 'video'): ?>
                <video
                    class="hero-media"
                    controls
                    playsinline
                    preload="metadata"
                    poster="<?php echo e($poster); ?>"
                >
                    <source src="<?php echo e($mediaSrc); ?>" type="video/mp4">
                    Your browser does not support the video tag.
                </video>
            <?php endif; ?>

        </div>

    </div>
</section>
<?php endif; ?>

<?php if (! $__env->hasRenderedOnce('cade1908-abe9-4e71-9043-0183a7c7dc4f')): $__env->markAsRenderedOnce('cade1908-abe9-4e71-9043-0183a7c7dc4f'); ?>
    
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<?php endif; ?>
<?php /**PATH /home/u552947370/domains/affiliatesummitglobal.com/public_html/resources/views/web/content/sections/promo.blade.php ENDPATH**/ ?>