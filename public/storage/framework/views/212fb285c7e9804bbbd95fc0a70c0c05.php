
<?php $__env->startSection('title','Create Time'); ?>
<?php $__env->startSection('content'); ?>
<?php if($errors->any()): ?> <div class="alert alert-danger"><?php echo e($errors->first()); ?></div> <?php endif; ?>
<div class="card shadow-sm">
  <div class="card-body">
    <form method="post" action="<?php echo e(route('dashboard.speaker-times.store')); ?>">
      <?php echo csrf_field(); ?>
      <?php echo $__env->make('content.speaker_times._form', ['time'=>null], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
      <div class="mt-3 d-flex gap-2">
        <button class="btn btn-primary"><i class="fas fa-save me-1"></i> Save</button>
        <a href="<?php echo e(route('dashboard.speaker-times.index')); ?>" class="btn btn-light">Back</a>
      </div>
    </form>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.layoutMaster', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/u552947370/domains/affiliatesummitglobal.com/public_html/resources/views/content/speaker_times/create.blade.php ENDPATH**/ ?>