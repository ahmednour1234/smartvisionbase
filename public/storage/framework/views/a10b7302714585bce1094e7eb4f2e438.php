<?php $__env->startSection('title', __('multi_media.show')); ?>

<?php $__env->startSection('content'); ?>
<div class="card shadow-sm">
  <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
    <h5 class="mb-0">
      <i class="bi bi-eye me-2"></i> <?php echo e(__('multi_media.show')); ?> - <?php echo e($media->name_ar); ?>

    </h5>
    <a href="<?php echo e(route('dashboard.multi-medias.index')); ?>" class="btn btn-outline-light btn-sm">
      <i class="bi bi-arrow-left"></i> <?php echo e(__('multi_media.back')); ?>

    </a>
  </div>

  <div class="card-body">
    <div class="row gy-4">
      
      <div class="col-md-6">
        <strong class="text-muted"><?php echo e(__('multi_media.name_ar')); ?>:</strong>
        <div class="border p-2 rounded"><?php echo e($media->name_ar); ?></div>
      </div>
      <div class="col-md-6">
        <strong class="text-muted"><?php echo e(__('multi_media.name_en')); ?>:</strong>
        <div class="border p-2 rounded"><?php echo e($media->name_en); ?></div>
      </div>

      
      <div class="col-md-6">
        <strong class="text-muted"><?php echo e(__('multi_media.date')); ?>:</strong>
        <div class="border p-2 rounded"><?php echo e($media->date); ?></div>
      </div>

      
      <div class="col-md-6">
        <strong class="text-muted"><?php echo e(__('multi_media.category')); ?>:</strong>
        <div class="border p-2 rounded"><?php echo e($media->category?->name_ar ?? '-'); ?></div>
      </div>

      
      <div class="col-md-6">
        <strong class="text-muted"><?php echo e(__('multi_media.active')); ?>:</strong>
        <div class="mt-1">
          <span class="badge bg-<?php echo e($media->active ? 'success' : 'secondary'); ?>">
            <?php echo e($media->active ? __('multi_media.status_active') : __('multi_media.status_inactive')); ?>

          </span>
        </div>
      </div>
    </div>

    
    <?php if(!empty($media->images)): ?>
      <hr>
      <h5 class="mt-4"><?php echo e(__('multi_media.images')); ?></h5>
      <div class="row">
        <?php $__currentLoopData = $media->images; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $image): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <div class="col-md-3 col-6 mb-3">
            <div class="border rounded p-1 h-100 text-center">
              <img src="<?php echo e(asset($image)); ?>" alt="image" class="img-fluid rounded shadow-sm" style="max-height: 180px;">
            </div>
          </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </div>
    <?php endif; ?>

    
<?php
    function convertToEmbed($url) {
        if (str_contains($url, 'watch?v=')) {
            return str_replace('watch?v=', 'embed/', $url);
        }
        return $url;
    }
?>

<?php if(!empty($media->links)): ?>
  <hr>
  <h5 class="mt-4"><?php echo e(__('multi_media.links')); ?></h5>
  <div class="row">
    <?php $__currentLoopData = $media->links; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $link): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
      <div class="col-md-6 mb-3">
        <div class="ratio ratio-16x9 border rounded">
          <iframe src="<?php echo e(convertToEmbed($link)); ?>" title="video" allowfullscreen></iframe>
        </div>
      </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
  </div>
<?php endif; ?>

  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.layoutMaster', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/u552947370/domains/affiliatesummitglobal.com/public_html/resources/views/content/multi_mediall/show.blade.php ENDPATH**/ ?>