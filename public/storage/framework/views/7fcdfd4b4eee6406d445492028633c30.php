<?php $__env->startSection('title', __('sponsor_category.page_title')); ?>

<?php $__env->startSection('content'); ?>
<div class="card">
  <div class="card-header d-flex justify-content-between align-items-center">
    <h5 class="mb-0"><?php echo e(__('sponsor_category.page_title')); ?></h5>
    <a href="<?php echo e(route('admin.sponsor_categories.create')); ?>" class="btn btn-primary">
      <i class="fas fa-plus me-1"></i> <?php echo e(__('sponsor_category.add')); ?>

    </a>
  </div>

  <div class="card-body">
    <form method="GET" action="<?php echo e(route('admin.sponsor_categories.index')); ?>" class="row g-3 mb-4">
      <div class="col-md-4">
        <input type="text" name="search" class="form-control" placeholder="<?php echo e(__('sponsor_category.search_name')); ?>" value="<?php echo e(request('search')); ?>">
      </div>
      <div class="col-md-2">
        <button class="btn btn-outline-primary w-100" type="submit">
          <i class="fas fa-search"></i> <?php echo e(__('general.search')); ?>

        </button>
      </div>
    </form>

    <div class="table-responsive">
      <table class="table  align-middle">
        <thead >
          <tr>
            <th>#</th>
            <th><?php echo e(__('sponsor_category.name')); ?></th>
            <th><?php echo e(__('sponsor_category.name_en')); ?></th>
            <th><?php echo e(__('sponsor_category.status')); ?></th>
            <th><?php echo e(__('sponsor_category.logo')); ?></th>
            <th><?php echo e(__('sponsor_category.actions')); ?></th>
          </tr>
        </thead>
        <tbody>
          <?php $__empty_1 = true; $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <tr>
              <td><?php echo e($loop->iteration + ($categories->currentPage() - 1) * $categories->perPage()); ?></td>
              <td><?php echo e($category->name); ?></td>
              <td><?php echo e($category->name_en); ?></td>
              <td>
                <?php if($category->active): ?>
                  <span class="badge bg-success"><?php echo e(__('general.active')); ?></span>
                <?php else: ?>
                  <span class="badge bg-danger"><?php echo e(__('general.inactive')); ?></span>
                <?php endif; ?>
              </td>
              <td>
                <?php if($category->logo): ?>
                  <img src="<?php echo e(asset($category->logo)); ?>" width="60" class="rounded">
                <?php else: ?>
                  <span class="text-muted">-</span>
                <?php endif; ?>
              </td>
              <td>
                <a href="<?php echo e(route('admin.sponsor_categories.edit', $category->id)); ?>" class="btn btn-sm btn-outline-primary">
                  <i class="fas fa-edit"></i>
                </a>
                <a href="<?php echo e(route('admin.sponsor_categories.show', $category->id)); ?>" class="btn btn-sm btn-outline-info">
                  <i class="fas fa-eye"></i>
                </a>
                <form action="<?php echo e(route('admin.sponsor_categories.destroy', $category->id)); ?>" method="POST" class="d-inline" onsubmit="return confirm('<?php echo e(__('sponsor_category.confirm_delete')); ?>')">
                  <?php echo csrf_field(); ?>
                  <?php echo method_field('DELETE'); ?>
                  <button class="btn btn-sm btn-outline-danger">
                    <i class="fas fa-trash"></i>
                  </button>
                </form>
                <a href="<?php echo e(route('admin.sponsor_categories.deactivate', $category->id)); ?>" class="btn btn-sm btn-outline-warning">
                  <i class="fas fa-ban"></i>
                </a>
              </td>
            </tr>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <tr>
              <td colspan="6" class="text-center"><?php echo e(__('general.no_data')); ?></td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>

    <?php if($categories->hasPages()): ?>
  <div class="mt-4 flex justify-center">
    <nav class="inline-flex shadow-sm rounded-md" aria-label="Pagination">
      
      <?php if($categories->onFirstPage()): ?>
        <span class="px-3 py-2 bg-gray-200 text-gray-500 border border-gray-300 rounded-l-md">‹</span>
      <?php else: ?>
        <a href="<?php echo e($categories->previousPageUrl()); ?>" class="px-3 py-2 bg-white text-gray-700 border border-gray-300 hover:bg-gray-100 rounded-l-md">‹</a>
      <?php endif; ?>

      
      <?php $__currentLoopData = $categories->getUrlRange(1, $categories->lastPage()); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $page => $url): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <a href="<?php echo e($url); ?>"
           class="px-3 py-2 border border-gray-300 <?php echo e($categories->currentPage() == $page ? 'bg-primary text-white' : 'bg-white text-gray-700 hover:bg-gray-100'); ?>">
          <?php echo e($page); ?>

        </a>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

      
      <?php if($categories->hasMorePages()): ?>
        <a href="<?php echo e($categories->nextPageUrl()); ?>" class="px-3 py-2 bg-white text-gray-700 border border-gray-300 hover:bg-gray-100 rounded-r-md">›</a>
      <?php else: ?>
        <span class="px-3 py-2 bg-gray-200 text-gray-500 border border-gray-300 rounded-r-md">›</span>
      <?php endif; ?>
    </nav>
  </div>
<?php endif; ?>

  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.layoutMaster', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/u552947370/domains/affiliatesummitglobal.com/public_html/resources/views/content/sponsor_categories/index.blade.php ENDPATH**/ ?>