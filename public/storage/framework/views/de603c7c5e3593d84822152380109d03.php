         <!-- Section Header Default-->
     <header class="section page-header">
         <!--RD Navbar-->
         <div class="rd-navbar-wrap">
             <nav class="rd-navbar rd-navbar-classic" data-layout="rd-navbar-fixed" data-sm-layout="rd-navbar-fixed"
                 data-md-layout="rd-navbar-fixed" data-md-device-layout="rd-navbar-fixed" data-lg-layout="rd-navbar-static"
                 data-lg-device-layout="rd-navbar-static" data-xl-layout="rd-navbar-static"
                 data-xl-device-layout="rd-navbar-static" data-xxl-layout="rd-navbar-static"
                 data-xxl-device-layout="rd-navbar-static" data-lg-stick-up-offset="46px" data-xl-stick-up-offset="46px"
                 data-xxl-stick-up-offset="76px" data-lg-stick-up="true" data-xl-stick-up="true"
                 data-xxl-stick-up="true">
                 <div class="rd-navbar-collapse-toggle rd-navbar-fixed-element-1"
                     data-rd-navbar-toggle=".rd-navbar-collapse"><span></span></div>
                 <div class="rd-navbar-main-outer">
                     <div class="rd-navbar-main">
                         <!--RD Navbar Panel-->
                         <div class="rd-navbar-panel ">
                             <!--RD Navbar Toggle-->
                             <button class="rd-navbar-toggle"
                                 data-rd-navbar-toggle=".rd-navbar-nav-wrap"><span></span></button>
                             <!--RD Navbar Brand-->
                             <div class="rd-navbar-brand">
                                 <?php
                                     $settings = \App\Models\Setting::first();
                                 ?>

                                 <!--Brand-->
                                 <a class="brand" href="<?php echo e(route('web.home')); ?>">
                                     <?php if($settings && $settings->img): ?>
                                         <img class="brand-logo-dark" src="<?php echo e(asset('public/'.$settings->img)); ?>"
                                             srcset="<?php echo e(asset('public/'.$settings->img)); ?> 2x" alt="Logo" />

                                         <img class="brand-logo-light" src="<?php echo e(asset('public/'.$settings->img)); ?>"
                                             srcset="<?php echo e(asset('public/'.$settings->img)); ?> 2x" alt="Logo" />
                                     <?php else: ?>
                                         <img class="brand-logo-dark" src="<?php echo e(asset('public/images/logo-default.png')); ?>"
                                             srcset="<?php echo e(asset('ipublic/mages/logo-default@2x.png')); ?> 2x" alt="Logo" />
                                         <img class="brand-logo-light" src="<?php echo e(asset('images/logo-inverse.png')); ?>"
                                             srcset="<?php echo e(asset('public/images/logo-inverse@2x.png')); ?> 2x" alt="Logo" />
                                     <?php endif; ?>
                                 </a>
                             </div>
                         </div>
                         <!-- Rd Navbar Navigation-->
                     <div class="rd-navbar-main-element">
    <div class="rd-navbar-nav-wrap">
        <ul class="rd-navbar-nav">
            <li class="rd-nav-item <?php echo e(request()->routeIs('web.home') ? 'active' : ''); ?>"style="text-decoration:none;">
                <a class="rd-nav-link" href="<?php echo e(route('web.home')); ?>"><?php echo e(__('Home')); ?></a>
            </li>

            <li class="rd-nav-item <?php echo e(request()->routeIs('web.about') ? 'active' : ''); ?>">
                <a class="rd-nav-link" href="<?php echo e(route('web.about')); ?>"><?php echo e(__('About')); ?></a>
            </li>
            <?php
              use App\Models\HomeSection;

             $section = HomeSection::find(10);
            ?>
            <?php if($section->is_active==1): ?>
  <li class="rd-nav-item <?php echo e(request()->routeIs('web.voting') ? 'active' : ''); ?>">
                                  <a class="rd-nav-link" href="<?php echo e(route('web.voting')); ?>"><?php echo e(__('Voting')); ?></a>
                              </li>
                              <?php endif; ?>
                                    <?php

             $section = HomeSection::find(11);
            ?>
                                <li class="rd-nav-item  <?php echo e(request()->routeIs('web.special*') ? 'active' : ''); ?>">
    <a class="rd-nav-link" href="#"><?php echo e(__('Special Guests')); ?></a>
    <ul class="rd-menu rd-navbar-dropdown">

        
        <li class="rd-dropdown-item <?php echo e(request()->routeIs('web.special') && (request()->query('section') == 'special' || !request()->query('section')) ? 'active' : ''); ?>">
            <a class="rd-dropdown-link" href="<?php echo e(route('web.special', ['section' => 'special'])); ?>"><?php echo e(__('Influencers')); ?></a>
        </li>
            <?php if($section->is_active==1): ?>

        
        <li class="rd-dropdown-item <?php echo e(request()->routeIs('web.special') && request()->query('section') == 'aps' ? 'active' : ''); ?>">
            <a class="rd-dropdown-link" href="<?php echo e(route('web.special', ['section' => 'aps'])); ?>"><?php echo e(__('IBS & Affiliate')); ?></a>
        </li>
                              <?php endif; ?>

    </ul>
