<?php $__env->startSection('title', __('gallery.add')); ?>

<?php $__env->startSection('content'); ?>
<div class="card shadow-sm">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0"><?php echo e(__('gallery.add')); ?></h5>
    </div>
    <div class="card-body">
        <form action="<?php echo e(route('dashboard.galleries.store')); ?>" method="POST" enctype="multipart/form-data">
            <?php echo csrf_field(); ?>

            <?php echo $__env->make('content.galleries._form', ['isEdit' => false], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

            <div class="mt-4 text-center">
                <button type="submit" class="btn btn-success">
                    <i class="bi bi-upload"></i> <?php echo e(__('gallery.upload')); ?>

                </button>
                <a href="<?php echo e(route('dashboard.galleries.index')); ?>" class="btn btn-outline-secondary ms-2">
                    <?php echo e(__('gallery.cancel')); ?>

                </a>
            </div>
        </form>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.layoutMaster', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/u552947370/domains/affiliatesummitglobal.com/public_html/resources/views/content/galleries/create.blade.php ENDPATH**/ ?>