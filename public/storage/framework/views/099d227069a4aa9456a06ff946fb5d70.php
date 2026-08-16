<?php $__env->startSection('title', __('pdfs.title')); ?>

<?php $__env->startSection('content'); ?>
<div class="container">
    <h2 class="mb-4"><?php echo e(__('pdfs.title')); ?></h2>

    <a href="<?php echo e(route('pdfs.create')); ?>" class="btn btn-primary mb-3"><?php echo e(__('pdfs.add_new')); ?></a>

    <?php if(session('success')): ?>
        <div class="alert alert-success"><?php echo e(session('success')); ?></div>
    <?php endif; ?>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th><?php echo e(__('pdfs.name')); ?></th>
                <th><?php echo e(__('pdfs.link')); ?></th>
                <th><?php echo e(__('pdfs.status')); ?></th>
                <th><?php echo e(__('pdfs.actions')); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php $__currentLoopData = $pdfs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pdf): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td><?php echo e($pdf->name); ?></td>
                    <td>
                        <div class="d-flex flex-column">
                            <input type="text" class="form-control link-input mb-1" value="<?php echo e(asset('public/'.$pdf->pdf)); ?>" readonly>
                            <button class="btn btn-sm btn-outline-secondary copy-btn" data-link="<?php echo e(asset('public/'.$pdf->pdf)); ?>">
                                <?php echo e(__('pdfs.copy_link')); ?>

                            </button>
                        </div>
                    </td>
                    <td>
                        <?php if($pdf->active): ?>
                            <span class="badge bg-success"><?php echo e(__('pdfs.active')); ?></span>
                        <?php else: ?>
                            <span class="badge bg-secondary"><?php echo e(__('pdfs.inactive')); ?></span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <a href="<?php echo e(route('pdfs.edit', $pdf)); ?>" class="btn btn-sm btn-info"><?php echo e(__('pdfs.edit')); ?></a>

                        <form action="<?php echo e(route('pdfs.destroy', $pdf)); ?>" method="POST" style="display:inline-block;">
                            <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('<?php echo e(__('pdfs.confirm_delete')); ?>')">
                                <?php echo e(__('pdfs.delete')); ?>

                            </button>
                        </form>

                        <form action="<?php echo e(route('pdfs.toggle-active', $pdf)); ?>" method="POST" style="display:inline-block;">
                            <?php echo csrf_field(); ?>
                            <button type="submit" class="btn btn-sm btn-warning">
                                <?php echo e($pdf->active ? __('pdfs.deactivate') : __('pdfs.activate')); ?>

                            </button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table>
</div>
<?php $__env->stopSection(); ?>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.copy-btn').forEach(function(button) {
            button.addEventListener('click', function () {
                const link = this.getAttribute('data-link');
                navigator.clipboard.writeText(link).then(() => {
                    alert("<?php echo e(__('pdfs.link_copied')); ?>");
                });
            });
        });
    });
</script>

<?php echo $__env->make('layouts.layoutMaster', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/u552947370/domains/affiliatesummitglobal.com/public_html/resources/views/content/pdfs/index.blade.php ENDPATH**/ ?>