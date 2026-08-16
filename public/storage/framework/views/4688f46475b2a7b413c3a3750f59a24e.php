
<?php
  $isEdit = isset($sponsor);
?>

<div class="row g-3">
  <div class="col-md-6">
    <label class="form-label"><?php echo e(__('sponsor.name_ar')); ?></label>
    <input type="text" name="name_ar" class="form-control" value="<?php echo e(old('name_ar', $isEdit ? $sponsor->name_ar : '')); ?>" required>
  </div>
  <div class="col-md-6">
    <label class="form-label"><?php echo e(__('sponsor.name_en')); ?></label>
    <input type="text" name="name_en" class="form-control" value="<?php echo e(old('name_en', $isEdit ? $sponsor->name_en : '')); ?>" required>
  </div>

  <div class="col-md-6">
    <label class="form-label"><?php echo e(__('sponsor.title_ar')); ?></label>
    <input type="text" name="title_ar" class="form-control" value="<?php echo e(old('title_ar', $isEdit ? $sponsor->title_ar : '')); ?>">
  </div>
  <div class="col-md-6">
    <label class="form-label"><?php echo e(__('sponsor.title_en')); ?></label>
    <input type="text" name="title_en" class="form-control" value="<?php echo e(old('title_en', $isEdit ? $sponsor->title_en : '')); ?>">
  </div>

  <div class="col-md-6">
    <label class="form-label"><?php echo e(__('sponsor.company_name_ar')); ?></label>
    <input type="text" name="company_name_ar" class="form-control" value="<?php echo e(old('company_name_ar', $isEdit ? $sponsor->company_name_ar : '')); ?>">
  </div>
  <div class="col-md-6">
    <label class="form-label"><?php echo e(__('sponsor.company_name_en')); ?></label>
    <input type="text" name="company_name_en" class="form-control" value="<?php echo e(old('company_name_en', $isEdit ? $sponsor->company_name_en : '')); ?>">
  </div>

  <div class="col-md-6">
    <label class="form-label"><?php echo e(__('sponsor.phone')); ?></label>
    <input type="text" name="phone" class="form-control" value="<?php echo e(old('phone', $isEdit ? $sponsor->phone : '')); ?>">
  </div>
  <div class="col-md-6">
    <label class="form-label"><?php echo e(__('sponsor.link_profile')); ?></label>
    <input type="text" name="link_profile" class="form-control" value="<?php echo e(old('link_profile', $isEdit ? $sponsor->link_profile : '')); ?>">
  </div>
  <div class="col-md-6">
    <label class="form-label"><?php echo e(__('sponsor.category')); ?></label>
    <select name="category_sponsor_id" class="form-select" required>
      <option value="">-- <?php echo e(__('sponsor.category')); ?> --</option>
      <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <option value="<?php echo e($cat->id); ?>" <?php echo e(old('category_sponsor_id', $isEdit ? $sponsor->category_sponsor_id : '') == $cat->id ? 'selected' : ''); ?>>
          <?php echo e(app()->getLocale() == 'ar' ? $cat->name : $cat->name_en); ?>

        </option>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </select>
  </div>
   <div class="col-md-6">
    <label class="form-label"><?php echo e(__('sponsor.orders')); ?></label>
    <input type="number" name="orders" class="form-control" value="<?php echo e(old('orders', $isEdit ? $sponsor->orders : '')); ?>" required>
  </div>
     <div class="col-md-6">
    <label class="form-label"><?php echo e(__('sponsor.link_profile')); ?></label>
    <input type="url" name="link_profile" class="form-control" value="<?php echo e(old('link_profile', $isEdit ? $sponsor->link_profile : '')); ?>" >
  </div>

  <div class="col-md-6">
    <label class="form-label"><?php echo e(__('sponsor.image')); ?></label>
    <input type="file" name="image" class="form-control">
    <?php if($isEdit && $sponsor->image): ?>
      <img src="<?php echo e(asset($sponsor->image)); ?>" alt="image" class="mt-2" width="80">
    <?php endif; ?>
  </div>
    <div class="col-md-6">
    <label class="form-label"><?php echo e(__('sponsor.email')); ?></label>
    <input type="email" name="email" class="form-control" value="<?php echo e(old('email', $isEdit ? $sponsor->email : '')); ?>" required>
  </div>
  <div class="col-md-6">
    <label class="form-label"><?php echo e(__('sponsor.password')); ?></label>
    <input type="password" name="password" class="form-control" value="<?php echo e(old('password', $isEdit ? $sponsor->password : '')); ?>" >
  </div>


  <div class="col-md-6">
    <label class="form-label"><?php echo e(__('sponsor.status')); ?></label>
    <select name="active" class="form-select">
      <option value="1" <?php echo e(old('active', $isEdit ? $sponsor->active : 1) == 1 ? 'selected' : ''); ?>><?php echo e(__('sponsor.active')); ?></option>
      <option value="0" <?php echo e(old('active', $isEdit ? $sponsor->active : 1) == 0 ? 'selected' : ''); ?>><?php echo e(__('sponsor.inactive')); ?></option>
    </select>
  </div>
</div>
<?php /**PATH /home/u552947370/domains/affiliatesummitglobal.com/public_html/resources/views/content/sponsors/_form.blade.php ENDPATH**/ ?>