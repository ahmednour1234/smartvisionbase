<?php $__env->startSection('title', __('sponsor.details')); ?>

<?php $__env->startSection('content'); ?>
<div class="card">
  <div class="card-header">
    <h5 class="mb-0"><?php echo e(__('sponsor.details')); ?></h5>
  </div>
  <div class="card-body row g-4">

    <div class="col-md-3">
      <label class="form-label"><?php echo e(__('sponsor.image')); ?></label><br>
      <?php if($sponsor->image): ?>
        <img src="<?php echo e(asset($sponsor->image)); ?>" class="img-thumbnail" width="150">
      <?php else: ?>
        <span class="text-muted">-</span>
      <?php endif; ?>
    </div>

    <div class="col-md-9 row g-3">
      <div class="col-md-6">
        <label class="form-label"><?php echo e(__('sponsor.name_ar')); ?></label>
        <div class="form-control"><?php echo e($sponsor->name_ar); ?></div>
      </div>

      <div class="col-md-6">
        <label class="form-label"><?php echo e(__('sponsor.name_en')); ?></label>
        <div class="form-control"><?php echo e($sponsor->name_en); ?></div>
      </div>

      <div class="col-md-6">
        <label class="form-label"><?php echo e(__('sponsor.title_ar')); ?></label>
        <div class="form-control"><?php echo e($sponsor->title_ar); ?></div>
      </div>

      <div class="col-md-6">
        <label class="form-label"><?php echo e(__('sponsor.title_en')); ?></label>
        <div class="form-control"><?php echo e($sponsor->title_en); ?></div>
      </div>

      <div class="col-md-6">
        <label class="form-label"><?php echo e(__('sponsor.company_name_ar')); ?></label>
        <div class="form-control"><?php echo e($sponsor->company_name_ar); ?></div>
      </div>

      <div class="col-md-6">
        <label class="form-label"><?php echo e(__('sponsor.company_name_en')); ?></label>
        <div class="form-control"><?php echo e($sponsor->company_name_en); ?></div>
      </div>

      <div class="col-md-6">
        <label class="form-label"><?php echo e(__('sponsor.phone')); ?></label>
        <div class="form-control"><?php echo e($sponsor->phone); ?></div>
      </div>

      <div class="col-md-6">
        <label class="form-label"><?php echo e(__('sponsor.category')); ?></label>
        <div class="form-control">
          <?php echo e(app()->getLocale() == 'ar' ? $sponsor->category->name ?? '-' : $sponsor->category->name_en ?? '-'); ?>

        </div>
      </div>

      <div class="col-md-6">
        <label class="form-label"><?php echo e(__('sponsor.status')); ?></label>
        <div class="form-control">
          <?php if($sponsor->active): ?>
            <span class="badge bg-success"><?php echo e(__('sponsor.active')); ?></span>
          <?php else: ?>
            <span class="badge bg-danger"><?php echo e(__('sponsor.inactive')); ?></span>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.layoutMaster', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/u552947370/domains/affiliatesummitglobal.com/public_html/resources/views/content/sponsors/show.blade.php ENDPATH**/ ?>