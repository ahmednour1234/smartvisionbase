<?php
    /** @var \App\Models\MultiMedia|null $media */
    $isEdit = isset($media);
?>

<div class="row gy-3">
  
  <div class="col-md-6">
    <label class="form-label"><?php echo e(__('multi_media.name_ar')); ?></label>
    <input type="text" name="name_ar"
           class="form-control <?php $__errorArgs = ['name_ar'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
           value="<?php echo e(old('name_ar', $isEdit ? $media->name_ar : '')); ?>">
    <?php $__errorArgs = ['name_ar'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
  </div>

  
  <div class="col-md-6">
    <label class="form-label"><?php echo e(__('multi_media.name_en')); ?></label>
    <input type="text" name="name_en"
           class="form-control <?php $__errorArgs = ['name_en'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
           value="<?php echo e(old('name_en', $isEdit ? $media->name_en : '')); ?>">
    <?php $__errorArgs = ['name_en'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
  </div>

  
  <div class="col-md-6">
    <label class="form-label"><?php echo e(__('multi_media.date')); ?></label>
    <input type="date" name="date"
           class="form-control <?php $__errorArgs = ['date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
           value="<?php echo e(old('date', $isEdit && $media->date ? $media->date->format('Y-m-d') : '')); ?>">
    <?php $__errorArgs = ['date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
  </div>

  
  <div class="col-md-6">
    <label class="form-label"><?php echo e(__('multi_media.category')); ?></label>
    <select name="multi_media_category_id"
            class="form-select <?php $__errorArgs = ['multi_media_category_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
      <option value=""><?php echo e(__('multi_media.select_category')); ?></option>
      <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $id => $name): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <option value="<?php echo e($id); ?>"
          <?php echo e(old('multi_media_category_id', $isEdit ? $media->multi_media_category_id : '') == $id ? 'selected' : ''); ?>>
          <?php echo e($name); ?>

        </option>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </select>
    <?php $__errorArgs = ['multi_media_category_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
  </div>

  
  <div class="col-md-6">
    <label class="form-label"><?php echo e(__('multi_media.images')); ?></label>
    <input type="file" name="images[]"
           class="form-control <?php $__errorArgs = ['images'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
           multiple accept="image/*">
    <?php $__errorArgs = ['images'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    <?php $__errorArgs = ['images.*'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>

    <?php if($isEdit && is_array($media->images) && count($media->images)): ?>
      <div class="mt-3 d-flex flex-wrap gap-2">
        <?php $__currentLoopData = $media->images; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $imgIndex => $img): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <div class="position-relative" style="width: 80px; height: 80px;">
            <img src="<?php echo e(asset('public/'.$img)); ?>"
                 alt="Image"
                 width="80" height="80"
                 style="object-fit: cover; border-radius: 5px;">

            <form method="POST"
                  action="<?php echo e(route('multi-medias.images.destroy', [$media->id, $imgIndex])); ?>"
                  class="position-absolute top-0 end-0"
                  onsubmit="return confirm('<?php echo e(__('multi_media.confirm_delete_image')); ?>');">
              <?php echo csrf_field(); ?>
              <?php echo method_field('DELETE'); ?>
              <button type="submit"
                      class="btn btn-sm btn-danger"
                      style="padding:0 4px; border-radius:50%;">
                &times;
              </button>
            </form>
          </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </div>
    <?php endif; ?>
  </div>

  
  <div class="col-md-6">
    <label class="form-label"><?php echo e(__('multi_media.links')); ?></label>

    <?php
      $links = old('links', $isEdit ? $media->links : ['']);
      if (!is_array($links)) {
          $links = [$links];
      }
    ?>

    <div class="d-flex flex-column gap-2">
      
      <?php if($isEdit && is_array($media->links) && count($media->links)): ?>
        <?php $__currentLoopData = $media->links; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $link): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <div class="input-group">
            <input type="url"
                   name="links[<?php echo e($index); ?>]"
                   class="form-control <?php $__errorArgs = ["links.$index"];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                   value="<?php echo e(old('links.'.$index, $link)); ?>"
                   placeholder="https://youtube.com/...">
            <?php $__errorArgs = ["links.$index"];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback d-block"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>

            <button type="submit"
                    form="delete-link-<?php echo e($index); ?>"
                    class="btn btn-outline-danger"
                    onclick="return confirm('<?php echo e(__('multi_media.confirm_delete_link')); ?>');">
              <i class="bi bi-x"></i>
            </button>
          </div>

          <form id="delete-link-<?php echo e($index); ?>"
                method="POST"
                action="<?php echo e(route('multi-medias.links.destroy', [$media->id, $index])); ?>">
            <?php echo csrf_field(); ?>
            <?php echo method_field('DELETE'); ?>
          </form>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      <?php else: ?>
        
        <?php $__currentLoopData = $links; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $link): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <input type="url"
                 name="links[<?php echo e($index); ?>]"
                 class="form-control <?php $__errorArgs = ["links.$index"];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                 value="<?php echo e($link); ?>"
                 placeholder="https://youtube.com/...">
          <?php $__errorArgs = ["links.$index"];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      <?php endif; ?>

      
      <input type="url" name="links[]"
             class="form-control"
             placeholder="https://youtube.com/...">
    </div>
  </div>

  
  <div class="col-md-6">
    <label class="form-label"><?php echo e(__('multi_media.active')); ?></label>
    <select name="active"
            class="form-select <?php $__errorArgs = ['active'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
      <option value="1" <?php echo e(old('active', $isEdit ? $media->active : 1) == 1 ? 'selected' : ''); ?>>
        <?php echo e(__('multi_media.status_active')); ?>

      </option>
      <option value="0" <?php echo e(old('active', $isEdit ? $media->active : 1) == 0 ? 'selected' : ''); ?>>
        <?php echo e(__('multi_media.status_inactive')); ?>

      </option>
    </select>
    <?php $__errorArgs = ['active'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
  </div>

  
  <div class="col-12 text-end mt-4">
    <button type="submit" class="btn btn-primary px-4">
      <i class="bi bi-save me-1"></i> <?php echo e($button); ?>

    </button>
    <a href="<?php echo e(route('dashboard.multi-medias.index')); ?>" class="btn btn-outline-secondary px-4">
      <?php echo e(__('multi_media.back')); ?>

    </a>
  </div>
</div>
<?php /**PATH /home/u552947370/domains/affiliatesummitglobal.com/public_html/resources/views/content/multi_mediall/_form.blade.php ENDPATH**/ ?>