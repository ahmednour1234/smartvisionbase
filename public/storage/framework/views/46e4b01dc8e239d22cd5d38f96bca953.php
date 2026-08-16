

<?php $__env->startSection('title','إضافة شركة'); ?>
<?php $__env->startSection('content'); ?>
<form class="card" method="post" action="<?php echo e(route('dashboard.attendance_company.store')); ?>">
  <?php echo csrf_field(); ?>
  <div class="card-body row g-3">
    <div class="col-md-6">
      <label class="form-label">الاسم</label>
      <input name="name" class="form-control" required>
    </div>
    <div class="col-md-3">
      <label class="form-label">مفعّل</label>
      <select name="active" class="form-select">
        <option value="1" selected>نعم</option>
        <option value="0">لا</option>
      </select>
    </div>
    <div class="col-md-3">
      <label class="form-label">حضور</label>
      <select name="attendance" class="form-select">
        <option value="0" selected>غير حاضر</option>
        <option value="1">حاضر</option>
      </select>
    </div>
    <div class="col-md-4">
      <label class="form-label">وقت الحضور (اختياري)</label>
      <input type="datetime-local" name="attendance_at" class="form-control">
    </div>
  </div>
  <div class="card-footer d-flex justify-content-end gap-2">
    <a href="<?php echo e(route('dashboard.attendance_company.index')); ?>" class="btn btn-secondary">إلغاء</a>
    <button class="btn btn-primary">حفظ</button>
  </div>
</form>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.layoutMaster', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/u552947370/domains/affiliatesummitglobal.com/public_html/resources/views/content/attendance_company/create.blade.php ENDPATH**/ ?>