<?php $__env->startSection('title', __('event_schedule.create')); ?>

<?php $__env->startSection('content'); ?>
<div class="card">
  <div class="card-header">
    <h5><?php echo e(__('event_schedule.create')); ?></h5>
  </div>
  <div class="card-body">
    <form action="<?php echo e(route('admin.event_schedules.store')); ?>" method="POST" enctype="multipart/form-data">
      <?php echo csrf_field(); ?>
      <?php echo $__env->make('content.event_schedules._form', ['eventSchedule' => null], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
      <div class="mt-3 text-center">
        <button type="submit" class="btn btn-primary">
          <i class="fas fa-save"></i> <?php echo e(__('general.save')); ?>

        </button>
      </div>
    </form>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.layoutMaster', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/u552947370/domains/affiliatesummitglobal.com/public_html/resources/views/content/event_schedules/create.blade.php ENDPATH**/ ?>