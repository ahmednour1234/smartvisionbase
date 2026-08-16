<?php
    $isEdit = isset($pdf);
?>

<form action="<?php echo e($isEdit ? route('pdfs.update', $pdf) : route('pdfs.store')); ?>" method="POST" enctype="multipart/form-data">
    <?php echo csrf_field(); ?>
    <?php if($isEdit): ?>
        <?php echo method_field('PUT'); ?>
    <?php endif; ?>

    <div class="mb-3">
        <label for="name" class="form-label"><?php echo e(__('pdfs.name')); ?></label>
        <input type="text" name="name" id="name" class="form-control" required
               value="<?php echo e(old('name', $pdf->name ?? '')); ?>">
    </div>

    <div class="mb-3">
        <label for="pdf" class="form-label"><?php echo e(__('pdfs.link')); ?></label>
        <input type="file" name="pdf" id="pdf" class="form-control" <?php echo e($isEdit ? '' : 'required'); ?>>
        <?php if($isEdit && $pdf->pdf): ?>
            <small class="text-muted">
                <?php echo e(__('pdfs.current_file')); ?>:
                <a href="<?php echo e(asset($pdf->pdf)); ?>" target="_blank"><?php echo e(__('pdfs.view')); ?></a>
            </small>
        <?php endif; ?>
    </div>

    <button type="submit" class="btn btn-success">
        <?php echo e($isEdit ? __('pdfs.update') : __('pdfs.save')); ?>

    </button>
</form>
<?php /**PATH /home/u552947370/domains/affiliatesummitglobal.com/public_html/resources/views/content/pdfs/_form.blade.php ENDPATH**/ ?>