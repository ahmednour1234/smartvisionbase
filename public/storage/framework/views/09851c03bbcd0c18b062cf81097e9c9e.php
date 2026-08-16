<?php $__env->startSection('title', __('sponsor.edit')); ?>

<?php $__env->startSection('content'); ?>
<div class="card">
  <div class="card-header">
    <h5 class="mb-0"><?php echo e(__('sponsor.edit')); ?></h5>
  </div>
  <div class="card-body">
    <form action="<?php echo e(route('admin.sponsors.update', $sponsor->id)); ?>" method="POST" enctype="multipart/form-data">
      <?php echo csrf_field(); ?>
      <?php echo method_field('PUT'); ?>
      <?php echo $__env->make('content.sponsors._form', ['sponsor' => $sponsor], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
      <div class="mt-3 text-center">
        <button type="submit" class="btn btn-primary"><?php echo e(__('general.update')); ?></button>
        <a href="<?php echo e(route('admin.sponsors.index')); ?>" class="btn btn-secondary"><?php echo e(__('general.cancel')); ?></a>
      </div>
    </form>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.layoutMaster', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/u552947370/domains/affiliatesummitglobal.com/public_html/resources/views/content/sponsors/edit.blade.php ENDPATH**/ ?>