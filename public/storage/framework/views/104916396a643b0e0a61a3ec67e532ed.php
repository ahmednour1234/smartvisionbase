
<?php
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
?>

<?php if($section): ?>
    
    <?php $__env->startPush('styles'); ?>
        <?php if($isVideo && $mediaSrc): ?>
            <link rel="preload" as="video" href="<?php echo e($mediaSrc); ?>" type="video/mp4">
        <?php endif; ?>
    <?php $__env->stopPush(); ?>

    <section class="section section-lg bg-default wow fadeIn mt-5 mb-5">
        <div class="container">
            <div class="row row-30 justify-content-center">
                <div class="col-md-10 col-lg-6 col-xl-5">
                    <h3 class="heading-lg-postfix-15"><?php echo e($section->title[app()->getLocale()] ?? ''); ?></h3>
                    <p style="color:black;"><?php echo e($section->description[app()->getLocale()] ?? ''); ?></p>

                    <a class="button button-primary" href="<?php echo e(route('web.becomesponsor')); ?>" data-triangle=".button-overlay">
                        <span>Become Sponsor</span>
                        <span class="button-overlay"></span>
                    </a>
                </div>

                <div class="col-md-10 col-lg-6 col-xl-7 text-md-right">
                    <div class="images-box">
                        <div class="images-box-item images-box-item-right">
                            <div class="wow fadeScale">
                                <?php if($isImage && $mediaSrc): ?>
                                    <img
                                        src="<?php echo e($mediaSrc); ?>"
                                        alt="About Image"
                                        style="max-width:100%;height:auto;display:block;border-radius:8px;"
                                        loading="eager" decoding="async" fetchpriority="high">
                                <?php elseif($isVideo && $mediaSrc): ?>
                                    <video
                                        id="<?php echo e($videoId); ?>"
                                        class="home-section-video"
                                        autoplay
                                        muted
                                        loop
                                        playsinline
                                        preload="metadata"
                                        <?php if($thumbUrl): ?> poster="<?php echo e($thumbUrl); ?>" <?php endif; ?>
                                        style="width:100%;height:auto;object-fit:cover;display:block;border-radius:8px;">
                                        <source src="<?php echo e($mediaSrc); ?>" type="video/mp4">
                                        <?php echo e(__('home_sections.your_browser_does_not_support_video')); ?>

                                    </video>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

            </div> 
        </div> 
    </section>

    
    <?php $__env->startPush('scripts'); ?>
    <?php if (! $__env->hasRenderedOnce('088b4869-0848-488f-9de4-c56045fbe837')): $__env->markAsRenderedOnce('088b4869-0848-488f-9de4-c56045fbe837'); ?>
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
    <?php endif; ?>
    <?php $__env->stopPush(); ?>
<?php endif; ?>
<?php /**PATH /home/u552947370/domains/affiliatesummitglobal.com/public_html/resources/views/web/content/sections/about.blade.php ENDPATH**/ ?>