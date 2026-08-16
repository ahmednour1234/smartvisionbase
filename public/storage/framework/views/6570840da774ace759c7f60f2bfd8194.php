<?php $__env->startSection('content'); ?>
<?php
    $locale = app()->getLocale();
?>
<style>
     @media (max-width: 991.98px) {
        .breadcrumbs-custom {
            height: 350px !important; /* صورة أطول في الأجهزة الصغيرة */
            background-size: cover;
            background-position: center;
        }

        .breadcrumbs-custom-title {
            font-size: 28px;
            padding-top:150px;
        }
    }

    .breadcrumbs-custom {
        background-size: cover;
        background-position: center;
    }
</style>
<!-- Breadcrumbs -->
<section class="breadcrumbs-custom bg-image context-dark"
         style="background-image: url(<?php echo e(asset('public/web/assets/images/bg-breadcrumbs-01-1894x424.jpg')); ?>); background-size: cover;">
    <div class="container">

        <h3 class="breadcrumbs-custom-title text-white fw-bold"><?php echo e(__('Multi Media')); ?></h3>
    </div>
</section>

<!-- Multi Media Categories -->
<section class="section multimedia-categories section-lg bg-light text-center">
    <div class="container">
        <div class="row row-30 justify-content-center">

            <?php $__currentLoopData = $multi_media_categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="col-md-6 col-lg-4">
                    <div class="card multimedia-card shadow-sm rounded-4 overflow-hidden border border-2 border-primary-subtle h-100">
                        <!-- Image Frame -->
                        <div class="image-container-short border-bottom">
                            <img src="<?php echo e(asset('public/'.$category->logo)); ?>"
                                 alt="logo"
                                 class="img-fluid w-100 h-100 object-fit-cover">
                        </div>

                        <!-- Content -->
                        <div class="card-body px-4 py-3 text-start">
                          <a href="<?php echo e(route('web.multi_media.show',[$category->id])); ?>">
                            <h5 class="fw-bold text-primary text-center mb-2">
                                <?php echo e($category->{'name_' . $locale} ?? ''); ?>

                            </h5>
                          </a>
                            <p class="text-muted small text-wrap mb-0">
                                <?php echo e($category->{'description_' . $locale} ?? ''); ?>

                            </p>
                        </div>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

            <?php if($multi_media_categories->isEmpty()): ?>
                <div class="col-12">
                    <p class="text-muted"><?php echo e(__('No multimedia categories available.')); ?></p>
                </div>
            <?php endif; ?>

        </div>
    </div>
</section>

<!-- Styles -->
<style>
    .image-container-short {
        height: 200px;
        overflow: hidden;
        background-color: #f8f9fa;
    }

    .image-container-short img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .multimedia-card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        background-color: #fff;
    }

    .multimedia-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 12px 24px rgba(0, 0, 0, 0.1);
        border-color: #cc252;
    }
    .multimedia-categories {
      min-height: calc(100vh - 424px); /* Adjust based on your header/footer height */
      display: flex;
      align-items: center;
      justify-content: center;
    }
    .multimedia-categories .container {
      width: 100%;
    }
    .multimedia-categories .container .col {
      width: 100%;
    }

</style>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('web.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/u552947370/domains/affiliatesummitglobal.com/public_html/resources/views/web/content/multimedia.blade.php ENDPATH**/ ?>