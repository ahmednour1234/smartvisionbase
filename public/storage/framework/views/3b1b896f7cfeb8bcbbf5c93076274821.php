

<?php $__env->startSection('title', __('clients.create')); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-3xl mx-auto p-6">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold">تعديل المرجع: <?php echo e($ref->name); ?></h1>
            <p class="text-gray-600 text-sm">تحكم في الكود، السماح بالمسارات، وتاريخ الانتهاء.</p>
        </div>
        <a href="<?php echo e(route('marketing-refs.index')); ?>" class="px-4 py-2 rounded-lg border">رجوع</a>
    </div>

    <div class="bg-white shadow rounded-lg p-6">
        <form action="<?php echo e(route('marketing-refs.update', $ref)); ?>" method="POST">
            <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
            <?php echo $__env->make('content.marketing_refs._form', ['ref' => $ref], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        </form>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.layoutMaster', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/u552947370/domains/affiliatesummitglobal.com/public_html/resources/views/content/marketing_refs/edit.blade.php ENDPATH**/ ?>