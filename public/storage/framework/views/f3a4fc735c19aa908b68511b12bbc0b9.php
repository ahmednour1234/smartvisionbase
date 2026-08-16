<?php $__env->startSection('title', __('form_title')); ?>

<?php $__env->startSection('content'); ?>
<div class="card shadow-sm">
  <div class="card-header bg-primary text-white">
    <h5 class="mb-0"><?php echo e(__('form_settings')); ?></h5>
  </div>

  <div class="card-body">
    <?php if(session('success')): ?>
      <div class="alert alert-success"><?php echo e(session('success')); ?></div>
    <?php endif; ?>

    <form action="<?php echo e(route('admin.forms.store')); ?>" method="POST" enctype="multipart/form-data">
      <?php echo csrf_field(); ?>
      <?php echo $__env->make('content.forms._form', ['form' => $form], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

      <div class="mt-4 text-center">
        <button type="submit" class="btn btn-success px-4">
          <i class="bi bi-save me-1"></i> <?php echo e(__('save_changes')); ?>

        </button>
      </div>
    </form>
  </div>
</div>
<?php $__env->stopSection(); ?>
  <script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      document.querySelectorAll('.html-editor').forEach((el) => {
        ClassicEditor.create(el).catch(error => console.error(error));
      });
    });
  </script>

<?php echo $__env->make('layouts.layoutMaster', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/u552947370/domains/affiliatesummitglobal.com/public_html/resources/views/content/forms/index.blade.php ENDPATH**/ ?>