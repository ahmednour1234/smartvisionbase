<?php $__env->startSection('title', __('pdfs.add_new')); ?>

<?php $__env->startSection('content'); ?>
<div class="container">
    <h2 class="mb-4"><?php echo e(__('pdfs.add_new')); ?></h2>

    <?php echo $__env->make('content.pdfs._form', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.layoutMaster', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/u552947370/domains/affiliatesummitglobal.com/public_html/resources/views/content/pdfs/create.blade.php ENDPATH**/ ?>