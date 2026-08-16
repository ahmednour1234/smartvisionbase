<?php $__env->startSection('title', __('home_sections.edit_title')); ?>

<?php $__env->startSection('content'); ?>
<div class="card">
  <div class="card-header bg-warning text-white">
    <h5 class="mb-0"><?php echo e(__('home_sections.edit_title')); ?></h5>
  </div>
  <div class="card-body">
    <form action="<?php echo e(route('dashboard.home_sections.update', $homeSection->id)); ?>" method="POST" enctype="multipart/form-data">
      <?php echo csrf_field(); ?>
      <?php echo method_field('PUT'); ?>
      <?php echo $__env->make('content.home_sections._form', ['homeSection' => $homeSection], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
      <button type="submit" class="btn btn-primary"><?php echo e(__('home_sections.update')); ?></button>
    </form>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.layoutMaster', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/u552947370/domains/affiliatesummitglobal.com/public_html/resources/views/content/home_sections/edit.blade.php ENDPATH**/ ?>