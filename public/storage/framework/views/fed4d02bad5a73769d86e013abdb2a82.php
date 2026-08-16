<?php $isEdit = isset($sponsorCategory); ?>
<div class="row g-3">
  <div class="col-md-6">
    <label class="form-label"><?php echo e(__('sponsor_category.name')); ?></label>
    <input type="text" name="name" class="form-control" value="<?php echo e(old('name', $isEdit ? $sponsorCategory->name : '')); ?>" required>
  </div>
  <div class="col-md-6">
    <label class="form-label"><?php echo e(__('sponsor_category.name_en')); ?></label>
    <input type="text" name="name_en" class="form-control" value="<?php echo e(old('name_en', $isEdit ? $sponsorCategory->name_en : '')); ?>" required>
  </div>

  <div class="col-md-6">
    <label class="form-label"><?php echo e(__('sponsor_category.status')); ?></label>
    <select name="active" class="form-select">
      <option value="1" <?php echo e(old('active', $isEdit ? $sponsorCategory->active : 1) == 1 ? 'selected' : ''); ?>><?php echo e(__('general.active')); ?></option>
      <option value="0" <?php echo e(old('active', $isEdit ? $sponsorCategory->active : 1) == 0 ? 'selected' : ''); ?>><?php echo e(__('general.inactive')); ?></option>
    </select>
  </div>
  <div class="col-md-6">
    <label class="form-label"><?php echo e(__('sponsor_category.orders')); ?></label>
    <input type="number" name="orders" class="form-control" value="<?php echo e(old('orders', $isEdit ? $sponsorCategory->orders : '')); ?>" required>
  </div>

  <div class="col-md-6">
    <label class="form-label"><?php echo e(__('sponsor_category.logo')); ?></label>
    <input type="file" name="logo" class="form-control">
    <?php if($isEdit && $sponsorCategory->logo): ?>
      <img src="<?php echo e(asset($sponsorCategory->logo)); ?>" class="img-thumbnail mt-2" width="100">
    <?php endif; ?>
  </div>
</div>
<?php /**PATH /home/u552947370/domains/affiliatesummitglobal.com/public_html/resources/views/content/sponsor_categories/_form.blade.php ENDPATH**/ ?>