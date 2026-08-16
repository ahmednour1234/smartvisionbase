<?php
  $isEdit = isset($form);
?>

<div class="row g-3">
  
  <div class="col-md-6">
    <label for="name_ar" class="form-label"><?php echo e(__('form_name_ar')); ?> <span class="text-danger">*</span></label>
    <input type="text" name="name[ar]" id="name_ar" class="form-control"
           value="<?php echo e(old('name.ar', $isEdit ? ($form->name['ar'] ?? '') : '')); ?>" required>
  </div>

  
  <div class="col-md-6">
    <label for="name_en" class="form-label"><?php echo e(__('form_name_en')); ?> <span class="text-danger">*</span></label>
    <input type="text" name="name[en]" id="name_en" class="form-control"
           value="<?php echo e(old('name.en', $isEdit ? ($form->name['en'] ?? '') : '')); ?>" required>
  </div>

  
  <div class="col-md-6">
    <label for="description_ar" class="form-label"><?php echo e(__('form_description_ar')); ?></label>
    <textarea name="description[ar]" id="description_ar" class="form-control html-editor" rows="5"><?php echo old('description.ar', $isEdit ? ($form->description['ar'] ?? '') : ''); ?></textarea>
  </div>

  
  <div class="col-md-6">
    <label for="description_en" class="form-label"><?php echo e(__('form_description_en')); ?></label>
    <textarea name="description[en]" id="description_en" class="form-control html-editor" rows="5"><?php echo old('description.en', $isEdit ? ($form->description['en'] ?? '') : ''); ?></textarea>
  </div>

  
  <div class="col-md-6">
    <label for="number" class="form-label"><?php echo e(__('form_number')); ?></label>
    <input type="text" name="number" id="number" class="form-control"
           value="<?php echo e(old('number', $isEdit ? $form->number : '')); ?>">
  </div>

  
  <div class="col-md-6 d-flex align-items-center">
    <div class="form-check form-switch mt-4">
      <input class="form-check-input" type="checkbox" name="active" id="active" value="1"
             <?php echo e(old('active', $isEdit ? $form->active : true) ? 'checked' : ''); ?>>
      <label class="form-check-label" for="active"><?php echo e(__('form_active')); ?></label>
    </div>
  </div>

  
  <div class="col-12">
    <label for="img" class="form-label"><?php echo e(__('form_image')); ?></label>
    <input type="file" name="img" id="img" class="form-control">
    <?php if($isEdit && $form->img): ?>
      <img src="<?php echo e(asset($form->img)); ?>" class="img-thumbnail mt-2 d-block" width="150" alt="Uploaded Image">
    <?php endif; ?>
  </div>
</div>
<?php /**PATH /home/u552947370/domains/affiliatesummitglobal.com/public_html/resources/views/content/forms/_form.blade.php ENDPATH**/ ?>