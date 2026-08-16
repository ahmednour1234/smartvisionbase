
<div class="mb-3">
    <label for="image" class="form-label"><?php echo e(__('gallery.image')); ?>

        <?php if(!$isEdit): ?>
            <span class="text-danger">*</span>
        <?php endif; ?>
    </label>

    <?php if($isEdit): ?>
        <div class="mb-2">
            <img src="<?php echo e(asset($gallery->image)); ?>" alt="preview" class="img-thumbnail" style="max-height: 150px;">
        </div>
    <?php endif; ?>

    <input type="file"
           name="<?php echo e($isEdit ? 'image' : 'images[]'); ?>"
           id="image"
           class="form-control <?php $__errorArgs = [$isEdit ? 'image' : 'images.*'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
           <?php echo e($isEdit ? '' : 'multiple required'); ?>>

    <?php $__errorArgs = [$isEdit ? 'image' : 'images.*'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
        <div class="invalid-feedback"><?php echo e($message); ?></div>
    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
</div>
<?php /**PATH /home/u552947370/domains/affiliatesummitglobal.com/public_html/resources/views/content/galleries/_form.blade.php ENDPATH**/ ?>