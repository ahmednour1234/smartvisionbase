<?php $__env->startSection('title', __('package.title')); ?>

<?php $__env->startSection('content'); ?>
<div class="card">
  <div class="card-header d-flex justify-content-between align-items-center">
    <h5><?php echo e(__('package.title')); ?></h5>
    <a href="<?php echo e(route('dashboard.packages.create')); ?>" class="btn btn-primary">
      <i class="bi bi-plus"></i> <?php echo e(__('package.create')); ?>

    </a>
  </div>

  <div class="card-body">
    
    <form method="GET" class="row mb-4 g-3">
      <div class="col-md-4">
        <input type="text" name="name" class="form-control" placeholder="<?php echo e(__('package.name_ar')); ?>"
               value="<?php echo e(request('name')); ?>">
      </div>

      <div class="col-md-4">
        <input type="number" name="price" class="form-control" placeholder="<?php echo e(__('package.price')); ?>"
               value="<?php echo e(request('price')); ?>">
      </div>

      <div class="col-md-4 text-end">
        <button type="submit" class="btn btn-outline-primary">
          <i class="bi bi-search"></i> <?php echo e(__('multi_media.search')); ?>

        </button>
        <a href="<?php echo e(route('dashboard.packages.index')); ?>" class="btn btn-outline-secondary">
          <i class="bi bi-x-circle"></i>  <?php echo e(__('multi_media.reset')); ?>

        </a>
      </div>
    </form>

    
    <div class="table-responsive">
      <table class="table text-center align-middle">
        <thead>
          <tr>
            <th>#</th>
            <th><?php echo e(__('package.name_ar')); ?></th>
            <th><?php echo e(__('package.price')); ?></th>
            <th><?php echo e(__('package.price_discount')); ?></th>
            <th><?php echo e(__('package.sort')); ?></th>
            <th><?php echo e(__('package.active')); ?></th>
            <th><?php echo e(__('package.actions')); ?></th>
          </tr>
        </thead>
        <tbody>
          <?php $__empty_1 = true; $__currentLoopData = $packages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $package): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <tr>
              <td><?php echo e($loop->iteration); ?></td>
              <td><?php echo e($package->name_ar); ?></td>
              <td><?php echo e(number_format($package->price, 2)); ?></td>
              <td><?php echo e(number_format($package->price_discount, 2)); ?></td>
              <td><?php echo e($package->sort); ?></td>
              <td>
                <form action="<?php echo e(route('dashboard.packages.activate', $package->id)); ?>" method="POST">
                  <?php echo csrf_field(); ?>
                  <button type="submit" class="btn btn-sm btn-<?php echo e($package->active ? 'success' : 'secondary'); ?>">
                    <?php echo e($package->active ? __('package.status_active') : __('package.status_inactive')); ?>

                  </button>
                </form>
              </td>
              <td>
                <a href="<?php echo e(route('dashboard.packages.show', $package->id)); ?>" class="btn btn-sm btn-info">👁️</a>
                <a href="<?php echo e(route('dashboard.packages.edit', $package->id)); ?>" class="btn btn-sm btn-warning">✏️</a>
              </td>
            </tr>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <tr>
              <td colspan="7">لا توجد بيانات حالياً</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>

    
  <?php if($packages->hasPages()): ?>
  <div class="mt-4 flex justify-center">
    <nav class="inline-flex rounded-md shadow-sm" aria-label="Pagination">
      
      <?php if($packages->onFirstPage()): ?>
        <span class="px-3 py-2 text-sm bg-gray-200 text-gray-500 border border-gray-300 rounded-l-md">‹</span>
      <?php else: ?>
        <a href="<?php echo e($packages->previousPageUrl()); ?>" class="px-3 py-2 text-sm bg-white text-gray-700 border border-gray-300 hover:bg-gray-100 rounded-l-md">‹</a>
      <?php endif; ?>

      
      <?php $__currentLoopData = $packages->getUrlRange(1, $packages->lastPage()); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $page => $url): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <a href="<?php echo e($url); ?>"
           class="px-3 py-2 text-sm border border-gray-300 <?php echo e($packages->currentPage() == $page ? 'bg-primary text-white' : 'bg-white text-gray-700 hover:bg-gray-100'); ?>">
          <?php echo e($page); ?>

        </a>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

      
      <?php if($packages->hasMorePages()): ?>
        <a href="<?php echo e($packages->nextPageUrl()); ?>" class="px-3 py-2 text-sm bg-white text-gray-700 border border-gray-300 hover:bg-gray-100 rounded-r-md">›</a>
      <?php else: ?>
        <span class="px-3 py-2 text-sm bg-gray-200 text-gray-500 border border-gray-300 rounded-r-md">›</span>
      <?php endif; ?>
    </nav>
  </div>
<?php endif; ?>

  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.layoutMaster', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/u552947370/domains/affiliatesummitglobal.com/public_html/resources/views/content/packages/index.blade.php ENDPATH**/ ?>