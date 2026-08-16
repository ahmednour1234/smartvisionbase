<?php $__env->startSection('title', __('sponsor.page_title')); ?>

<?php $__env->startSection('content'); ?>
<div class="card">
  <div class="card-header d-flex justify-content-between align-items-center">
    <h5 class="mb-0"><?php echo e(__('sponsor.page_title')); ?></h5>
    <a href="<?php echo e(route('admin.sponsors.create')); ?>" class="btn btn-primary">
      <i class="fas fa-plus me-1"></i> <?php echo e(__('sponsor.add')); ?>

    </a>
  </div>

  <div class="card-body">
    <form method="GET" action="<?php echo e(route('admin.sponsors.index')); ?>" class="row g-3 mb-4">
      <div class="col-md-4">
        <input type="text" name="name" class="form-control" placeholder="<?php echo e(__('sponsor.search_name')); ?>" value="<?php echo e(request('name')); ?>">
      </div>
      <div class="col-md-4">
        <select name="category_sponsor_id" class="form-select">
          <option value=""><?php echo e(__('sponsor.category')); ?></option>
          <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($category->id); ?>" <?php echo e(request('category_sponsor_id') == $category->id ? 'selected' : ''); ?>>
              <?php echo e(app()->getLocale() == 'ar' ? $category->name : $category->name_en); ?>

            </option>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
      </div>
      <div class="col-md-2">
        <button class="btn btn-outline-primary w-100" type="submit">
          <i class="fas fa-search"></i> <?php echo e(__('general.search')); ?>

        </button>
      </div>
    </form>

    <div class="table-responsive">
      <table class="table align-middle">
        <thead>
          <tr>
            <th>#</th>
            <th><?php echo e(__('sponsor.image')); ?></th>
            <th><?php echo e(__('sponsor.name_ar')); ?></th>
            <th><?php echo e(__('sponsor.name_en')); ?></th>
            <th><?php echo e(__('sponsor.category')); ?></th>
            <th><?php echo e(__('sponsor.status')); ?></th>
            <th><?php echo e(__('sponsor.actions')); ?></th>
          </tr>
        </thead>
        <tbody>
          <?php $__empty_1 = true; $__currentLoopData = $sponsors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sponsor): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <tr>
              <td><?php echo e($loop->iteration + ($sponsors->currentPage() - 1) * $sponsors->perPage()); ?></td>
              <td>
                <?php if($sponsor->image): ?>
                  <img src="<?php echo e(asset('public/'.$sponsor->image)); ?>" width="60" height="60" class="rounded-circle">
                <?php else: ?>
                  <span class="text-muted">-</span>
                <?php endif; ?>
              </td>
              <td><?php echo e($sponsor->name_ar); ?></td>
              <td><?php echo e($sponsor->name_en); ?></td>
              <td><?php echo e(app()->getLocale() == 'ar' ? $sponsor->category->name ?? '-' : $sponsor->category->name_en ?? '-'); ?></td>
              <td>
                <?php if($sponsor->active): ?>
                  <span class="badge bg-success"><?php echo e(__('sponsor.active')); ?></span>
                <?php else: ?>
                  <span class="badge bg-danger"><?php echo e(__('sponsor.inactive')); ?></span>
                <?php endif; ?>
              </td>
              <td>
                <a href="<?php echo e(route('admin.sponsors.show', $sponsor->id)); ?>" class="btn btn-sm btn-outline-info" title="<?php echo e(__('general.show')); ?>">
                  <i class="fas fa-eye"></i>
                </a>

                <a href="<?php echo e(route('admin.sponsors.edit', $sponsor->id)); ?>" class="btn btn-sm btn-outline-primary" title="<?php echo e(__('general.edit')); ?>">
                  <i class="fas fa-edit"></i>
                </a>

                <a href="<?php echo e(route('admin.sponsors.deactivate', $sponsor->id)); ?>" class="btn btn-sm btn-outline-warning" title="<?php echo e(__('general.toggle_status')); ?>">
                  <i class="fas fa-power-off"></i>
                </a>

                <form action="<?php echo e(route('admin.sponsors.destroy', $sponsor->id)); ?>" method="POST" class="d-inline" onsubmit="return confirm('<?php echo e(__('general.confirm_delete')); ?>')">
                  <?php echo csrf_field(); ?>
                  <?php echo method_field('DELETE'); ?>
                  <button class="btn btn-sm btn-outline-danger" title="<?php echo e(__('general.delete')); ?>">
                    <i class="fas fa-trash"></i>
                  </button>
                </form> 
              </td>
            </tr>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <tr>
              <td colspan="7" class="text-center"><?php echo e(__('general.no_data')); ?></td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
<?php if($sponsors->hasPages()): ?>
  <div class="mt-4 flex justify-center">
    <nav class="inline-flex shadow-sm rounded-md" aria-label="Pagination">
      
      <?php if($sponsors->onFirstPage()): ?>
        <span class="px-3 py-2 bg-gray-200 text-gray-500 border border-gray-300 rounded-l-md">‹</span>
      <?php else: ?>
        <a href="<?php echo e($sponsors->previousPageUrl()); ?>" class="px-3 py-2 bg-white text-gray-700 border border-gray-300 hover:bg-gray-100 rounded-l-md">‹</a>
      <?php endif; ?>

      
      <?php $__currentLoopData = $sponsors->getUrlRange(1, $sponsors->lastPage()); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $page => $url): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <a href="<?php echo e($url); ?>"
           class="px-3 py-2 border border-gray-300 <?php echo e($sponsors->currentPage() == $page ? 'bg-primary text-white' : 'bg-white text-gray-700 hover:bg-gray-100'); ?>">
          <?php echo e($page); ?>

        </a>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

      
      <?php if($sponsors->hasMorePages()): ?>
        <a href="<?php echo e($sponsors->nextPageUrl()); ?>" class="px-3 py-2 bg-white text-gray-700 border border-gray-300 hover:bg-gray-100 rounded-r-md">›</a>
      <?php else: ?>
        <span class="px-3 py-2 bg-gray-200 text-gray-500 border border-gray-300 rounded-r-md">›</span>
      <?php endif; ?>
    </nav>
  </div>
<?php endif; ?>

  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.layoutMaster', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/u552947370/domains/affiliatesummitglobal.com/public_html/resources/views/content/sponsors/index.blade.php ENDPATH**/ ?>