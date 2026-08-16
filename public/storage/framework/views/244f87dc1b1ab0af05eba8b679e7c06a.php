

<?php $__env->startSection('title', __('qr_code_list')); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid" style="direction:rtl">
  <div class="row align-items-center mb-3">
    <div class="col-sm">
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb bg-white px-3 py-2 rounded shadow-sm">

          <li class="breadcrumb-item active text-primary" aria-current="page">سجل الحضور بالـ QR</li>
        </ol>
      </nav>
    </div>
  </div>

  <!-- فلاتر -->
  <div class="card mb-3">
    <div class="card-body">
      <form method="get" class="row g-3">
        <div class="col-12 col-md-3">
          <label class="form-label">Register ID</label>
          <input type="text" name="register_id" value="<?php echo e(request('register_id')); ?>" class="form-control" placeholder="مثال: 123">
        </div>
        <div class="col-12 col-md-3">
          <label class="form-label">Short Code</label>
          <input type="text" name="short_code" value="<?php echo e(request('short_code')); ?>" class="form-control" placeholder="الكود المختصر">
        </div>
        <div class="col-12 col-md-3">
          <label class="form-label">من تاريخ</label>
          <input type="date" name="from_date" value="<?php echo e(request('from_date')); ?>" class="form-control">
        </div>
        <div class="col-12 col-md-3">
          <label class="form-label">إلى تاريخ</label>
          <input type="date" name="to_date" value="<?php echo e(request('to_date')); ?>" class="form-control">
        </div>

        <div class="col-12 d-flex gap-2 justify-content-end">
          <button class="btn btn-primary">بحث</button>
          <a href="<?php echo e(route('qrcodes.attendees')); ?>" class="btn btn-outline-secondary">مسح الفلاتر</a>
        </div>
      </form>
    </div>
  </div>

  <!-- الجدول -->
  <div class="card">
    <div class="card-body table-responsive">
      <table class="table table-striped align-middle">
        <thead class="table-light">
          <tr>
            <th>#</th>
            <th>الاسم</th>
            <th>البريد</th>
            <th>الهاتف</th>
            <th>Register ID</th>
            <th>Short Code</th>
            <th>تاريخ/وقت الحضور</th>
          </tr>
        </thead>
        <tbody>
          <?php $__empty_1 = true; $__currentLoopData = $attendees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <tr>
              <td><?php echo e($attendees->firstItem() + $loop->index); ?></td>
              <td><?php echo e($row->client_name ?? '—'); ?></td>
              <td><?php echo e($row->client_email ?? '—'); ?></td>
              <td><?php echo e($row->client_phone ?? '—'); ?></td>
              <td><?php echo e($row->register_id); ?></td>
              <td><?php echo e($row->short_code ?? '—'); ?></td>
              <td>
                <?php
                  $dt = $row->attendance_at ? \Carbon\Carbon::parse($row->attendance_at) : null;
                ?>
                <?php echo e($dt ? $dt->format('Y-m-d H:i') : '—'); ?>

              </td>
            </tr>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <tr>
              <td colspan="7" class="text-center text-muted">لا توجد نتائج</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>

      <div class="mt-3">
        <?php echo e($attendees->links()); ?>

      </div>
    </div>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.layoutMaster', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/u552947370/domains/affiliatesummitglobal.com/public_html/resources/views/content/qrcodes/attendees.blade.php ENDPATH**/ ?>