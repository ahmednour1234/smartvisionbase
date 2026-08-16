<?php $__env->startSection('title', __('multi_media.edit')); ?>

<?php $__env->startSection('content'); ?>
<div class="card">
  <div class="card-header">
    <h5 class="mb-0"><?php echo e(__('multi_media.edit')); ?></h5>
  </div>
  <div class="card-body">
    <form action="<?php echo e(route('dashboard.multimedia-categories.update', $category->id)); ?>" method="POST" enctype="multipart/form-data">
      <?php echo csrf_field(); ?>
      <?php echo method_field('PUT'); ?>
      <?php echo $__env->make('content.multi_media._form', ['button' => __('multi_media.update'), 'category' => $category], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    </form>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.layoutMaster', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/u552947370/domains/affiliatesummitglobal.com/public_html/resources/views/content/multi_media/edit.blade.php ENDPATH**/ ?>