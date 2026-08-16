<?php $__env->startSection('content'); ?>
<div class="card">
  <div class="card-header">
    <h5 class="mb-0"><?php echo e(__('sponsor_category.edit')); ?></h5>
  </div>
  <div class="card-body">
    <form action="<?php echo e(route('admin.sponsor_categories.update', $sponsorCategory->id)); ?>" method="POST" enctype="multipart/form-data">
      <?php echo csrf_field(); ?>
      <?php echo method_field('PUT'); ?>
      <?php echo $__env->make('content.sponsor_categories._form', ['sponsorCategory' => $sponsorCategory], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
      <button type="submit" class="btn btn-primary mt-3">
        <i class="fas fa-save me-1"></i> <?php echo e(__('general.update')); ?>

      </button>
    </form>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.layoutMaster', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/u552947370/domains/affiliatesummitglobal.com/public_html/resources/views/content/sponsor_categories/edit.blade.php ENDPATH**/ ?>