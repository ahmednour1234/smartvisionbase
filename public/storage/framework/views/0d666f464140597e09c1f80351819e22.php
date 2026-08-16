<?php $__env->startSection('title', __('multi_media.title')); ?>

<?php $__env->startSection('content'); ?>
<div class="card">
  <div class="card-header d-flex justify-content-between align-items-center">
    <h5><?php echo e(__('multi_media.title')); ?></h5>
    <a href="<?php echo e(route('dashboard.multi-medias.create')); ?>" class="btn btn-primary">
      <i class="bi bi-plus"></i> <?php echo e(__('multi_media.create')); ?>

    </a>
  </div>

  <div class="card-body">
    
    <form method="GET" class="row mb-4 g-3">
      <div class="col-md-4">
        <input type="text" name="name" class="form-control" placeholder="<?php echo e(__('multi_media.search_name')); ?>"
               value="<?php echo e(request('name')); ?>">
      </div>

      <div class="col-md-4">
        <input type="date" name="date" class="form-control"
               value="<?php echo e(request('date')); ?>">
      </div>

      <div class="col-md-4">
        <select name="multi_media_category_id" class="form-select">
          <option value=""><?php echo e(__('multi_media.all_categories')); ?></option>
          <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $id => $name): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($id); ?>" <?php echo e(request('multi_media_category_id') == $id ? 'selected' : ''); ?>>
              <?php echo e($name); ?>

            </option>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
      </div>

      <div class="col-md-12 text-end">
        <button type="submit" class="btn btn-outline-primary">
          <i class="bi bi-search"></i> <?php echo e(__('multi_media.search')); ?>

        </button>
        <a href="<?php echo e(route('dashboard.multi-medias.index')); ?>" class="btn btn-outline-secondary">
          <i class="bi bi-x-circle"></i> <?php echo e(__('multi_media.reset')); ?>

        </a>
      </div>
    </form>

    
    <div class="table-responsive">
      <table class="table align-middle text-center">
        <thead>
          <tr>
            <th>#</th>
            <th><?php echo e(__('multi_media.name_ar')); ?></th>
            <th><?php echo e(__('multi_media.name_en')); ?></th>
            <th><?php echo e(__('multi_media.date')); ?></th>
            <th><?php echo e(__('multi_media.active')); ?></th>
            <th><?php echo e(__('multi_media.actions')); ?></th>
          </tr>
        </thead>
        <tbody>
          <?php $__empty_1 = true; $__currentLoopData = $multiMedias; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $media): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <tr>
              <td><?php echo e($loop->iteration); ?></td>
              <td><?php echo e($media->name_ar); ?></td>
              <td><?php echo e($media->name_en); ?></td>
              <td><?php echo e($media->date); ?></td>
              <td>
                <form action="<?php echo e(route('dashboard.multi-medias.activate', $media->id)); ?>" method="POST">
                  <?php echo csrf_field(); ?>
                  <button class="btn btn-sm btn-<?php echo e($media->active ? 'success' : 'secondary'); ?>">
                    <?php echo e($media->active ? __('multi_media.status_active') : __('multi_media.status_inactive')); ?>

                  </button>
                </form>
              </td>
              <td>
                <a href="<?php echo e(route('dashboard.multi-medias.show', $media->id)); ?>" class="btn btn-sm btn-info">
                  <i class="bi bi-eye"></i> <?php echo e(__('multi_media.show')); ?>

                </a>
                <a href="<?php echo e(route('dashboard.multi-medias.edit', $media->id)); ?>" class="btn btn-sm btn-warning">
                  <i class="bi bi-pencil-square"></i> <?php echo e(__('multi_media.edit')); ?>

                </a>
              </td>
            </tr>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <tr>
              <td colspan="6"><?php echo e(__('multi_media.no_data')); ?></td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
<?php if($multiMedias->hasPages()): ?>
  <div class="mt-4 flex justify-center">
    <nav class="inline-flex rounded-md shadow-sm" aria-label="Pagination">
      
      <?php if($multiMedias->onFirstPage()): ?>
        <span class="px-3 py-2 text-sm bg-gray-200 text-gray-500 border border-gray-300 rounded-l-md">‹</span>
      <?php else: ?>
        <a href="<?php echo e($multiMedias->previousPageUrl()); ?>" class="px-3 py-2 text-sm bg-white text-gray-700 border border-gray-300 hover:bg-gray-100 rounded-l-md">‹</a>
      <?php endif; ?>

      
      <?php $__currentLoopData = $multiMedias->getUrlRange(1, $multiMedias->lastPage()); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $page => $url): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <a href="<?php echo e($url); ?>"
           class="px-3 py-2 text-sm border border-gray-300 <?php echo e($multiMedias->currentPage() == $page ? 'bg-primary text-white' : 'bg-white text-gray-700 hover:bg-gray-100'); ?>">
          <?php echo e($page); ?>

        </a>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

      
      <?php if($multiMedias->hasMorePages()): ?>
        <a href="<?php echo e($multiMedias->nextPageUrl()); ?>" class="px-3 py-2 text-sm bg-white text-gray-700 border border-gray-300 hover:bg-gray-100 rounded-r-md">›</a>
      <?php else: ?>
        <span class="px-3 py-2 text-sm bg-gray-200 text-gray-500 border border-gray-300 rounded-r-md">›</span>
      <?php endif; ?>
    </nav>
  </div>
<?php endif; ?>

  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.layoutMaster', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/u552947370/domains/affiliatesummitglobal.com/public_html/resources/views/content/multi_mediall/index.blade.php ENDPATH**/ ?>