<?php $__env->startSection('title', __('pixels.title')); ?>

<?php $__env->startSection('content'); ?>
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4><?php echo e(__('pixels.title')); ?></h4>
        <a href="<?php echo e(route('dashboard.pixels.create')); ?>" class="btn btn-primary">
            <i class="fa fa-plus"></i> <?php echo e(__('pixels.create')); ?>

        </a>
    </div>

    <?php if(session('success')): ?>
        <div class="alert alert-success"><?php echo e(session('success')); ?></div>
    <?php endif; ?>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>#</th>
                <th><?php echo e(__('pixels.name')); ?></th>
                <th><?php echo e(__('pixels.pixel_id')); ?></th>
                <th><?php echo e(__('pixels.status')); ?></th>
                <th><?php echo e(__('pixels.actions')); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php $__currentLoopData = $pixels; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pixel): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr>
                <td><?php echo e($loop->iteration); ?></td>
                <td><?php echo e($pixel->name); ?></td>
                <td><?php echo e($pixel->pixel_id); ?></td>
                <td>
                    <form action="<?php echo e(route('dashboard.pixels.toggle-active', $pixel->id)); ?>" method="POST">
                        <?php echo csrf_field(); ?>
                        <button class="btn btn-sm <?php echo e($pixel->active ? 'btn-success' : 'btn-secondary'); ?>">
                            <?php echo e($pixel->active ? __('pixels.active') : __('pixels.inactive')); ?>

                        </button>
                    </form>
                </td>
                <td>
                    <a href="<?php echo e(route('dashboard.pixels.edit', $pixel->id)); ?>" class="btn btn-warning btn-sm"><?php echo e(__('pixels.edit')); ?></a>
                    <form action="<?php echo e(route('dashboard.pixels.destroy', $pixel->id)); ?>" method="POST" style="display:inline-block;">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('DELETE'); ?>
                        <button class="btn btn-danger btn-sm" onclick="return confirm('<?php echo e(__('pixels.confirm_delete')); ?>')"><?php echo e(__('pixels.delete')); ?></button>
                    </form>
                </td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table>

    <?php echo e($pixels->links()); ?>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.layoutMaster', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/u552947370/domains/affiliatesummitglobal.com/public_html/resources/views/content/pixels/index.blade.php ENDPATH**/ ?>