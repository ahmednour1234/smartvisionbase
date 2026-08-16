<?php
  $isEdit = isset($booking) && $booking;
?>

<div class="row g-3">
  <div class="col-md-4">
    <label class="form-label">Speaker (type=1)</label>
    <select name="speaker_id" class="form-select" required>
      <option value="">Choose…</option>
      <?php $__currentLoopData = $speakers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <option value="<?php echo e($sp->id); ?>" <?php if(old('speaker_id', $booking->speaker_id ?? request('speaker_id'))==$sp->id): echo 'selected'; endif; ?>>
          <?php echo e(app()->getLocale()==='ar' ? ($sp->name_ar ?? $sp->id) : ($sp->name_en ?? $sp->id)); ?>

        </option>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </select>
  </div>

  <div class="col-md-4">
    <label class="form-label">Client</label>
    <select name="client_id" class="form-select" required>
      <option value="">Choose…</option>
      <?php $__currentLoopData = $clients; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <option value="<?php echo e($c->id); ?>" <?php if(old('client_id', $booking->client_id ?? null)==$c->id): echo 'selected'; endif; ?>><?php echo e($c->name ?? $c->id); ?></option>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </select>
  </div>

  <div class="col-md-4">
    <label class="form-label">Speaker Time (optional)</label>
    <select name="speaker_time_id" class="form-select">
      <option value="">—</option>
      <?php $__currentLoopData = $speakerTimes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $st): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <option value="<?php echo e($st->id); ?>" <?php if(old('speaker_time_id', $booking->speaker_time_id ?? null)==$st->id): echo 'selected'; endif; ?>>
          #<?php echo e($st->id); ?> — <?php echo e(optional($st->date)->format('Y-m-d') ?? $st->date); ?> <?php echo e($st->time_from); ?>→<?php echo e($st->time_to); ?>

        </option>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </select>
    <?php if(!count($speakerTimes)): ?>
      <small class="text-muted">اختر متحدثًا أولًا من الأعلى ثم اضغط Enter لإعادة تحميل القائمة.</small>
    <?php endif; ?>
  </div>

  <div class="col-md-3">
    <label class="form-label">From (datetime)</label>
    <input type="datetime-local" name="time_from" class="form-control" required
           value="<?php echo e(old('time_from', isset($booking->time_from)?$booking->time_from->format('Y-m-d\TH:i'):'' )); ?>">
  </div>

  <div class="col-md-3">
    <label class="form-label">To (datetime)</label>
    <input type="datetime-local" name="time_to" class="form-control" required
           value="<?php echo e(old('time_to', isset($booking->time_to)?$booking->time_to->format('Y-m-d\TH:i'):'' )); ?>">
  </div>

  <div class="col-md-3">
    <label class="form-label">Status</label>
    <select name="status" class="form-select" required>
      <?php $__currentLoopData = ['pending','confirmed','cancelled']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $st): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <option value="<?php echo e($st); ?>" <?php if(old('status', $booking->status ?? 'pending')==$st): echo 'selected'; endif; ?>><?php echo e(ucfirst($st)); ?></option>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </select>
  </div>

  <div class="col-md-3 d-flex align-items-end">
    <div class="form-check form-switch">
      <input class="form-check-input" type="checkbox" name="active" value="1" id="activeSwitch" <?php echo e(old('active', $booking->active ?? true)?'checked':''); ?>>
      <label class="form-check-label" for="activeSwitch">Active</label>
    </div>
  </div>
</div>
<?php /**PATH /home/u552947370/domains/affiliatesummitglobal.com/public_html/resources/views/content/bookings/_form.blade.php ENDPATH**/ ?>