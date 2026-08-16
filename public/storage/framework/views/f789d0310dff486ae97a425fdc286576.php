<?php
  $locale          = $locale          ?? app()->getLocale();
  $countryFlags    = $countryFlags    ?? [];
  $socialPlatforms = $socialPlatforms ?? [
      'facebook'  => 'facebook-f',
      'twitter'   => 'twitter',
      'linkedin'  => 'linkedin',
      'youtube'   => 'youtube-play',
      'tiktok'    => 'tiktok',
      'instgram'  => 'instagram',
  ];
?>

<style>
  /* تصغير الصورة داخل .speaker-img + ضبط نسبة العرض/الارتفاع */
  .sp-card .speaker-img img{
    width:100%; height:auto; object-fit:cover; display:block;
    aspect-ratio: 4 / 5;
  }
  /* شريط المتابعين: تقليل حجم الأيقونات والخط */
  .sp-card .followers-inline{
    font-size:14px;
  }
  .sp-card .followers-inline svg{
    width:22px; height:22px;
  }

  /* شاشات صغيرة */
  @media (max-width: 575.98px){
    .sp-card .speaker-img img{
    }
    .sp-card .followers-inline{
      font-size:11px;              /* خط أصغر */
      gap:8px;
    }
    .sp-card .followers-inline svg{
      width:18px; height:18px;     /* أيقونة أصغر */
    }
  }

  /* موبايل صغير جدًا */
  @media (max-width: 360px){
    .sp-card .followers-inline{
      font-size:10px;
      gap:6px;
    }
    .sp-card .followers-inline svg{
      width:16px; height:16px;
    }
  }
</style>
<style>
  /* الوضع الافتراضي: أفقي */
  .sp-card .followers-inline{
    display:flex; align-items:center; gap:10px; margin-top:8px;
  }
  .sp-card .followers-inline .sep{ display:inline; }

  /* موبايل ≤ 575.98px: عمودي تحت بعض */
  @media (max-width:575.98px){
    .sp-card .followers-inline{
      flex-direction:column;        /* الترتيب عمودي */
      align-items:center;           /* توسيط */
      gap:8px;                      /* مسافة أقل */
    }
    .sp-card .followers-inline .sep{
      display:none;                 /* نخفي الشرطة في العمودي */
    }
    /* تصغير الأيقونة والخط للموبايل (اختياري) */
    .sp-card .followers-inline .followers-bar svg{ width:18px; height:18px; }
    .sp-card .followers-inline{ font-size:11px; }
  }

  /* موبايل صغير جدًا ≤ 360px (اختياري) */
  @media (max-width:360px){
    .sp-card .followers-inline .followers-bar svg{ width:16px; height:16px; }
    .sp-card .followers-inline{ font-size:10px; gap:6px; }
  }
</style>

<?php $__empty_1 = true; $__currentLoopData = $speakers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $speaker): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
  <?php
    $img = $speaker->image
            ? asset('public/'.$speaker->image)
            : asset('public/web/assets/images/placeholder-speaker.jpg');

    $followersRaw = $speaker->number_of_followers;
    $followers    = $followersRaw; // بدون أي تحويل

    $spkCode   = strtoupper($speaker->country_code ?? '');
    $flagUrl   = $countryFlags[$spkCode] ?? null;

    $speakerName  = $locale === 'ar'
                    ? ($speaker->name_ar  ?? 'متحدث')
                    : ($speaker->name_en  ?? 'Speaker');

    $speakerTitle = $locale === 'ar'
                    ? ($speaker->title_ar ?? '')
                    : ($speaker->title_en ?? '');

    $hasIg = !empty($followers);
    $hasTt = !empty($speaker->followers_ticktock);
  ?>

  <div class="col-6 col-md-6 col-lg-4 mb-4 sp-col">
    <div class="speaker sp-card">
      <div class="speaker-img">
        <a href="#">
          <img src="<?php echo e($img); ?>" alt="<?php echo e(e($speakerName)); ?>" loading="lazy" decoding="async">
        </a>

        
        <?php if($hasIg || $hasTt): ?>
          <div class="followers-inline" style="display:flex;align-items:center;gap:5px;margin-top:8px;">
            <?php if($hasIg): ?>
              <span class="followers-bar" style="display:inline-flex;align-items:center;gap:6px;">
                
                <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false" style="display:block">
                  <rect x="3" y="3" width="18" height="18" rx="5" ry="5"
                        fill="none" stroke="currentColor" stroke-width="1.5"/>
                  <circle cx="12" cy="12" r="4.5"
                          fill="none" stroke="currentColor" stroke-width="1.5"/>
                  <circle cx="17.5" cy="6.5" r="1.25" fill="currentColor"/>
                </svg>
                <span>
                  <strong><?php echo e($followers); ?></strong>
                  <?php echo e((app()->getLocale() ?? 'ar') === 'ar' ? ($followers == 1 ? 'متابع' : 'متابعين') : 'Followers'); ?>

                </span>
              </span>
            <?php endif; ?>

            
            <?php if($hasIg && $hasTt): ?>
              <span class="sep" aria-hidden="true">-</span>
            <?php endif; ?>

            <?php if($hasTt): ?>
              <span class="followers-bar" style="display:inline-flex;align-items:center;gap:6px;">
                
                <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false" style="display:block">
                  <path d="M14 3v3.6c1.4 1.2 3.2 2 5 2v2.5c-2.1 0-3.9-.6-5-1.4v4.4A6.5 6.5 0 1 1 9 8.5c.5 0 1 .05 1.5.16v2.6A3.5 3.5 0 1 0 12.5 15V3h1.5z"
                        fill="currentColor"/>
                </svg>
                <span>
                  <strong><?php echo e($speaker->followers_ticktock); ?></strong>
                  <?php echo e((app()->getLocale() ?? 'ar') === 'ar'
                      ? ($speaker->followers_ticktock == 1 ? 'متابع' : 'متابعين')
                      : 'Followers'); ?>

                </span>
              </span>
            <?php endif; ?>
          </div>
        <?php endif; ?>
      </div>

      <div class="speaker-info">
        <h5 class="speaker-title">
          <a href="#">
            <?php echo e($speakerName); ?>

            <?php if($flagUrl): ?>
              <img class="country-flag" src="<?php echo e($flagUrl); ?>" alt="flag" loading="lazy">
            <?php endif; ?>
          </a>
        </h5>
        <p class="speaker-position"><?php echo e($speakerTitle); ?></p>

        <ul class="speaker-social-list">
          <?php $__currentLoopData = $socialPlatforms; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $field => $icon): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php $url = trim((string) ($speaker->$field ?? '')); ?>
            <?php if($url !== ''): ?>
              <li>
                <a class="icon fa fa-<?php echo e($icon); ?>"
                   href="<?php echo e($url); ?>"
                   target="_blank"
                   rel="noopener noreferrer nofollow"
                   aria-label="<?php echo e($field); ?>"></a>
              </li>
            <?php endif; ?>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </ul>
      </div>
    </div>
  </div>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
  
<?php endif; ?>
<?php /**PATH /home/u552947370/domains/affiliatesummitglobal.com/public_html/resources/views/web/content/partials/_cards_special.blade.php ENDPATH**/ ?>