</li>
            <li class="rd-nav-item <?php echo e(request()->routeIs('web.speaker') ? 'active' : ''); ?>">
                <a class="rd-nav-link" href="<?php echo e(route('web.speaker')); ?>"><?php echo e(__('Speakers')); ?></a>
            </li>

            <li class="rd-nav-item <?php echo e(request()->routeIs('web.sponsors') ? 'active' : ''); ?>">
                <a class="rd-nav-link" href="<?php echo e(route('web.sponsors')); ?>"><?php echo e(__('Sponsors')); ?></a>
            </li>

            <li class="rd-nav-item <?php echo e(request()->routeIs('web.packages') ? 'active' : ''); ?>">
                <a class="rd-nav-link" href="<?php echo e(route('web.packages')); ?>"><?php echo e(__('Packages')); ?></a>
            </li>

            <?php
                $moreRoutes = ['web.schdule', 'web.blogs', 'web.multi_media'];
                $isMoreActive = collect($moreRoutes)->contains(fn($r) => request()->routeIs($r));
            ?>
            <li class="rd-nav-item <?php echo e($isMoreActive ? 'active' : ''); ?>">
                <a class="rd-nav-link" href="#"><?php echo e(__('More')); ?></a>
                <ul class="rd-menu rd-navbar-dropdown">
                    <li class="rd-dropdown-item <?php echo e(request()->routeIs('web.schdule') ? 'active' : ''); ?>">
                        <a class="rd-dropdown-link" href="<?php echo e(route('web.schdule')); ?>"><?php echo e(__('Schedule Event')); ?></a>
                    </li>
                    <li class="rd-dropdown-item <?php echo e(request()->routeIs('web.blogs') ? 'active' : ''); ?>">
                        <a class="rd-dropdown-link" href="<?php echo e(route('web.blogs')); ?>"><?php echo e(__('Blogs')); ?></a>
                    </li>
                    <li class="rd-dropdown-item <?php echo e(request()->routeIs('web.multi_media') ? 'active' : ''); ?>">
                        <a class="rd-dropdown-link" href="<?php echo e(route('web.multi_media')); ?>"><?php echo e(__('Multi Media')); ?></a>
                    </li>
                              <li class="rd-nav-item <?php echo e(request()->routeIs('web.contact') ? 'active' : ''); ?>">
                                  <a class="rd-dropdown-link" href="<?php echo e(route('web.contact')); ?>"><?php echo e(__('Contact')); ?></a>
                              </li>
                </ul>
            </li>
        </ul>
    </div>
    <!--<div class="rd-navbar-collapse toggle-original-elements d-block d-md-none">-->
<!--  <a href="<?php echo e(route('web.register')); ?>" class="custom-button-white mb-2 text-center">-->
<!--    <?php echo e(__('register_now')); ?>-->
<!--  </a>-->
<!--  <a href="<?php echo e(route('web.becomesponsor')); ?>" class="custom-button-red text-center">-->
<!--    <?php echo e(__('become_sponsor')); ?>-->
<!--  </a>-->
<!--</div>-->
</div>

                         <!-- RD Navbar Collapse-->

 <div class="action-buttons d-none d-md-flex">
  <a href="<?php echo e(route('web.register')); ?>" class="custom-button-white">
    <?php echo e(__('register_now')); ?>

  </a>
  <a href="<?php echo e(route('web.becomesponsor')); ?>" class="custom-button-red">
    <?php echo e(__('become_sponsor')); ?>

  </a>
</div>

                     </div>
             </nav>
         </div>
     </header>
