
<?php $__env->startSection('title','Times Sheets'); ?>
<?php $__env->startSection('content'); ?>
<?php
  $isAr = app()->getLocale()==='ar';
  $t = fn($ar,$en)=> $isAr? $ar:$en;
?>

<?php if(session('success')): ?> <div class="alert alert-success"><?php echo e(session('success')); ?></div> <?php endif; ?>
<?php if($errors->any()): ?>   <div class="alert alert-danger"><?php echo e($errors->first()); ?></div> <?php endif; ?>

<div class="card shadow-sm mb-3">
  <div class="card-body">
    <form class="row g-3">
      <div class="col-md-3">
        <label class="form-label"><?php echo e($t('المتحدث','Speaker')); ?></label>
        <select name="speaker_id" class="form-select">
          <option value=""><?php echo e($t('الكل','All')); ?></option>
          <?php $__currentLoopData = $speakers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($sp->id); ?>" <?php if(($filters['speaker_id']??null)==$sp->id): echo 'selected'; endif; ?>>
              <?php echo e($isAr?($sp->name_ar??$sp->id):($sp->name_en??$sp->id)); ?>

            </option>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
      </div>
      <div class="col-md-2">
        <label class="form-label"><?php echo e($t('من يوم','From day')); ?></label>
        <input type="date" name="date_from" value="<?php echo e($filters['date_from']); ?>" class="form-control">
      </div>
      <div class="col-md-2">
        <label class="form-label"><?php echo e($t('إلى يوم','To day')); ?></label>
        <input type="date" name="date_to" value="<?php echo e($filters['date_to']); ?>" class="form-control">
      </div>
      <div class="col-md-2">
        <label class="form-label"><?php echo e($t('من وقت','From time')); ?></label>
        <input type="time" name="time_from" value="<?php echo e($filters['time_from']); ?>" class="form-control">
      </div>
      <div class="col-md-2">
        <label class="form-label"><?php echo e($t('إلى وقت','To time')); ?></label>
        <input type="time" name="time_to" value="<?php echo e($filters['time_to']); ?>" class="form-control">
      </div>
      <div class="col-md-1">
        <label class="form-label"><?php echo e($t('مفعل','Active')); ?></label>
        <select name="active" class="form-select">
          <option value=""><?php echo e($t('الكل','All')); ?></option>
          <option value="1" <?php if(($filters['active']??null)===true): echo 'selected'; endif; ?>> <?php echo e($t('نعم','Yes')); ?> </option>
          <option value="0" <?php if(($filters['active']??null)===false): echo 'selected'; endif; ?>> <?php echo e($t('لا','No')); ?> </option>
        </select>
      </div>
      <div class="col-md-2">
        <label class="form-label"><?php echo e($t('الحالة','Status')); ?></label>
        <select name="status" class="form-select">
          <option value=""><?php echo e($t('الكل','All')); ?></option>
          <option value="available"  <?php if(($filters['status']??'')==='available'): echo 'selected'; endif; ?>><?php echo e($t('متاح','Available')); ?></option>
          <option value="booked"     <?php if(($filters['status']??'')==='booked'): echo 'selected'; endif; ?>><?php echo e($t('محجوز','Booked')); ?></option>
          <option value="unavailable"<?php if(($filters['status']??'')==='unavailable'): echo 'selected'; endif; ?>><?php echo e($t('غير متاح','Unavailable')); ?></option>
        </select>
      </div>
      <div class="col-md-12 d-flex gap-2">
        <button class="btn btn-primary"><i class="fas fa-filter me-1"></i> <?php echo e($t('تصفية','Filter')); ?></button>
        <a href="<?php echo e(route('dashboard.speaker-times.index')); ?>" class="btn btn-light"><?php echo e($t('إعادة ضبط','Reset')); ?></a>
        <a href="<?php echo e(route('dashboard.speaker-times.create')); ?>" class="btn btn-success ms-auto"><i class="fas fa-plus me-1"></i> <?php echo e($t('إضافة موعد','Create')); ?></a>
      </div>
    </form>
  </div>
</div>

<div class="card shadow-sm">
  <div class="table-responsive">
    <table class="table table-striped align-middle mb-0">
      <thead>
        <tr>
          <th>#</th>
          <th><?php echo e($t('المتحدث','Speaker')); ?></th>
          <th><?php echo e($t('اليوم','Date')); ?></th>
          <th><?php echo e($t('من','From')); ?></th>
          <th><?php echo e($t('إلى','To')); ?></th>
          <th><?php echo e($t('الحالة','Status')); ?></th>
          <th><?php echo e($t('مفعل؟','Active?')); ?></th>
          <th class="text-end"><?php echo e($t('إجراءات','Actions')); ?></th>
        </tr>
      </thead>
      <tbody>
        <?php $__empty_1 = true; $__currentLoopData = $times; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
          <tr>
            <td><?php echo e($row->id); ?></td>
            <td><?php echo e($isAr ? ($row->speaker->name_ar ?? $row->speaker_id) : ($row->speaker->name_en ?? $row->speaker_id)); ?></td>
            <td><?php echo e($row->date->format('Y-m-d') ?? $row->date); ?></td>
            <td><?php echo e($row->time_from); ?></td>
            <td><?php echo e($row->time_to); ?></td>
            <td>
              <?php if($row->status==='available'): ?> <span class="badge bg-success"><?php echo e($t('متاح','Available')); ?></span>
              <?php elseif($row->status==='booked'): ?> <span class="badge bg-primary"><?php echo e($t('محجوز','Booked')); ?></span>
              <?php else: ?> <span class="badge bg-secondary"><?php echo e($t('غير متاح','Unavailable')); ?></span> <?php endif; ?>
            </td>
            <td><?php echo $row->active ? '<span class="badge bg-success">'.$t('نعم','Yes').'</span>' : '<span class="badge bg-danger">'.$t('لا','No').'</span>'; ?></td>
            <td class="text-end">
              <a class="btn btn-sm btn-outline-secondary" href="<?php echo e(route('dashboard.speaker-times.show',$row)); ?>"><?php echo e($t('عرض','Show')); ?></a>
              <a class="btn btn-sm btn-outline-primary" href="<?php echo e(route('dashboard.speaker-times.edit',$row)); ?>"><?php echo e($t('تعديل','Edit')); ?></a>
              <form class="d-inline" method="post" action="<?php echo e(route('dashboard.speaker-times.toggle',$row)); ?>"><?php echo csrf_field(); ?> <?php echo method_field('PATCH'); ?>
                <button class="btn btn-sm btn-outline-warning"><?php echo e($row->active ? $t('تعطيل','Disable') : $t('تفعيل','Enable')); ?></button>
              </form>
              <form class="d-inline" method="post" action="<?php echo e(route('dashboard.speaker-times.destroy',$row)); ?>" onsubmit="return confirm('<?php echo e($t('حذف؟','Delete?')); ?>')"><?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                <button class="btn btn-sm btn-outline-danger"><?php echo e($t('حذف','Delete')); ?></button>
              </form>
            </td>
          </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
          <tr><td colspan="8" class="text-center text-muted py-4"><?php echo e($t('لا توجد بيانات','No data')); ?></td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
  <div class="card-footer"><?php echo e($times->links()); ?></div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.layoutMaster', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/u552947370/domains/affiliatesummitglobal.com/public_html/resources/views/content/speaker_times/index.blade.php ENDPATH**/ ?>