<?php $__env->startSection('title', __('Companies')); ?>

<?php $__env->startSection('content'); ?>
<style>
    .pagination {
        display: flex;
        list-style: none;
        padding-left: 0;
    }

    .page-item {
        margin: 0 3px;
    }

    .page-link {
        display: block;
        padding: 8px 14px;
        border: 1px solid #dee2e6;
        border-radius: 4px;
        color: #007bff;
        text-decoration: none;
        background-color: #fff;
        transition: all 0.3s ease;
    }

    .page-link:hover {
        background-color: #f1f1f1;
        color: #0056b3;
    }

    .page-item.active .page-link {
        background-color: #007bff;
        color: white;
        border-color: #007bff;
    }

    .page-item.disabled .page-link {
        color: #6c757d;
        pointer-events: none;
        background-color: #f8f9fa;
        border-color: #dee2e6;
    }
</style>

<div class="container">
    <form method="GET" action="<?php echo e(route('dashboard.companies.index')); ?>" class="row g-2 align-items-end mb-3">
    <div class="col-md-4">
        <label class="form-label"><?php echo e(__('Search')); ?></label>
        <input type="text" name="q" class="form-control"
               placeholder="<?php echo e(__('Search by Arabic/English name')); ?>"
               value="<?php echo e(request('q')); ?>">
    </div>

    <div class="col-md-2">
        <label class="form-label"><?php echo e(__('Status')); ?></label>
        <select name="status" class="form-select">
            <option value="all"><?php echo e(__('All')); ?></option>
            <option value="active"   <?php if(request('status')==='active'): echo 'selected'; endif; ?>><?php echo e(__('Active')); ?></option>
            <option value="inactive" <?php if(request('status')==='inactive'): echo 'selected'; endif; ?>><?php echo e(__('Inactive')); ?></option>
        </select>
    </div>

    <div class="col-md-2">
        <label class="form-label"><?php echo e(__('Min Votes')); ?></label>
        <input type="number" name="min_votes" class="form-control" value="<?php echo e(request('min_votes')); ?>">
    </div>

    <div class="col-md-2">
        <label class="form-label"><?php echo e(__('Max Votes')); ?></label>
        <input type="number" name="max_votes" class="form-control" value="<?php echo e(request('max_votes')); ?>">
    </div>

    <div class="col-md-2">
        <label class="form-label"><?php echo e(__('Per page')); ?></label>
        <select name="per_page" class="form-select">
            <?php $__currentLoopData = [10,20,30,50,100,200]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $n): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($n); ?>" <?php if(request('per_page', 20)==$n): echo 'selected'; endif; ?>><?php echo e($n); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
    </div>

    <div class="col-md-2 d-flex gap-2">
        <button type="submit" class="btn btn-secondary w-100"><?php echo e(__('Filter')); ?></button>
        <a href="<?php echo e(route('dashboard.companies.index')); ?>" class="btn btn-light w-100"><?php echo e(__('Reset')); ?></a>
    </div>
</form>

    <h4 class="mb-4"><?php echo e(__('Influncer List')); ?></h4>

    <a href="<?php echo e(route('dashboard.companies.create')); ?>" class="btn btn-primary mb-3">
        + <?php echo e(__('Add Influncer')); ?>

    </a>

    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>#</th>
                <th><?php echo e(__('Name (AR)')); ?></th>
                <th><?php echo e(__('Name (EN)')); ?></th>
                <th><?php echo e(__('Votes')); ?></th>
                <th><?php echo e(__('Status')); ?></th>
                <th><?php echo e(__('Image')); ?></th>
                <th><?php echo e(__('Actions')); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php $__empty_1 = true; $__currentLoopData = $companies; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $company): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                    <td><?php echo e($loop->iteration); ?></td>
                    <td><?php echo e($company->name_ar); ?></td>
                    <td><?php echo e($company->name_en); ?></td>
                    <td><?php echo e($company->count_vote); ?></td>
                    <td>
                        <?php if($company->active): ?>
                            <span class="badge bg-success"><?php echo e(__('Active')); ?></span>
                        <?php else: ?>
                            <span class="badge bg-danger"><?php echo e(__('Inactive')); ?></span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if($company->image): ?>
                            <img src="<?php echo e(asset('public/'.$company->image)); ?>" alt="Image" width="60">
                        <?php endif; ?>
                    </td>
                   <td>
    <a href="<?php echo e(route('dashboard.companies.show', $company->id)); ?>" class="btn btn-info btn-sm">
        <?php echo e(__('Show')); ?>

    </a>

    <a href="<?php echo e(route('dashboard.companies.edit', $company->id)); ?>" class="btn btn-warning btn-sm">
        <?php echo e(__('Edit')); ?>

    </a>

    <?php if($company->active): ?>
        <form action="<?php echo e(route('dashboard.companies.deactivate', $company->id)); ?>" method="POST" style="display:inline-block;">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PATCH'); ?>
            <button type="submit" class="btn btn-danger btn-sm"><?php echo e(__('Deactivate')); ?></button>
        </form>
    <?php else: ?>
        <form action="<?php echo e(route('dashboard.companies.activate', $company->id)); ?>" method="POST" style="display:inline-block;">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PATCH'); ?>
            <button type="submit" class="btn btn-success btn-sm"><?php echo e(__('Activate')); ?></button>
        </form>
    <?php endif; ?>

    
    <form action="<?php echo e(route('dashboard.companies.destroy', $company->id)); ?>" method="POST" style="display:inline-block;" onsubmit="return confirm('<?php echo e(__('Are you sure you want to delete this company?')); ?>')">
        <?php echo csrf_field(); ?>
        <?php echo method_field('DELETE'); ?>
        <button type="submit" class="btn btn-outline-danger btn-sm">
            <?php echo e(__('Delete')); ?>

        </button>
    </form>
</td>

                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="8" class="text-center"><?php echo e(__('No companies found.')); ?></td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
<?php if($companies->hasPages()): ?>
    <nav class="mt-4">
        <ul class="pagination justify-content-center">

            
            <?php if($companies->onFirstPage()): ?>
                <li class="page-item disabled"><span class="page-link">&laquo; السابق</span></li>
            <?php else: ?>
                <li class="page-item">
                    <a class="page-link" href="<?php echo e($companies->previousPageUrl()); ?>" rel="prev">&laquo; السابق</a>
                </li>
            <?php endif; ?>

            
            <?php for($page = 1; $page <= $companies->lastPage(); $page++): ?>
                <li class="page-item <?php echo e($companies->currentPage() == $page ? 'active' : ''); ?>">
                    <a class="page-link" href="<?php echo e($companies->url($page)); ?>"><?php echo e($page); ?></a>
                </li>
            <?php endfor; ?>

            
            <?php if($companies->hasMorePages()): ?>
                <li class="page-item">
                    <a class="page-link" href="<?php echo e($companies->nextPageUrl()); ?>" rel="next">التالي &raquo;</a>
                </li>
            <?php else: ?>
                <li class="page-item disabled"><span class="page-link">التالي &raquo;</span></li>
            <?php endif; ?>

        </ul>
    </nav>
<?php endif; ?>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.layoutMaster', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/u552947370/domains/affiliatesummitglobal.com/public_html/resources/views/content/companies/index.blade.php ENDPATH**/ ?>