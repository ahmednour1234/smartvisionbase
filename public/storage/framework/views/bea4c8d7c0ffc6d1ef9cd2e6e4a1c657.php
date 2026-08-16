<?php $__env->startSection('title', __('event.page_title')); ?>

<?php $__env->startSection('vendor-style'); ?>
  <link rel="stylesheet" href="<?php echo e(asset('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css')); ?>">
  <link rel="stylesheet" href="<?php echo e(asset('assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css')); ?>">
  <link rel="stylesheet" href="<?php echo e(asset('assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css')); ?>">
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="card">
  <div class="card-header d-flex justify-content-between align-items-center">
    <h5 class="mb-0"><?php echo e(__('event.page_title')); ?></h5>
    
  </div>

  <div class="card-body">
    <div class="table-responsive">
      <table class="table table-bordered table-striped datatable">
        <thead class="table-light">
          <tr>
            <th>#</th>
            <th><?php echo e(__('event.name')); ?></th>
            <th><?php echo e(__('event.date')); ?></th>
            <th><?php echo e(__('event.address')); ?></th>
            <th><?php echo e(__('event.status')); ?></th>
            <th><?php echo e(__('event.actions')); ?></th>
          </tr>
        </thead>
        <tbody>
          <?php $__currentLoopData = $events; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $event): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr>
              <td><?php echo e($key + 1); ?></td>
              <td><?php echo e(app()->getLocale() == 'ar' ? $event->name_ar : $event->name_en); ?></td>
              <td><?php echo e(\Carbon\Carbon::parse($event->event_date)->format('Y-m-d H:i')); ?></td>
              <td><?php echo e(app()->getLocale() == 'ar' ? $event->address_ar : $event->address_en); ?></td>
              <td>
                <?php if($event->active): ?>
                  <span class="badge bg-success"><?php echo e(__('event.active')); ?></span>
                <?php else: ?>
                  <span class="badge bg-danger"><?php echo e(__('event.inactive')); ?></span>
                <?php endif; ?>
              </td>
              <td>
                <a href="<?php echo e(route('admin.events.edit', $event->id)); ?>" class="btn btn-sm btn-outline-primary">
                  <i class="fas fa-edit"></i>
                </a>
                
              </td>
            </tr>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
      </table>
    </div>

    <div class="mt-3">
      <?php echo e($events->links()); ?>

    </div>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('vendor-script'); ?>
  <script src="<?php echo e(asset('assets/vendor/libs/datatables/jquery.dataTables.js')); ?>"></script>
  <script src="<?php echo e(asset('assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js')); ?>"></script>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('page-script'); ?>
  <script>
    $(function () {
      $('.datatable').DataTable({
        paging: false,
        searching: false,
        info: false
      });
    });
  </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.layoutMaster', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/u552947370/domains/affiliatesummitglobal.com/public_html/resources/views/content/events/index.blade.php ENDPATH**/ ?>