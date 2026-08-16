<?php
  $isAr = app()->getLocale()==='ar';
  $t = fn($ar,$en)=> $isAr ? $ar : $en;
?>

<div class="row g-3">
  <div class="col-md-4">
    <label class="form-label"><?php echo e($t('التاريخ','Date')); ?></label>
    <input type="date" name="date" class="form-control" required
           value="<?php echo e(old('date', isset($time) && optional($time->date)->format ? $time->date->format('Y-m-d') : (isset($time->date) ? $time->date : ''))); ?>">
    <div class="form-help"><?php echo e($t('اختر يوم الموعد','Pick the appointment day')); ?></div>
  </div>

  <div class="col-md-4">
    <label class="form-label"><?php echo e($t('من','From')); ?></label>
    <div class="input-group">
      <span class="input-group-text"><i class="far fa-clock"></i></span>
      <input type="time" name="time_from" class="form-control" required
             value="<?php echo e(old('time_from', $time->time_from ?? '')); ?>">
    </div>
    <div class="form-help"><?php echo e($t('وقت بداية الموعد (ساعة:دقيقة)','Start time (HH:MM)')); ?></div>
  </div>

  <div class="col-md-4">
    <label class="form-label d-flex justify-content-between">
      <span><?php echo e($t('إلى','To')); ?></span>
      <span class="text-muted" style="font-weight:normal"><?php echo e($t('المدة','Duration')); ?></span>
    </label>
    <div class="input-group">
      <span class="input-group-text"><i class="far fa-clock"></i></span>
      <input type="time" name="time_to" class="form-control" required
             value="<?php echo e(old('time_to', $time->time_to ?? '')); ?>">
      <select id="duration" class="form-select" style="max-width: 140px">
        <option value="15">15 <?php echo e($t('دقيقة','min')); ?></option>
        <option value="30" selected>30 <?php echo e($t('دقيقة','min')); ?></option>
        <option value="45">45 <?php echo e($t('دقيقة','min')); ?></option>
        <option value="60">60 <?php echo e($t('دقيقة','min')); ?></option>
      </select>
    </div>
    <div class="form-help"><?php echo e($t('يمكنك اختيار مدة وسيتم حساب وقت الانتهاء تلقائيًا','Pick a duration to auto-calc end time')); ?></div>
  </div>

  <div class="col-md-4">
    <label class="form-label"><?php echo e($t('الحالة','Status')); ?></label>
    <select name="status" class="form-select">
      <?php $__currentLoopData = ['available'=>$t('متاح','Available'),'booked'=>$t('محجوز','Booked'),'unavailable'=>$t('غير متاح','Unavailable')]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k=>$v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <option value="<?php echo e($k); ?>" <?php if(old('status', $time->status ?? 'available')==$k): echo 'selected'; endif; ?>><?php echo e($v); ?></option>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </select>
    <div class="form-help"><?php echo e($t('اضبط حالة الفتحة الزمنية','Set the time slot status')); ?></div>
  </div>

  <div class="col-md-4 d-flex align-items-end">
    <div class="form-check form-switch">
      <input class="form-check-input" type="checkbox" name="active" value="1"
             id="activeSwitch"
             <?php echo e(old('active', $time->active ?? true) ? 'checked' : ''); ?>>
      <label class="form-check-label" for="activeSwitch"><?php echo e($t('مُفعّل','Active')); ?></label>
    </div>
  </div>
</div>
<?php /**PATH /home/u552947370/domains/affiliatesummitglobal.com/public_html/resources/views/content/speakers/times/_form.blade.php ENDPATH**/ ?>