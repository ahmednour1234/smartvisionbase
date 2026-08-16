
<?php $__env->startSection('title','Bookings'); ?>
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
        <label class="form-label"><?php echo e($t('الحالة','Status')); ?></label>
        <select name="status" class="form-select">
          <option value=""><?php echo e($t('الكل','All')); ?></option>
          <option value="pending"   <?php if(($filters['status']??'')==='pending'): echo 'selected'; endif; ?>><?php echo e($t('معلق','Pending')); ?></option>
          <option value="confirmed" <?php if(($filters['status']??'')==='confirmed'): echo 'selected'; endif; ?>><?php echo e($t('مؤكد','Confirmed')); ?></option>
          <option value="cancelled" <?php if(($filters['status']??'')==='cancelled'): echo 'selected'; endif; ?>><?php echo e($t('ملغي','Cancelled')); ?></option>
        </select>
      </div>
      <div class="col-md-1">
        <label class="form-label"><?php echo e($t('مفعل','Active')); ?></label>
        <select name="active" class="form-select">
          <option value=""><?php echo e($t('الكل','All')); ?></option>
          <option value="1" <?php if(($filters['active']??null)===true): echo 'selected'; endif; ?>> <?php echo e($t('نعم','Yes')); ?> </option>
          <option value="0" <?php if(($filters['active']??null)===false): echo 'selected'; endif; ?>> <?php echo e($t('لا','No')); ?> </option>
        </select>
      </div>
      <div class="col-md-12 d-flex gap-2">
        <button class="btn btn-primary"><i class="fas fa-filter me-1"></i> <?php echo e($t('تصفية','Filter')); ?></button>
        <a href="<?php echo e(route('dashboard.bookings.index')); ?>" class="btn btn-light"><?php echo e($t('إعادة ضبط','Reset')); ?></a>
        <a href="<?php echo e(route('dashboard.bookings.create')); ?>" class="btn btn-success ms-auto"><i class="fas fa-plus me-1"></i> <?php echo e($t('إنشاء','Create')); ?></a>
      </div>
    </form>
  </div>
</div>

<div class="card shadow-sm">
  <div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
      <thead>
        <tr>
          <th>#</th>
          <th><?php echo e($t('المتحدث','Speaker')); ?></th>
          <th><?php echo e($t('العميل','Client')); ?></th>
          <th><?php echo e($t('من','From')); ?></th>
          <th><?php echo e($t('إلى','To')); ?></th>
          <th><?php echo e($t('الحالة','Status')); ?></th>
          <th><?php echo e($t('مفعل؟','Active?')); ?></th>
          <th class="text-end"><?php echo e($t('إجراءات','Actions')); ?></th>
        </tr>
      </thead>
      <tbody>
        <?php $__empty_1 = true; $__currentLoopData = $bookings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $b): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
          <tr>
            <td><?php echo e($b->id); ?></td>
            <td><?php echo e($isAr ? ($b->speaker->name_ar ?? $b->speaker_id) : ($b->speaker->name_en ?? $b->speaker_id)); ?></td>
            <td><?php echo e(optional($b->client)->name ?? $b->client_id); ?></td>
            <td><?php echo e(optional($b->time_from)->format('Y-m-d H:i') ?? $b->time_from); ?></td>
            <td><?php echo e(optional($b->time_to)->format('Y-m-d H:i')   ?? $b->time_to); ?></td>
            <td>
              <?php if($b->status==='confirmed'): ?> <span class="badge bg-success"><?php echo e($t('مؤكد','Confirmed')); ?></span>
              <?php elseif($b->status==='pending'): ?> <span class="badge bg-warning text-dark"><?php echo e($t('معلق','Pending')); ?></span>
              <?php else: ?> <span class="badge bg-secondary"><?php echo e($t('ملغي','Cancelled')); ?></span> <?php endif; ?>
            </td>
            <td><?php echo $b->active ? '<span class="badge bg-success">'.$t('نعم','Yes').'</span>' : '<span class="badge bg-danger">'.$t('لا','No').'</span>'; ?></td>
            <td class="text-end">
              <a class="btn btn-sm btn-outline-secondary" href="<?php echo e(route('dashboard.bookings.show',$b)); ?>"><?php echo e($t('عرض','Show')); ?></a>
              <a class="btn btn-sm btn-outline-primary" href="<?php echo e(route('dashboard.bookings.edit',$b)); ?>"><?php echo e($t('تعديل','Edit')); ?></a>
              <form class="d-inline" method="post" action="<?php echo e(route('dashboard.bookings.toggle',$b)); ?>"><?php echo csrf_field(); ?> <?php echo method_field('PATCH'); ?>
                <button class="btn btn-sm btn-outline-warning"><?php echo e($b->active ? $t('تعطيل','Disable') : $t('تفعيل','Enable')); ?></button>
              </form>
              <form class="d-inline" method="post" action="<?php echo e(route('dashboard.bookings.destroy',$b)); ?>" onsubmit="return confirm('<?php echo e($t('حذف؟','Delete?')); ?>')"><?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
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
  <div class="card-footer"><?php echo e($bookings->links()); ?></div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.layoutMaster', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/u552947370/domains/affiliatesummitglobal.com/public_html/resources/views/content/bookings/index.blade.php ENDPATH**/ ?>