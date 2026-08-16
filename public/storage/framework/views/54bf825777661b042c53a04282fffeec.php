<?php $__env->startSection('title', __('event_schedule.page_title')); ?>

<?php $__env->startSection('content'); ?>
<div class="card">
  <div class="card-header d-flex justify-content-between align-items-center">
    <h5 class="mb-0"><?php echo e(__('event_schedule.page_title')); ?></h5>
    <a href="<?php echo e(route('admin.event_schedules.create')); ?>" class="btn btn-primary">
      <i class="fas fa-plus me-1"></i> <?php echo e(__('event_schedule.add')); ?>

    </a>
  </div>

  <div class="card-body">
    <form method="GET" action="<?php echo e(route('admin.event_schedules.index')); ?>" class="row g-3 mb-4">
      <div class="col-md-3">
        <input type="text" name="name" class="form-control" placeholder="<?php echo e(__('event_schedule.name')); ?>" value="<?php echo e(request('name')); ?>">
      </div>
      <div class="col-md-3">
        <input type="date" name="date" class="form-control" value="<?php echo e(request('date')); ?>">
      </div>
      <div class="col-md-3">
        <input type="time" name="time" class="form-control" value="<?php echo e(request('time')); ?>">
      </div>
      <div class="col-md-3">
        <button class="btn btn-outline-primary w-100" type="submit">
          <i class="fas fa-search"></i> <?php echo e(__('general.search')); ?>

        </button>
      </div>
    </form>

    <div class="table-responsive">
      <table class="table table-striped align-middle">
        <thead>
          <tr>
            <th>#</th>
            <th><?php echo e(__('event_schedule.name')); ?></th>
            <th><?php echo e(__('event_schedule.event')); ?></th>
            <th><?php echo e(__('event_schedule.end_time')); ?></th>
            <th><?php echo e(__('event_schedule.start_time')); ?></th>
            <th><?php echo e(__('event_schedule.active')); ?></th>
            <th><?php echo e(__('event_schedule.actions')); ?></th>
          </tr>
        </thead>
        <tbody>
          <?php $__empty_1 = true; $__currentLoopData = $schedules; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $schedule): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <tr>
              <td><?php echo e($loop->iteration + ($schedules->currentPage() - 1) * $schedules->perPage()); ?></td>
              <td><?php echo e(app()->getLocale() == 'ar' ?$schedule->title_ar ??'-':$schedule->title_em); ?></td>
              <td><?php echo e(app()->getLocale() == 'ar' ? $schedule->event->name_ar ?? '-' : $schedule->event->name_en ?? '-'); ?></td>
              <td><?php echo e($schedule->start_datetime); ?></td>
              <td><?php echo e($schedule->end_datetime); ?></td>
              <td>
                <?php if($schedule->active): ?>
                  <span class="badge bg-success"><?php echo e(__('event_schedule.active')); ?></span>
                <?php else: ?>
                  <span class="badge bg-danger"><?php echo e(__('event_schedule.inactive')); ?></span>
                <?php endif; ?>
              </td>
              <td>
                <a href="<?php echo e(route('admin.event_schedules.show', $schedule->id)); ?>" class="btn btn-sm btn-outline-info">
                  <i class="fas fa-eye"></i>
                </a>
                <a href="<?php echo e(route('admin.event_schedules.edit', $schedule->id)); ?>" class="btn btn-sm btn-outline-primary">
                  <i class="fas fa-edit"></i>
                </a>
                <form action="<?php echo e(route('admin.event_schedules.destroy', $schedule->id)); ?>" method="POST" class="d-inline" onsubmit="return confirm('<?php echo e(__('general.confirm_delete')); ?>')">
                  <?php echo csrf_field(); ?>
                  <?php echo method_field('DELETE'); ?>
                  <button class="btn btn-sm btn-outline-danger">
                    <i class="fas fa-trash"></i>
                  </button>
                </form>
              </td>
            </tr>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <tr>
              <td colspan="8" class="text-center"><?php echo e(__('general.no_data')); ?></td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
<?php if($schedules->hasPages()): ?>
  <div class="mt-4 flex justify-center">
    <nav class="inline-flex rounded-md shadow-sm" aria-label="Pagination">
      
      <?php if($schedules->onFirstPage()): ?>
        <span class="px-3 py-2 text-sm bg-gray-200 text-gray-500 border border-gray-300 rounded-l-md">‹</span>
      <?php else: ?>
        <a href="<?php echo e($schedules->previousPageUrl()); ?>" class="px-3 py-2 text-sm bg-white text-gray-700 border border-gray-300 hover:bg-gray-100 rounded-l-md">‹</a>
      <?php endif; ?>

      
      <?php $__currentLoopData = $schedules->getUrlRange(1, $schedules->lastPage()); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $page => $url): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <a href="<?php echo e($url); ?>"
           class="px-3 py-2 text-sm border border-gray-300 <?php echo e($schedules->currentPage() == $page ? 'bg-primary text-white' : 'bg-white text-gray-700 hover:bg-gray-100'); ?>">
          <?php echo e($page); ?>

        </a>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

      
      <?php if($schedules->hasMorePages()): ?>
        <a href="<?php echo e($schedules->nextPageUrl()); ?>" class="px-3 py-2 text-sm bg-white text-gray-700 border border-gray-300 hover:bg-gray-100 rounded-r-md">›</a>
      <?php else: ?>
        <span class="px-3 py-2 text-sm bg-gray-200 text-gray-500 border border-gray-300 rounded-r-md">›</span>
      <?php endif; ?>
    </nav>
  </div>
<?php endif; ?>

  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.layoutMaster', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/u552947370/domains/affiliatesummitglobal.com/public_html/resources/views/content/event_schedules/index.blade.php ENDPATH**/ ?>