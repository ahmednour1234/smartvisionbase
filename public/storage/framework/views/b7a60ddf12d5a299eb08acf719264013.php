<?php $__env->startSection('title', __('event_schedule.edit')); ?>

<?php $__env->startSection('content'); ?>
<div class="card">
  <div class="card-header">
    <h5 class="mb-0"><?php echo e(__('event_schedule.edit')); ?></h5>
  </div>
  <div class="card-body">
    <form action="<?php echo e(route('admin.event_schedules.update', $eventSchedule->id)); ?>" method="POST" enctype="multipart/form-data">
      <?php echo csrf_field(); ?>
      <?php echo method_field('PUT'); ?>

      <?php echo $__env->make('content.event_schedules._form', ['eventSchedule' => $eventSchedule], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

      <div class="mt-4 text-center">
        <button type="submit" class="btn btn-primary">
          <i class="fas fa-save me-1"></i> <?php echo e(__('general.save')); ?>

        </button>
        <a href="<?php echo e(route('admin.event_schedules.index')); ?>" class="btn btn-outline-secondary ms-2">
          <?php echo e(__('general.cancel')); ?>

        </a>
      </div>
    </form>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.layoutMaster', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/u552947370/domains/affiliatesummitglobal.com/public_html/resources/views/content/event_schedules/edit.blade.php ENDPATH**/ ?>