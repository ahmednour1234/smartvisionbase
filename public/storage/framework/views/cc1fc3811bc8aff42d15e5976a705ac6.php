<?php $__env->startSection('content'); ?>

<style>
  .gallery-item {
    overflow: hidden;
    border-radius: 10px;
    display: block;
    position: relative;
    width: 100%;
    aspect-ratio: 1 / 1; /* لحفظ النسبة 1:1 */
  }

  .gallery-item img {
    width: 100%;
    height: 100%;
    object-fit: cover; /* لملء الصورة داخل الإطار */
    transition: transform 0.3s ease-in-out, box-shadow 0.3s ease-in-out;
    border-radius: 10px;
  }

  .gallery-item:hover img {
    transform: scale(1.05);
    box-shadow: 0 8px 20px rgba(0,0,0,0.2);
  }

  @media (max-width: 576px) {
    .gallery-grid .col-6 {
      flex: 0 0 100%;
      max-width: 100%;
    }
  }
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

<?php
  use Carbon\Carbon;
  $locale = app()->getLocale();
?>

<!-- Breadcrumb Section -->
<section class="breadcrumbs-custom bg-image context-dark"
         style="background-image: url(<?php echo e(asset('public/web/assets/images/bg-breadcrumbs-01-1894x424.jpg')); ?>);">
  <div class="container">

    <h3 class="breadcrumbs-custom-title">Gallery</h3>
  </div>
</section>

<!-- Gallery Section -->
<?php if($gallery_section && $gallery_section->media_type === 'image'): ?>
<section class="section bg-default mt-5 pt-5">
  <div class="container">
    <h6 class="text-center text-secondary"><?php echo e($gallery_section->title[$locale] ?? ''); ?></h6>
    <h3 class="text-center mb-5"><?php echo e($gallery_section->description[$locale] ?? ''); ?></h3>

    <div class="row gallery-grid">
      <?php $__currentLoopData = $gallieries; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $gallery): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="col-6 col-md-4 col-lg-3 mb-4">
          <a href="<?php echo e(asset('public/'.$gallery->image)); ?>" data-lightgallery="item" class="gallery-item">
            <img src="<?php echo e(asset('public/'.$gallery->image)); ?>" alt="gallery">
          </a>
        </div>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
  </div>
</section>
<?php endif; ?>

<?php $__env->stopSection(); ?>

<?php $__env->startPush('styles'); ?>
<!-- LightGallery CSS -->
<link href="https://cdn.jsdelivr.net/npm/lightgallery@2.7.1/css/lightgallery-bundle.min.css" rel="stylesheet" />
<?php $__env->stopPush(); ?>

<?php $__env->startPush('scripts'); ?>
<!-- LightGallery JS -->
<script src="https://cdn.jsdelivr.net/npm/lightgallery@2.7.1/lightgallery.min.js"></script>
<script>
  lightGallery(document.querySelector('.gallery-grid'), {
    selector: '[data-lightgallery="item"]'
  });
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('web.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/u552947370/domains/affiliatesummitglobal.com/public_html/resources/views/web/content/gallery.blade.php ENDPATH**/ ?>