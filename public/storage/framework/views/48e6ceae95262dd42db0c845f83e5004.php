<?php $__env->startSection('content'); ?>
<?php $locale = app()->getLocale(); ?>

<style>
      /* Breadcrumbs */
    @media (max-width: 991.98px) {
        .breadcrumbs-custom {
            height: 350px !important;
            background-size: cover;
            background-position: center;
        }
        .breadcrumbs-custom-title {
            font-size: 28px;
            padding-top: 150px;
        }
    }
    .breadcrumbs-custom {
        background-size: cover;
        background-position: center;
    }
    /* Reset and Base Styles */
    .sponsors-section * {
        box-sizing: border-box;
        margin: 0;
        padding: 0;
    }

    /* Main Container */
    .sponsors-container {
        width: 100%;
        min-height: 100vh;
        padding: 60px 20px;
        background: linear-gradient(135deg, #f5f7fa 0%, #e4e8f0 100%);
        position: relative;
        overflow: hidden;
    }

    /* Animated Background Elements */
    .sponsors-container::before {
        content: '';
        position: absolute;
        top: -50%;
        left: -50%;
        width: 200%;
        height: 200%;
        background-image:
            radial-gradient(circle at 20% 30%, rgba(231, 55, 1, 0.05) 0%, transparent 20%),
            radial-gradient(circle at 80% 70%, rgba(231, 55, 1, 0.05) 0%, transparent 20%);
        animation: float 15s ease-in-out infinite;
        pointer-events: none;
    }

    @keyframes float {
        0%, 100% { transform: translate(0, 0); }
        50% { transform: translate(50px, 50px); }
    }

    /* Header Styles */
    .sponsors-header {
        text-align: center;
        margin-bottom: 60px;
        position: relative;
        z-index: 2;
    }

    .sponsors-title {
        font-size: 3rem;
        font-weight: 800;
        color: #cc252e;
        margin-bottom: 20px;
        text-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    /* Category Styles */
    .category-container {
        margin-bottom: 60px;
        position: relative;
        z-index: 2;
    }

    .category-title {
        text-align: center;
        margin-bottom: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .category-title h2 {
        font-size: 2rem;
        color: #333;
        padding: 10px 20px;
        background: white;
        position: relative;
        z-index: 2;
        border-radius: 5px;
    }

    .category-title::before,
    .category-title::after {
        content: '';
        flex: 1;
        height: 2px;
        background: linear-gradient(to right, transparent, #cc252e, transparent);
    }

    /* Cards Grid */
    .cards-grid {
        display: flex;
        justify-content: center;
        align-items: center;
        flex-wrap: wrap;
        gap: 30px;
    }

    /* Card Wrapper */
    .sponsor-card-wrapper {
      height: fit-content !important;
    }

    /* Card Styles */
    .sponsor-card {
        width: 300px;
        height: 350px;
        position: relative;
        transform-style: preserve-3d;
        transition: all 0.8s ease;
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        cursor: pointer;
        margin: 0;
    }

    /* Card Faces */
    .card-face {
        position: absolute;
        width: 100%;
        height: 100%;
        backface-visibility: hidden;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        padding: 30px;
    }

    .card-front {
        background: white;
        transform: rotateY(0deg);
        transition: filter 0.3s ease;
    }

    /* Redial Circle */
    .redial {
        background:#cc252e;
        width: 70px;
        height: 70px;
        border-radius: 50%;
        position: absolute;
        top: 40%;
        right: 20px;
        transition: all 0.3s ease;
        overflow: hidden;

        /* نخلي المحتوى داخلها مخفي */
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        color: white;
        font-weight: 700;
        font-size: 1.2rem;

        /* نخفي المحتوى عشان يظهر عند hover */
        opacity: 0;
        pointer-events: none;
    }

    /* محتوى الـ redial (الاسم واللينك) مخفيين في الأصل */
    .redial h3,
    .redial a {
        display: none;
        color: #FFF;
        font-size: 30px;
        margin: 5px 0;
        text-decoration: none;
    }

    .redial a {
      font-size: 14px;
      background: #FFF;
      padding: 7px;
      color: #000;
      border-radius: 5px
    }

    /* عند الهوفر على الكارد */
    .sponsor-card:hover .redial {
        width: 100%;
        height: 100%;
        top: 0;
        right: 0;
        border-radius: 0;
        opacity: 1;
        pointer-events: auto;
        flex-direction: column;
        justify-content: space-evenly;
        padding: 30px;
    }

    .sponsor-card:hover .redial h3,
    .sponsor-card:hover .redial a {
        display: block;
    }

    /* نخفف تدرج الخلفية للكارد الأساسي لما الكورة تظهر */
    .sponsor-card:hover .card-front {
        filter: brightness(0.8);
    }

    /* Front Card Content */
    .logo-container {
        width: 100%;
        height: 60%;
        display: flex;
        justify-content: center;
        align-items: center;
        margin-bottom: 20px;
    }

    .sponsor-logo {
        max-width: 100%;
        max-height: 100%;
        object-fit: contain;
        filter: grayscale(30%);
        transition: all 0.3s ease;
        margin-top: 110px;
    }

    .sponsor-card:hover .sponsor-logo {
        filter: grayscale(0%);
    }

    .sponsor-info {
        text-align: center;
    }

    .sponsor-name {
        font-size: 1.4rem;
        font-weight: 700;
        color: #333;
        margin-bottom: 5px;
    }

    .sponsor-category {
        font-size: 1rem;
        color: #cc252e;
    }
        .sponsors-cta {
        padding: 40px 30px;
        border-radius: 20px;
        margin-top: 60px;
        text-align: center;
        max-width: 700px;
        margin-left: auto;
        margin-right: auto;
    }

    .sponsors-cta .cta-title {
        font-size: 40px;
        font-weight: 800;
        margin-bottom: 15px;
        text-shadow: 1px 1px 3px rgba(0,0,0,0.2);
        color: #cc252e;
      }

      .sponsors-cta .cta-subtitle {
        font-size: 1.2rem;
        margin-bottom: 30px;
        opacity: 0.9;
        color: #000;
        font-weight: bold;
    }

    .sponsors-cta .cta-btn {
        display: inline-block;
        background: #fff;
        color: #cc252e;
        font-weight: 700;
        padding: 15px 40px;
        border-radius: 50px;
        text-decoration: none;
        font-size: 1.2rem;
        box-shadow: 0 5px 15px rgba(255, 255, 255, 0.6);
        transition: all 0.3s ease;
        cursor: pointer !important;
    }

    .sponsors-cta .cta-btn:hover {
        background: #ff7043;
        color: #fff;
        box-shadow: 0 8px 25px rgba(255, 112, 67, 0.6);
    }

    /* Responsive adjustments */
    @media (max-width: 576px) {
        .sponsors-cta {
            padding: 30px 20px;
            border-radius: 15px;
        }

        .sponsors-cta .cta-title {
            font-size: 1.8rem;
        }

        .sponsors-cta .cta-subtitle {
            font-size: 1rem;
        }

        .sponsors-cta .cta-btn {
            font-size: 1rem;
            padding: 12px 30px;
        }

      .redial h3,
      .redial a {
          font-size: 20px;
          margin: 2px 0;
      }

    .redial a {
      font-size: 10px;
      padding: 7px;
      border-radius: 5px
    }
    }

    /* Responsive Adjustments */
    @media (max-width: 992px) {
        .cards-grid {
            grid-template-columns: repeat(3, 1fr);
            gap: 25px;
        }

        .sponsor-card-wrapper {
            height: 320px;
        }

        .sponsors-title {
            font-size: 2.5rem;
        }
    }

    @media (max-width: 509px) {
      .sponsor-card {
        width: 140px;
        height: 185px;
      }
      .sponsor-category {
        font-size: 11px
      }
      .sponsor-info {
        padding: 10px 0;
      }
      .sponsor-logo {
        margin-top: 40px;
      }
      .sponsor-name {
    font-size: 1rem;
    font-weight: 700;
    color: #333;
    margin-bottom: 5px;
}
    }
</style>

<section class="breadcrumbs-custom bg-image context-dark" style="background-image: url(<?php echo e(asset('public/web/assets/images/bg-breadcrumbs-01-1894x424.jpg')); ?>);">
  <div class="container">
    <h3 class="breadcrumbs-custom-title" style="font-size: 2.2rem;"><?php echo e($sponsor_section->title[$locale] ?? ''); ?></h3>
  </div>
</section>

<div class="sponsors-container">
    <div class="container">
        <div class="sponsors-header">
            <h1 class="sponsors-title gre-title" style="font-size: 40px;font-weight: bolder;">Our Sponsors</h1>
        </div>

        <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="category-container">
                <div class="category-title">
                    <h2><?php echo e($locale == 'ar' ? $category->name : $category->name_en); ?></h2>
                </div>

                <?php if($category->sponsors->count() > 0): ?>
                    <div class="cards-grid">
    <?php $__currentLoopData = $category->sponsors->sortBy('orders'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sponsor): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="sponsor-card-wrapper">
                                <div class="sponsor-card">
                                    <!-- Front Side -->
                                    <div class="card-face card-front">
                                        <div class="logo-container">
                                            <img src="<?php echo e(asset('public/'.$sponsor->image)); ?>" alt="<?php echo e($sponsor->name); ?>" class="sponsor-logo">
                                        </div>
                                        <div class="sponsor-info">
                                        <!--    <h3 class="sponsor-name"><?php echo e($sponsor->name_en); ?></h3>-->
                                        <!--    <p class="sponsor-category"><?php echo e($locale == 'ar' ? $category->name : $category->name_en); ?></p>-->
                                        </div>

                                        <div class="redial">
                                            <h3><?php echo e($sponsor->name_en); ?></h3>
                                            <a href="<?php echo e($sponsor->link_profile); ?>">View Profile</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                <?php else: ?>
                    <div style="text-align: center; padding: 40px 20px; background: white; border-radius: 15px; box-shadow: 0 5px 15px rgba(0,0,0,0.05);">
                        <svg width="60" height="60" viewBox="0 0 24 24" fill="none" stroke="#cc252e" stroke-width="1.5" style="opacity: 0.7; margin-bottom: 20px;">
                            <circle cx="12" cy="12" r="10"></circle>
                            <line x1="12" y1="8" x2="12" y2="12"></line>
                            <line x1="12" y1="16" x2="12.01" y2="16"></line>
                        </svg>
                        <h4 style="color: #666; font-weight: 500;"><?php echo e(__('No sponsors available in this category')); ?></h4>
                    </div>
                <?php endif; ?>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

       
    </div>
</div>
<section class="section section-lg text-center" style="color: #fff; padding: 80px 0;">
    <div class="container">
        <div class="block-lg block-center">
            <h6 style="font-size: 20px; font-weight: 600; color: #000; margin-bottom: 10px; letter-spacing: 1px;">
                Become a sponsor.
            </h6>
      <h2 class="promo-title"
    style="
        font-size: 60px;
        font-weight: 900;
        line-height: 1.2;
        margin-bottom: 10px;
        background: linear-gradient(270deg, #000000, #cc252e, #000000);
        background-size: 600% 600%;
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        animation: gradientShift 5s ease infinite;
        text-transform: none;
    ">
    Let’s become a part of our conference
</h2>

            <a href="<?php echo e(route('web.becomesponsor')); ?>" class="btn btn-lg" style="
                padding: 14px 32px;
                font-size: 18px;
                font-weight: bold;
                color: #fff;
                background: linear-gradient(90deg, #000, #cc252e);
                border-radius: 50px;
                text-decoration: none;
                transition: all 0.3s ease-in-out;"
                onmouseover="this.style.background='linear-gradient(90deg, #000, #cc252e)'; this.style.color='#fff'"
                onmouseout="this.style.background='linear-gradient(90deg, #000, #cc252e)'; this.style.color='#fff'">
                Become a Sponsor
            </a>
        </div>
    </div>
</section>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('web.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/u552947370/domains/affiliatesummitglobal.com/public_html/resources/views/web/content/sponsor.blade.php ENDPATH**/ ?>