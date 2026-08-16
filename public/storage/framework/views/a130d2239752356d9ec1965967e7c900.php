

<?php $__env->startSection('title', __('clients.create')); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-3xl mx-auto p-6">
    <div class="mb-6">
        <h1 class="text-2xl font-bold">إضافة مرجع تسويقي</h1>
        <p class="text-gray-600 text-sm">سيتم تتبع التسجيلات القادمة من هذا المرجع فقط.</p>
    </div>

    <div class="bg-white shadow rounded-lg p-6">
        <form action="<?php echo e(route('marketing-refs.store')); ?>" method="POST">
            <?php echo csrf_field(); ?>
            <?php echo $__env->make('content.marketing_refs._form', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        </form>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.layoutMaster', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/u552947370/domains/affiliatesummitglobal.com/public_html/resources/views/content/marketing_refs/create.blade.php ENDPATH**/ ?>