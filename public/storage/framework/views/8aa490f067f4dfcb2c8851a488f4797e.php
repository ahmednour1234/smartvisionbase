<?php $__env->startSection('title', __('Add Influncer')); ?>

<?php $__env->startSection('content'); ?>
<div class="container">
    <h4 class="mb-4"><?php echo e(__('Add Influncer')); ?></h4>
    <?php echo $__env->make('content.companies._form', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.layoutMaster', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/u552947370/domains/affiliatesummitglobal.com/public_html/resources/views/content/companies/create.blade.php ENDPATH**/ ?>