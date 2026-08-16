


<?php $__env->startSection('title', __('ads_title')); ?>

<?php $__env->startSection('content'); ?>
<?php
  // حضّر أدوات الترقيم اليدوي (نفس الصفحة + نفس الـ query string بدون page)
  $baseUrl = url()->current();
  $qs = request()->query();
  unset($qs['page']);
  $qsStr = http_build_query($qs);

  $pageUrl = function($p) use ($baseUrl, $qsStr) {
    return $baseUrl . '?' . ($qsStr ? ($qsStr . '&') : '') . 'page=' . max(1, (int)$p);
  };

  // نافذة أرقام الصفحات
  $current = $invitations->currentPage();
  $last    = $invitations->lastPage();
  $window  = 2; // عدد الأرقام يمين ويسار الصفحة الحالية
  $start   = max(1, $current - $window);
  $end     = min($last, $current + $window);
  if ($end - $start < $window * 2) {
    // وسّع البداية/النهاية لو القائمة قصيرة
    $start = max(1, min($start, $last - $window * 2));
    $end   = min($last, max($end, 1 + $window * 2));
  }
?>

<div class="card">
  <div class="card-header d-flex justify-content-between align-items-center">
    <h5 class="mb-0"><?php echo e(__('Invitations')); ?></h5>
    <div class="d-flex gap-2">
      <a class="btn btn-outline-secondary btn-sm" href="<?php echo e(route('dashboard.invitations.template')); ?>"><?php echo e(__('Template')); ?></a>
      <a class="btn btn-primary btn-sm" href="<?php echo e(route('dashboard.invitations.export', request()->query())); ?>"><?php echo e(__('Export')); ?></a>
    </div>
  </div>

  <div class="card-body">
    <?php if(session('success')): ?>
      <div class="alert alert-success"><?php echo e(session('success')); ?></div>
    <?php endif; ?>

    
    <div class="mb-3">
      <span class="badge bg-secondary"><?php echo e(__('Total')); ?>: <?php echo e($stats['total']); ?></span>
      <span class="badge bg-success"><?php echo e(__('Present')); ?>: <?php echo e($stats['present']); ?></span>
      <span class="badge bg-danger"><?php echo e(__('Absent')); ?>: <?php echo e($stats['absent']); ?></span>
    </div>

    
    <form class="row g-2 mb-3" method="get">
      <div class="col-md-4">
        <input type="text" name="q" value="<?php echo e($search); ?>" class="form-control" placeholder="<?php echo e(__('Search name or number')); ?>">
      </div>
      <div class="col-md-3">
        <input type="text" name="type" value="<?php echo e($type); ?>" class="form-control" placeholder="<?php echo e(__('Type (e.g. VIP)')); ?>">
      </div>
      <div class="col-md-3">
        <select name="attendance" class="form-select">
          <option value="all" <?php echo e($attendance==='all' ? 'selected' : ''); ?>><?php echo e(__('All')); ?></option>
          <option value="present" <?php echo e($attendance==='present' ? 'selected' : ''); ?>><?php echo e(__('Present')); ?></option>
          <option value="absent" <?php echo e($attendance==='absent' ? 'selected' : ''); ?>><?php echo e(__('Absent')); ?></option>
        </select>
      </div>
      <div class="col-md-2">
        <button class="btn btn-outline-primary w-100"><?php echo e(__('Filter')); ?></button>
      </div>
    </form>

    
    <form action="<?php echo e(route('dashboard.invitations.import')); ?>" method="post" enctype="multipart/form-data" class="mb-3">
      <?php echo csrf_field(); ?>
      <div class="row g-2 align-items-center">
        <div class="col-md-6">
          <input type="file" name="file" class="form-control" accept=".xlsx,.xls,.csv" required>
        </div>
        <div class="col-md-3">
          <button class="btn btn-success w-100"><?php echo e(__('Import Excel')); ?></button>
        </div>
        
      </div>
    </form>

    
    <div class="table-responsive">
      <table class="table table-striped align-middle">
        <thead>
          <tr>
            <th>#</th>
            <th><?php echo e(__('Name')); ?></th>
            <th><?php echo e(__('Invitation Number')); ?></th>
            <th><?php echo e(__('Type')); ?></th>
            <th><?php echo e(__('Attendance')); ?></th>
            <th><?php echo e(__('Actions')); ?></th>
          </tr>
        </thead>
        <tbody>
          <?php $__empty_1 = true; $__currentLoopData = $invitations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $inv): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <tr>
              <td><?php echo e($invitations->firstItem() + $loop->index); ?></td>
              <td><?php echo e($inv->name); ?></td>
              <td><?php echo e($inv->invitation_number); ?></td>
              <td><?php echo e($inv->type); ?></td>
              <td>
                <button
                  class="btn btn-sm <?php echo e($inv->attendance ? 'btn-success' : 'btn-outline-secondary'); ?>"
                  onclick="toggleAttendance(<?php echo e($inv->id); ?>)">
                  <?php echo e($inv->attendance ? __('Present') : __('Absent')); ?>

                </button>
              </td>
              <td class="d-flex gap-2">
                
                <form action="<?php echo e(route('dashboard.invitations.destroy', $inv)); ?>" method="post" onsubmit="return confirm('<?php echo e(__('Delete?')); ?>')">
                  <?php echo csrf_field(); ?>
                  <?php echo method_field('DELETE'); ?>
                  <button class="btn btn-sm btn-danger"><?php echo e(__('Delete')); ?></button>
                </form>
              </td>
            </tr>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <tr>
              <td colspan="6" class="text-center text-muted"><?php echo e(__('No data')); ?></td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>

    
    <?php if($last > 1): ?>
      <nav aria-label="Pagination" class="mt-3">
        <ul class="pagination mb-0">
          
          <li class="page-item <?php echo e($current === 1 ? 'disabled' : ''); ?>">
            <a class="page-link" href="<?php echo e($current === 1 ? '#' : $pageUrl(1)); ?>" tabindex="-1">« <?php echo e(__('First')); ?></a>
          </li>

          
          <li class="page-item <?php echo e($current === 1 ? 'disabled' : ''); ?>">
            <a class="page-link" href="<?php echo e($current === 1 ? '#' : $pageUrl($current - 1)); ?>" tabindex="-1">‹ <?php echo e(__('Prev')); ?></a>
          </li>

          
          <?php if($start > 1): ?>
            <li class="page-item">
              <a class="page-link" href="<?php echo e($pageUrl(1)); ?>">1</a>
            </li>
            <?php if($start > 2): ?>
              <li class="page-item disabled"><span class="page-link">…</span></li>
            <?php endif; ?>
          <?php endif; ?>

          
          <?php for($i = $start; $i <= $end; $i++): ?>
            <li class="page-item <?php echo e($i === $current ? 'active' : ''); ?>">
              <a class="page-link" href="<?php echo e($i === $current ? '#' : $pageUrl($i)); ?>"><?php echo e($i); ?></a>
            </li>
          <?php endfor; ?>

          
          <?php if($end < $last): ?>
            <?php if($end < $last - 1): ?>
              <li class="page-item disabled"><span class="page-link">…</span></li>
            <?php endif; ?>
            <li class="page-item">
              <a class="page-link" href="<?php echo e($pageUrl($last)); ?>"><?php echo e($last); ?></a>
            </li>
          <?php endif; ?>

          
          <li class="page-item <?php echo e($current === $last ? 'disabled' : ''); ?>">
            <a class="page-link" href="<?php echo e($current === $last ? '#' : $pageUrl($current + 1)); ?>"><?php echo e(__('Next')); ?> ›</a>
          </li>

          
          <li class="page-item <?php echo e($current === $last ? 'disabled' : ''); ?>">
            <a class="page-link" href="<?php echo e($current === $last ? '#' : $pageUrl($last)); ?>"><?php echo e(__('Last')); ?> »</a>
          </li>
        </ul>
      </nav>

      
      <div class="text-muted small mt-2">
        <?php echo e(__('Showing')); ?> <?php echo e($invitations->firstItem()); ?>–<?php echo e($invitations->lastItem()); ?>

        <?php echo e(__('of')); ?> <?php echo e($invitations->total()); ?>

      </div>
    <?php endif; ?>
  </div>
</div>


<meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">

<script>
function toggleAttendance(id) {
  fetch(`<?php echo e(url('dashboard/invitations')); ?>/${id}/attendance`, {
    method: 'PATCH',
    headers: {
      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
      'Accept': 'application/json'
    }
  })
  .then(r => r.json())
  .then(data => {
    if (data && data.success) {
      // إعادة تحميل للحفاظ على الفلاتر والترقيم
      window.location.reload();
    }
  })
  .catch(() => {});
}
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.layoutMaster', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/u552947370/domains/affiliatesummitglobal.com/public_html/resources/views/content/invitations/index.blade.php ENDPATH**/ ?>