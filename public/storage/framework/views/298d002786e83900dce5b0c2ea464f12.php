<?php $__env->startSection('title', __('gallery.title')); ?>

<?php $__env->startSection('content'); ?>
<div class="card shadow-sm">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0"><?php echo e(__('gallery.title')); ?></h5>
    </div>
    <div class="card-body">
        <div class="mb-3 text-end">
            <a href="<?php echo e(route('dashboard.galleries.create')); ?>" class="btn btn-success">
                <i class="bi bi-plus-circle"></i> <?php echo e(__('gallery.add')); ?>

            </a>
        </div>

        <?php if($galleries->count()): ?>
            <div class="row">
                <?php $__currentLoopData = $galleries; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $gallery): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="col-md-3 mb-4">
                        <div class="card h-100 border rounded shadow-sm">
                            <img src="<?php echo e(asset($gallery->image)); ?>" class="card-img-top" alt="image" style="object-fit:cover;height:200px">
                            <div class="card-body p-2 text-center">
                                <form action="<?php echo e(route('dashboard.galleries.destroy', $gallery->id)); ?>" method="POST" onsubmit="return confirm('<?php echo e(__('gallery.confirm_delete')); ?>')">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('DELETE'); ?>
                                    <button type="submit" class="btn btn-sm btn-danger w-100">
                                        <i class="bi bi-trash"></i> <?php echo e(__('gallery.delete')); ?>

                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>

            <div class="d-flex justify-content-center mt-4">
                <?php echo e($galleries->links()); ?>

            </div>
        <?php else: ?>
            <p class="text-muted text-center"><?php echo e(__('gallery.no_data')); ?></p>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.layoutMaster', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/u552947370/domains/affiliatesummitglobal.com/public_html/resources/views/content/galleries/index.blade.php ENDPATH**/ ?>