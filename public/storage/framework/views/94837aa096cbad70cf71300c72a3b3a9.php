<?php $__env->startSection('title', __('Show Company')); ?>

<?php $__env->startSection('content'); ?>
<div class="container">
    <h4 class="mb-4"><?php echo e(__('Company Details')); ?></h4>

    <div class="card">
        <div class="card-body">

            <div class="mb-3">
                <strong><?php echo e(__('Name (AR)')); ?>:</strong>
                <p><?php echo e($company->name_ar); ?></p>
            </div>

            <div class="mb-3">
                <strong><?php echo e(__('Name (EN)')); ?>:</strong>
                <p><?php echo e($company->name_en); ?></p>
            </div>

            <div class="mb-3">
                <strong><?php echo e(__('Title (AR)')); ?>:</strong>
                <p><?php echo e($company->title_ar); ?></p>
            </div>

            <div class="mb-3">
                <strong><?php echo e(__('Title (EN)')); ?>:</strong>
                <p><?php echo e($company->title_en); ?></p>
            </div>


            <div class="mb-3">
                <strong><?php echo e(__('Status')); ?>:</strong>
                <p>
                    <?php if($company->active): ?>
                        <span class="badge bg-success"><?php echo e(__('Active')); ?></span>
                    <?php else: ?>
                        <span class="badge bg-danger"><?php echo e(__('Inactive')); ?></span>
                    <?php endif; ?>
                </p>
            </div>

     
            <div class="mb-3">
                <strong><?php echo e(__('Image')); ?>:</strong><br>
                <?php if($company->image): ?>
                    <img src="<?php echo e(asset($company->image)); ?>" alt="Image" width="150">
                <?php else: ?>
                    <span class="text-muted"><?php echo e(__('No Image')); ?></span>
                <?php endif; ?>
            </div>

        </div>
    </div>

    <a href="<?php echo e(route('dashboard.companies.index')); ?>" class="btn btn-secondary mt-3">
        <?php echo e(__('Back to List')); ?>

    </a>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.layoutMaster', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/u552947370/domains/affiliatesummitglobal.com/public_html/resources/views/content/companies/show.blade.php ENDPATH**/ ?>