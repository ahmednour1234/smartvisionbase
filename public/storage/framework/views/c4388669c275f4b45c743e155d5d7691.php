<?php $__env->startSection('title', __('blog.create')); ?>

<?php $__env->startSection('content'); ?>
<div class="card">
  <div class="card-header bg-primary text-white">
    <h5 class="mb-0"><i class="bi bi-plus-circle me-1"></i> <?php echo e(__('blog.create')); ?></h5>
  </div>
  <div class="card-body">
    <form action="<?php echo e(route('dashboard.blogs.store')); ?>" method="POST" enctype="multipart/form-data">
      <?php echo csrf_field(); ?>
      <?php echo $__env->make('content.blogs._form', ['button' => __('general.save')], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    </form>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.layoutMaster', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/u552947370/domains/affiliatesummitglobal.com/public_html/resources/views/content/blogs/create.blade.php ENDPATH**/ ?>