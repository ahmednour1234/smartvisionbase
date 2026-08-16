<?php $__env->startSection('title', __('event_schedule.details')); ?>

<?php $__env->startSection('content'); ?>
<div class="card">
  <div class="card-header bg-primary text-white">
    <h5 class="mb-0"><i class="bi bi-calendar-event me-2"></i> <?php echo e(__('event_schedule.details')); ?></h5>
  </div>

  <div class="card-body">
    <div class="row mb-3">
      <div class="col-md-6">
        <strong><?php echo e(__('event_schedule.title_ar')); ?>:</strong>
        <p><?php echo e($eventSchedule->title_ar); ?></p>
      </div>
      <div class="col-md-6">
        <strong><?php echo e(__('event_schedule.title_en')); ?>:</strong>
        <p><?php echo e($eventSchedule->title_en); ?></p>
      </div>
    </div>

    <div class="row mb-3">
      <div class="col-md-6">
        <strong><?php echo e(__('event_schedule.description_ar')); ?>:</strong>
        <p><?php echo e($eventSchedule->description_ar ?: '---'); ?></p>
      </div>
      <div class="col-md-6">
        <strong><?php echo e(__('event_schedule.description_en')); ?>:</strong>
        <p><?php echo e($eventSchedule->description_en ?: '---'); ?></p>
      </div>
    </div>

    <div class="row mb-3">
      <div class="col-md-6">
        <strong><?php echo e(__('event_schedule.location_ar')); ?>:</strong>
        <p><?php echo e($eventSchedule->location_ar); ?></p>
      </div>
      <div class="col-md-6">
        <strong><?php echo e(__('event_schedule.location_en')); ?>:</strong>
        <p><?php echo e($eventSchedule->location_en); ?></p>
      </div>
    </div>

    <div class="row mb-3">
      <div class="col-md-4">
        <strong><?php echo e(__('event_schedule.start_datetime')); ?>:</strong>
        <p><?php echo e($eventSchedule->start_datetime); ?></p>
      </div>
      <div class="col-md-4">
        <strong><?php echo e(__('event_schedule.end_datetime')); ?>:</strong>
        <p><?php echo e($eventSchedule->end_datetime); ?></p>
      </div>
      <div class="col-md-4">
        <strong><?php echo e(__('event_schedule.max_attendees')); ?>:</strong>
        <p><?php echo e($eventSchedule->max_attendees); ?></p>
      </div>
    </div>

    <div class="row mb-3">
      <div class="col-md-6">
        <strong><?php echo e(__('event_schedule.status')); ?>:</strong>
        <p>
          <span class="badge bg-<?php echo e($eventSchedule->status ? 'success' : 'secondary'); ?>">
            <?php echo e($eventSchedule->status ? __('event_schedule.active') : __('event_schedule.inactive')); ?>

          </span>
        </p>
      </div>
      <div class="col-md-6">
        <strong><?php echo e(__('event_schedule.event')); ?>:</strong>
        <p><?php echo e(app()->getLocale() == 'ar' ? $eventSchedule->event?->title_ar : $eventSchedule->event?->title_en); ?></p>
      </div>
    </div>

    <?php if($eventSchedule->logo): ?>
    <div class="mb-3">
      <strong><?php echo e(__('event_schedule.logo')); ?>:</strong><br>
      <img src="<?php echo e(asset( $eventSchedule->logo)); ?>" width="120" height="120" class="mt-2 rounded border">
    </div>
    <?php endif; ?>

    <?php if($eventSchedule->speakers && $eventSchedule->speakers->count()): ?>
    <div class="mb-3">
      <strong><?php echo e(__('event_schedule.speakers')); ?>:</strong>
      <ul class="list-unstyled ms-3 mt-2">
        <?php $__currentLoopData = $eventSchedule->speakers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $speaker): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <li>🎤 <?php echo e($speaker->name); ?></li>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </ul>
    </div>
    <?php endif; ?>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.layoutMaster', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/u552947370/domains/affiliatesummitglobal.com/public_html/resources/views/content/event_schedules/show.blade.php ENDPATH**/ ?>