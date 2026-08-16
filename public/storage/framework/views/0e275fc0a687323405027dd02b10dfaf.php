<?php $__env->startSection('title', __('blog.title')); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-7xl mx-auto p-6 space-y-6">
  <div class="card-header d-flex justify-content-between align-items-center">
    <h5><?php echo e(__('blog.title')); ?></h5>
    <a href="<?php echo e(route('dashboard.blogs.create')); ?>" class="btn btn-primary">
      <i class="bi bi-plus"></i> <?php echo e(__('blog.create')); ?>

    </a>
  </div>

  <div class="card-body">
    
    <form method="GET" class="row g-3 mb-4">
      <div class="col-md-4">
        <input type="text" name="name" class="form-control" placeholder="<?php echo e(__('blog.name_ar')); ?> / <?php echo e(__('blog.name_en')); ?>" value="<?php echo e(request('name')); ?>">
      </div>
      <div class="col-md-4">
        <input type="date" name="date" class="form-control" value="<?php echo e(request('date')); ?>">
      </div>
      <div class="col-md-4 text-end">
        <button type="submit" class="btn btn-outline-primary">
          <i class="bi bi-search"></i> <?php echo e(__('general.search')); ?>

        </button>
        <a href="<?php echo e(route('dashboard.blogs.index')); ?>" class="btn btn-outline-secondary">
          <i class="bi bi-x-circle"></i> <?php echo e(__('general.reset')); ?>

        </a>
      </div>
    </form>

    
    <div class="table-responsive">
      <table class="table text-center align-middle">
        <thead>
          <tr>
            <th>#</th>
            <th><?php echo e(__('blog.name_ar')); ?></th>
            <th><?php echo e(__('blog.name_en')); ?></th>
            <th><?php echo e(__('blog.date')); ?></th>
            <th><?php echo e(__('blog.active')); ?></th>
            <th><?php echo e(__('blog.actions')); ?></th>
          </tr>
        </thead>
        <tbody>
          <?php $__empty_1 = true; $__currentLoopData = $blogs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $blog): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <tr>
              <td><?php echo e($loop->iteration); ?></td>
              <td><?php echo e($blog->name_ar); ?></td>
              <td><?php echo e($blog->name_en); ?></td>
              <td><?php echo e($blog->date); ?></td>
              <td>
                <form action="<?php echo e(route('dashboard.blogs.activate', $blog->id)); ?>" method="POST">
                  <?php echo csrf_field(); ?>
                  <button class="btn btn-sm btn-<?php echo e($blog->active ? 'success' : 'secondary'); ?>">
                    <?php echo e($blog->active ? __('blog.status_active') : __('blog.status_inactive')); ?>

                  </button>
                </form>
              </td>
              <td>
                <a href="<?php echo e(route('dashboard.blogs.show', $blog->id)); ?>" class="btn btn-sm btn-info">👁️</a>
                <a href="<?php echo e(route('dashboard.blogs.edit', $blog->id)); ?>" class="btn btn-sm btn-warning">✏️</a>
              </td>
            </tr>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <tr>
              <td colspan="6"><?php echo e(__('general.no_data')); ?></td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
<?php if($blogs->hasPages()): ?>
  <div class="mt-4 flex justify-center">
    <nav class="inline-flex -space-x-px rounded-md shadow-sm" aria-label="Pagination">
      
      <?php if($blogs->onFirstPage()): ?>
        <span class="px-3 py-2 text-sm bg-gray-200 text-gray-500 border border-gray-300 rounded-l-md">‹</span>
      <?php else: ?>
        <a href="<?php echo e($blogs->previousPageUrl()); ?>" class="px-3 py-2 text-sm bg-white text-gray-700 border border-gray-300 hover:bg-gray-100 rounded-l-md">‹</a>
      <?php endif; ?>

      
      <?php $__currentLoopData = $blogs->getUrlRange(1, $blogs->lastPage()); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $page => $url): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <a href="<?php echo e($url); ?>"
           class="px-3 py-2 text-sm border border-gray-300 <?php echo e($blogs->currentPage() == $page ? 'bg-primary text-white' : 'bg-white text-gray-700 hover:bg-gray-100'); ?>">
           <?php echo e($page); ?>

        </a>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

      
      <?php if($blogs->hasMorePages()): ?>
        <a href="<?php echo e($blogs->nextPageUrl()); ?>" class="px-3 py-2 text-sm bg-white text-gray-700 border border-gray-300 hover:bg-gray-100 rounded-r-md">›</a>
      <?php else: ?>
        <span class="px-3 py-2 text-sm bg-gray-200 text-gray-500 border border-gray-300 rounded-r-md">›</span>
      <?php endif; ?>
    </nav>
  </div>
<?php endif; ?>

  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.layoutMaster', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/u552947370/domains/affiliatesummitglobal.com/public_html/resources/views/content/blogs/index.blade.php ENDPATH**/ ?>