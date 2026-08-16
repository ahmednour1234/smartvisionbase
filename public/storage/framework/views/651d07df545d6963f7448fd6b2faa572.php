<?php echo csrf_field(); ?>

<div class="row mb-3">
  <div class="col-md-6">
    <label for="event_id" class="form-label"><?php echo e(__('event_schedule.event')); ?></label>
    <select name="event_id" id="event_id" class="form-select" required>
      <option value="">-- <?php echo e(__('event_schedule.select_event')); ?> --</option>
      <?php $__currentLoopData = $events; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $event): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <option value="<?php echo e($event->id); ?>" <?php echo e((old('event_id', $eventSchedule->event_id ?? '') == $event->id) ? 'selected' : ''); ?>>
          <?php echo e(app()->getLocale() == 'ar' ? $event->name_ar : $event->name_en); ?>

        </option>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </select>
  </div>
  <div class="col-md-6">
    <label for="title_ar" class="form-label"><?php echo e(__('event_schedule.name_ar')); ?></label>
    <input type="text" class="form-control" name="title_ar" id="title_ar" value="<?php echo e(old('title_ar', $eventSchedule->title_ar ?? '')); ?>" required>
  </div>
</div>

<div class="row mb-3">
  <div class="col-md-6">
    <label for="title_en" class="form-label"><?php echo e(__('event_schedule.name_en')); ?></label>
    <input type="text" class="form-control" name="title_en" id="title_en" value="<?php echo e(old('title_en', $eventSchedule->title_en ?? '')); ?>" required>
  </div>
  <div class="col-md-6">
    <label for="max_attendees" class="form-label"><?php echo e(__('event_schedule.max_attendees')); ?></label>
    <input type="number" class="form-control" name="max_attendees" id="max_attendees" value="<?php echo e(old('max_attendees', $eventSchedule->max_attendees ?? '')); ?>">
  </div>
</div>

<div class="row mb-3">
  <div class="col-md-6">
    <label for="location_ar" class="form-label"><?php echo e(__('event_schedule.location_ar')); ?></label>
    <input type="text" class="form-control" name="location_ar" id="location_ar" value="<?php echo e(old('location_ar', $eventSchedule->location_ar ?? '')); ?>">
  </div>
  <div class="col-md-6">
    <label for="location_en" class="form-label"><?php echo e(__('event_schedule.location_en')); ?></label>
    <input type="text" class="form-control" name="location_en" id="location_en" value="<?php echo e(old('location_en', $eventSchedule->location_en ?? '')); ?>">
  </div>
</div>

<div class="row mb-3">
  <div class="col-md-6">
    <label for="description_ar" class="form-label"><?php echo e(__('event_schedule.description_ar')); ?></label>
    <textarea class="form-control" name="description_ar" id="description_ar"><?php echo e(old('description_ar', $eventSchedule->description_ar ?? '')); ?></textarea>
  </div>
  <div class="col-md-6">
    <label for="description_en" class="form-label"><?php echo e(__('event_schedule.description_en')); ?></label>
    <textarea class="form-control" name="description_en" id="description_en"><?php echo e(old('description_en', $eventSchedule->description_en ?? '')); ?></textarea>
  </div>
</div>

<div class="row mb-3">
  <div class="col-md-4">
    <label for="start_datetime" class="form-label"><?php echo e(__('event_schedule.start_time')); ?></label>
    <input type="datetime-local" class="form-control" name="start_datetime" id="start_datetime" value="<?php echo e(old('start_datetime', $eventSchedule->start_datetime ?? '')); ?>" required>
  </div>
  <div class="col-md-4">
    <label for="end_datetime" class="form-label"><?php echo e(__('event_schedule.end_time')); ?></label>
    <input type="datetime-local" class="form-control" name="end_datetime" id="end_datetime" value="<?php echo e(old('end_datetime', $eventSchedule->end_datetime ?? '')); ?>" required>
  </div>
</div>

<div class="row mb-3">
  <div class="col-md-6">
    <label for="logo" class="form-label"><?php echo e(__('event_schedule.logo')); ?></label>
    <input type="file" class="form-control" name="logo" id="logo">
    <?php if(isset($eventSchedule) && $eventSchedule->logo): ?>
      <img src="<?php echo e(asset($eventSchedule->logo)); ?>" class="mt-2" width="80" height="80">
    <?php endif; ?>
  </div>
  <div class="col-md-6">
    <label for="active" class="form-label"><?php echo e(__('event_schedule.status')); ?></label>
    <select name="active" id="active" class="form-select">
      <option value="1" <?php echo e(old('active', $eventSchedule->active ?? 1) == 1 ? 'selected' : ''); ?>><?php echo e(__('event_schedule.active')); ?></option>
      <option value="0" <?php echo e(old('active', $eventSchedule->active ?? 1) == 0 ? 'selected' : ''); ?>><?php echo e(__('event_schedule.inactive')); ?></option>
    </select>
  </div>
</div>

<div class="row mb-3">
  <div class="col-md-6">
    <label for="status" class="form-label"><?php echo e(__('event_schedule.status_event')); ?></label>
    <select name="status" id="status" class="form-select" required>
      <option value="upcoming" <?php echo e(old('status', $eventSchedule->status ?? 'upcoming') == 'upcoming' ? 'selected' : ''); ?>><?php echo e(__('event_schedule.status_upcoming')); ?></option>
      <option value="ongoing" <?php echo e(old('status', $eventSchedule->status ?? '') == 'ongoing' ? 'selected' : ''); ?>><?php echo e(__('event_schedule.status_ongoing')); ?></option>
      <option value="completed" <?php echo e(old('status', $eventSchedule->status ?? '') == 'completed' ? 'selected' : ''); ?>><?php echo e(__('event_schedule.status_completed')); ?></option>
      <option value="canceled" <?php echo e(old('status', $eventSchedule->status ?? '') == 'canceled' ? 'selected' : ''); ?>><?php echo e(__('event_schedule.status_canceled')); ?></option>
    </select>
  </div>
</div>



<div class="row mb-3">
  <div class="col-md-12">
    <label class="form-label"><?php echo e(__('event_schedule.speakers')); ?></label>
    <div class="d-flex flex-wrap gap-2">
      <?php
        $selectedSpeakers = old('speaker_ids', isset($eventSchedule) ? $eventSchedule->speakers->pluck('id')->toArray() : []);
      ?>

      <?php $__currentLoopData = $speakers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $speaker): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="form-check me-3">
          <input
            class="form-check-input"
            type="checkbox"
            name="speaker_ids[]"
            value="<?php echo e($speaker->id); ?>"
            id="speaker_<?php echo e($speaker->id); ?>"
            <?php echo e(in_array($speaker->id, $selectedSpeakers) ? 'checked' : ''); ?>

          >
          <label class="form-check-label" for="speaker_<?php echo e($speaker->id); ?>">
            <?php echo e(app()->getLocale() == 'ar' ? $speaker->name_ar : $speaker->name_en); ?>

          </label>
        </div>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
  </div>
</div>
<?php /**PATH /home/u552947370/domains/affiliatesummitglobal.com/public_html/resources/views/content/event_schedules/_form.blade.php ENDPATH**/ ?>