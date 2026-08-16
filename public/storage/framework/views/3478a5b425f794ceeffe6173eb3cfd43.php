<div class="row gy-3">
    
    <div class="col-md-6">
        <label for="name_ar" class="form-label"><?php echo e(__('multi_media.name_ar')); ?> <span class="text-danger">*</span></label>
        <input type="text" name="name_ar" id="name_ar" value="<?php echo e(old('name_ar', $category->name_ar ?? '')); ?>"
            class="form-control <?php $__errorArgs = ['name_ar'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" placeholder="<?php echo e(__('multi_media.name_ar')); ?>">
        <?php $__errorArgs = ['name_ar'];
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

    
    <div class="col-md-6">
        <label for="name_en" class="form-label"><?php echo e(__('multi_media.name_en')); ?> <span
                class="text-danger">*</span></label>
        <input type="text" name="name_en" id="name_en" value="<?php echo e(old('name_en', $category->name_en ?? '')); ?>"
            class="form-control <?php $__errorArgs = ['name_en'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" placeholder="<?php echo e(__('multi_media.name_en')); ?>">
        <?php $__errorArgs = ['name_en'];
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


    
    <div class="col-md-6">
        <label for="promo" class="form-label"><?php echo e(__('multi_media.promo_url')); ?></label>
        <input type="url" name="promo" id="promo" value="<?php echo e(old('promo', $category->promo ?? '')); ?>"
            class="form-control <?php $__errorArgs = ['promo'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" placeholder="https://youtube.com/...">
        <?php $__errorArgs = ['promo'];
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

    
    <div class="col-md-6">
        <label for="logo" class="form-label"><?php echo e(__('multi_media.logo')); ?></label>
        <input type="file" name="logo" id="logo" class="form-control <?php $__errorArgs = ['logo'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
        <?php $__errorArgs = ['logo'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
            <div class="invalid-feedback"><?php echo e($message); ?></div>
        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>

        <?php if(!empty($category->logo)): ?>
            <div class="mt-2">
                <img src="<?php echo e(asset($category->logo)); ?>" alt="logo" class="img-thumbnail"
                    style="width: 80px; height: auto;">
            </div>
        <?php endif; ?>
    </div>

    
    <div class="col-md-6">
        <label for="active" class="form-label"><?php echo e(__('multi_media.active')); ?></label>
        <select name="active" id="active" class="form-select">
            <option value="1" <?php echo e(old('active', $category->active ?? 1) == 1 ? 'selected' : ''); ?>>
                <?php echo e(__('multi_media.status_active')); ?>

            </option>
            <option value="0" <?php echo e(old('active', $category->active ?? 1) == 0 ? 'selected' : ''); ?>>
                <?php echo e(__('multi_media.status_inactive')); ?>

            </option>
        </select>
    </div>

    
    <div class="col-12 mt-4 text-end">
        <button type="submit" class="btn btn-primary px-4">
            <i class="bi bi-save me-1"></i> <?php echo e($button); ?>

        </button>
        <a href="<?php echo e(route('dashboard.multimedia-categories.index')); ?>" class="btn btn-outline-secondary ms-2 px-4">
            <i class="bi bi-arrow-left"></i> <?php echo e(__('multi_media.back')); ?>

        </a>
    </div>
</div>
<?php /**PATH /home/u552947370/domains/affiliatesummitglobal.com/public_html/resources/views/content/multi_media/_form.blade.php ENDPATH**/ ?>