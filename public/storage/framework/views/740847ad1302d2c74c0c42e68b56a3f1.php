<?php $isEdit = isset($time) && $time; ?>
<div class="row g-3">
  <div class="col-md-4">
    <label class="form-label">Speaker (type=1)</label>
    <select name="speaker_id" class="form-select" required>
      <option value="">Choose…</option>
      <?php $__currentLoopData = $speakers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <option value="<?php echo e($sp->id); ?>" <?php if(old('speaker_id', $time->speaker_id ?? null)==$sp->id): echo 'selected'; endif; ?>>
          <?php echo e(app()->getLocale()==='ar' ? ($sp->name_ar ?? $sp->id) : ($sp->name_en ?? $sp->id)); ?>

        </option>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </select>
  </div>
  <div class="col-md-3">
    <label class="form-label">Date</label>
    <input type="date" name="date" class="form-control" required
           value="<?php echo e(old('date', optional($time->date ?? null)->format('Y-m-d'))); ?>">
  </div>
  <div class="col-md-2">
    <label class="form-label">From</label>
    <input type="time" name="time_from" class="form-control" required
           value="<?php echo e(old('time_from', $time->time_from ?? '')); ?>">
  </div>
  <div class="col-md-2">
    <label class="form-label">To</label>
    <input type="time" name="time_to" class="form-control" required
           value="<?php echo e(old('time_to', $time->time_to ?? '')); ?>">
  </div>
  <div class="col-md-3">
    <label class="form-label">Status</label>
    <select name="status" class="form-select" required>
      <?php $__currentLoopData = ['available','booked','unavailable']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $st): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <option value="<?php echo e($st); ?>" <?php if(old('status', $time->status ?? 'available')==$st): echo 'selected'; endif; ?>><?php echo e(ucfirst($st)); ?></option>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </select>
  </div>
  <div class="col-md-2 d-flex align-items-end">
    <div class="form-check form-switch">
      <input class="form-check-input" type="checkbox" name="active" value="1" id="activeSwitch" <?php echo e(old('active', $time->active ?? true)?'checked':''); ?>>
      <label class="form-check-label" for="activeSwitch">Active</label>
    </div>
  </div>
</div>
<?php /**PATH /home/u552947370/domains/affiliatesummitglobal.com/public_html/resources/views/content/speaker_times/_form.blade.php ENDPATH**/ ?>