

<?php $__env->startSection('title', __('speaker.edit')); ?>

<?php $__env->startSection('content'); ?>
<div class="card">
  <div class="card-header d-flex justify-content-between align-items-center">
    <h5 class="mb-0"><?php echo e(__('speaker.edit')); ?></h5>
    <?php if(session('success')): ?>
      <span class="badge bg-success"><?php echo e(session('success')); ?></span>
    <?php endif; ?>
  </div>

  <div class="card-body">
    <form action="<?php echo e(route('updatenew', [$type, $speaker->id])); ?>" method="POST" enctype="multipart/form-data">
      <?php echo csrf_field(); ?>
      
      <?php echo $__env->make('content.speakers._form', ['speaker' => $speaker, 'type' => $type], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

      <button type="submit" class="btn btn-primary mt-3">
        <i class="fas fa-save me-1"></i> <?php echo e(__('general.update')); ?>

      </button>
    </form>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.layoutMaster', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/u552947370/domains/affiliatesummitglobal.com/public_html/resources/views/content/speakers/edit.blade.php ENDPATH**/ ?>