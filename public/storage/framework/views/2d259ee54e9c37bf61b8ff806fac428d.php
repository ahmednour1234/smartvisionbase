// 1. create.blade.php



<?php $__env->startSection('title', __('speaker.add')); ?>

<?php $__env->startSection('content'); ?>
<div class="card">
  <h5 class="mb-0">
    <?php if(isset($type) && $type == 1): ?>
        <?php echo e(__('speaker.add')); ?>

    <?php elseif(isset($type) && $type == 2): ?>
        <?php echo e(__('speaker.special_guests_title')); ?>

    <?php else: ?>
        <?php echo e(__('speaker.add')); ?>

    <?php endif; ?>
</h5>

  <div class="card-body">
    <form action="<?php echo e(route('admin.speakers.store')); ?>" method="POST" enctype="multipart/form-data">
      <?php echo csrf_field(); ?>
      <?php echo $__env->make('content.speakers._form', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
      <button type="submit" class="btn btn-primary mt-3">
        <i class="fas fa-save me-1"></i> <?php echo e(__('general.save')); ?>

      </button>
    </form>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.layoutMaster', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/u552947370/domains/affiliatesummitglobal.com/public_html/resources/views/content/speakers/create.blade.php ENDPATH**/ ?>