<style>
  .btn-same-size{min-width:160px;text-align:center;padding:10px 15px;font-weight:500;white-space:nowrap}
  .navbar-buttons{gap:10px;flex-wrap:nowrap!important}

  .rd-navbar-classic.rd-navbar-static .rd-navbar-aside-outer,
  .rd-navbar-classic.rd-navbar-static .rd-navbar-main-outer{padding-left:0;padding-right:0}
  .rd-navbar-classic.rd-navbar-static .rd-navbar-aside,
  .rd-navbar-classic.rd-navbar-static .rd-navbar-main{max-width:1300px;margin-left:10px;margin-right:10px}
  .rd-navbar-classic.rd-navbar-static .rd-navbar-brand img{padding-left:35px;max-width:150px;max-height:200px}

  .action-buttons{display:flex;gap:20px;flex-wrap:wrap;justify-content:center;padding-right:35px}

  /* ====== موبايل: إصلاح القوائم المنسدلة (More وغيرها) ====== */
  @media (max-width: 991.98px){
    /* خلي الناف بالكامل يسكroll لو طويل */
    .rd-navbar-nav-wrap{
      max-height: calc(100vh - 70px);
      overflow-y: auto;
      -webkit-overflow-scrolling: touch;
    }
    /* المدخلات ذات الساب منيو */
    .rd-nav-item.has-sub > .rd-sub-toggle{
      display:inline-flex; align-items:center; justify-content:center;
      margin-left:8px; width:28px; height:28px; border-radius:6px;
      background:rgba(0,0,0,.06); border:0; cursor:pointer;
      position:relative;
    }
    .rd-nav-item.has-sub > .rd-sub-toggle::before{
      content:""; width:8px; height:8px; border-right:2px solid currentColor; border-bottom:2px solid currentColor;
      transform: rotate(45deg); display:block;
    }
    .rd-nav-item.has-sub.rd-dropdown-open > .rd-sub-toggle::before{
      transform: rotate(-135deg);
    }
    /* القوائم المنسدلة داخل الموبايل تبقى static وتظهر/تختفي */
    .rd-navbar .rd-menu{
      position: static !important;
      display: none;
      padding-left: 14px;
      margin-top: 6px;
    }
    .rd-navbar .rd-nav-item.rd-dropdown-open > .rd-menu{
      display: block;
    }
    /* تحسين لمساحات اللمس */
    .rd-nav-link, .rd-dropdown-link{
      padding-top: 10px; padding-bottom: 10px;
    }
  }
  .rd-navbar-fixed .rd-navbar-nav-wrap::-webkit-scrollbar-thumb {
    background: #cc252e;
    border: none;
    border-radius: 0;
    opacity: .2;
}
.rd-navbar-fixed .rd-navbar-nav-wrap::-webkit-scrollbar-track {
    background: #cc252e;
    border: none;
    border-radius: 0;
}
</style>
<script>
document.addEventListener('DOMContentLoaded', function () {
  // عناصر اللوجو
  const brand = document.querySelector('.brand');
  if (!brand) return;

  const darkImg  = brand.querySelector('img.brand-logo-dark');
  const lightImg = brand.querySelector('img.brand-logo-light');

  // مسارات الأصل للحفظ والرجوع
  const orig = {
    dark:  { src: darkImg?.getAttribute('src') || '',  srcset: darkImg?.getAttribute('srcset')  || '' },
    light: { src: lightImg?.getAttribute('src') || '', srcset: lightImg?.getAttribute('srcset') || '' },
  };

  // مسار اللوجو الأسود المطلوب
  const blackLogo = "<?php echo e(asset('public/logoblack.png')); ?>";

  // عنصر الـwrap اللي بيتغير عليه الكلاس
  const navWrap = document.querySelector('.rd-navbar-nav-wrap') || document.body;

  // موبايل لحد ~992px (عدّلها لو نظامك مختلف)
  const mql = window.matchMedia('(max-width: 991.98px)');

  // تبديل/إرجاع اللوجو
  function applyLogo(forceBlack) {
    const applyTo = (img, origObj) => {
      if (!img) return;
      if (forceBlack) {
        img.setAttribute('src', blackLogo);
        img.setAttribute('srcset', blackLogo + ' 2x');
      } else {
        if (origObj.src)    img.setAttribute('src', origObj.src);
        if (origObj.srcset) img.setAttribute('srcset', origObj.srcset);
      }
    };
    applyTo(darkImg,  orig.dark);
    applyTo(lightImg, orig.light);
  }

  // شرط التفعيل: الموبايل + وجود الكلاسات المطلوبة
  function recompute() {
    const active = document.querySelector('.rd-navbar-nav-wrap.toggle-original-elements.active');
    applyLogo(Boolean(active) && mql.matches);
  }

  // أول مرة
  recompute();

  // راقب تغييرات الكلاسات على عنصر الـwrap
  if (navWrap) {
    const observer = new MutationObserver(recompute);
    observer.observe(navWrap, { attributes: true, attributeFilter: ['class'], subtree: true });
  }

  // تغيّر حجم الشاشة
  if (mql.addEventListener) {
    mql.addEventListener('change', recompute);
  } else {
    // Safari قديم
    mql.addListener(recompute);
  }

  // في بعض الثيمات، زر البرجر بيضيف/يشيل الكلاس—نتابع النقرات
  document.addEventListener('click', function (e) {
    if (e.target.closest('.rd-navbar-toggle') || e.target.closest('.navbar-toggler')) {
      setTimeout(recompute, 0);
    }
  });
});
</script>
<?php /**PATH /home/u552947370/domains/affiliatesummitglobal.com/public_html/resources/views/web/layouts/header.blade.php ENDPATH**/ ?>