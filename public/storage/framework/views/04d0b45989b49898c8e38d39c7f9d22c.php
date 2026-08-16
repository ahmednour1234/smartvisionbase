<?php $__env->startSection('title', __('multi_media.title')); ?>

<?php $__env->startSection('content'); ?>
<div class="card">
  <div class="card-header d-flex justify-content-between align-items-center">
    <h5 class="mb-0"><?php echo e(__('multi_media.title')); ?></h5>
    <a href="<?php echo e(route('dashboard.multimedia-categories.create')); ?>" class="btn btn-primary">
      <i class="bi bi-plus-lg"></i> <?php echo e(__('multi_media.create')); ?>

    </a>
  </div>

  <div class="card-body">
    <div class="table-responsive">
      <table class="table  align-middle">
        <thead>
          <tr>
            <th>#</th>
            <th><?php echo e(__('multi_media.name_ar')); ?></th>
            <th><?php echo e(__('multi_media.name_en')); ?></th>
            <th><?php echo e(__('multi_media.logo')); ?></th>
            <th><?php echo e(__('multi_media.active')); ?></th>
            <th><?php echo e(__('multi_media.actions')); ?></th>
          </tr>
        </thead>
        <tbody>
          <?php $__empty_1 = true; $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <tr>
              <td><?php echo e($loop->iteration); ?></td>
              <td><?php echo e($category->name_ar); ?></td>
              <td><?php echo e($category->name_en); ?></td>
              <td>
                <?php if($category->logo): ?>
                  <img src="<?php echo e(asset($category->logo)); ?>" alt="logo" width="50" height="50">
                <?php else: ?>
                  ---
                <?php endif; ?>
              </td>
              <td>
                <form method="POST" action="<?php echo e(route('dashboard.multimedia-categories.activate', $category->id)); ?>">
                  <?php echo csrf_field(); ?>
                  <button type="submit" class="btn btn-sm btn-<?php echo e($category->active ? 'success' : 'secondary'); ?>">
                    <?php echo e($category->active ? __('multi_media.status_active') : __('multi_media.status_inactive')); ?>

                  </button>
                </form>
              </td>
              <td>
                <a href="<?php echo e(route('dashboard.multimedia-categories.show', $category->id)); ?>" class="btn btn-info btn-sm">
                  <?php echo e(__('multi_media.show')); ?>

                </a>
                <a href="<?php echo e(route('dashboard.multimedia-categories.edit', $category->id)); ?>" class="btn btn-warning btn-sm">
                  <?php echo e(__('multi_media.edit')); ?>

                </a>
              </td>
            </tr>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <tr>
              <td colspan="6" class="text-center">لا توجد بيانات</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>

    
 <?php if($categories->hasPages()): ?>
  <div class="mt-4 flex justify-center">
    <nav class="inline-flex rounded-md shadow-sm" aria-label="Pagination">
      
      <?php if($categories->onFirstPage()): ?>
        <span class="px-3 py-2 text-sm bg-gray-200 text-gray-500 border border-gray-300 rounded-l-md">‹</span>
      <?php else: ?>
        <a href="<?php echo e($categories->previousPageUrl()); ?>" class="px-3 py-2 text-sm bg-white text-gray-700 border border-gray-300 hover:bg-gray-100 rounded-l-md">‹</a>
      <?php endif; ?>

      
      <?php $__currentLoopData = $categories->getUrlRange(1, $categories->lastPage()); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $page => $url): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <a href="<?php echo e($url); ?>"
           class="px-3 py-2 text-sm border border-gray-300 <?php echo e($categories->currentPage() == $page ? 'bg-primary text-white' : 'bg-white text-gray-700 hover:bg-gray-100'); ?>">
          <?php echo e($page); ?>

        </a>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

      
      <?php if($categories->hasMorePages()): ?>
        <a href="<?php echo e($categories->nextPageUrl()); ?>" class="px-3 py-2 text-sm bg-white text-gray-700 border border-gray-300 hover:bg-gray-100 rounded-r-md">›</a>
      <?php else: ?>
        <span class="px-3 py-2 text-sm bg-gray-200 text-gray-500 border border-gray-300 rounded-r-md">›</span>
      <?php endif; ?>
    </nav>
  </div>
<?php endif; ?>

  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.layoutMaster', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/u552947370/domains/affiliatesummitglobal.com/public_html/resources/views/content/multi_media/index.blade.php ENDPATH**/ ?>