<?php $__env->startSection('title', __('registrations.details')); ?>

<?php $__env->startSection('content'); ?>
<div class="card shadow-sm border-0">
  <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
    <h5 class="mb-0"><i class="fas fa-user"></i> <?php echo e(__('registrations.details')); ?></h5>
    <a href="<?php echo e(route('dashboard.clients.index')); ?>" class="btn btn-light btn-sm">
      <i class="fas fa-arrow-left"></i> <?php echo e(__('registrations.back')); ?>

    </a>
  </div>

  <div class="card-body py-4">
    <div class="row gy-4">

      
      <div class="col-md-6">
        <label class="text-muted small"><?php echo e(__('registrations.name')); ?></label>
        <div class="fw-semibold"><?php echo e($client->name); ?></div>
      </div>

      
      <div class="col-md-6">
        <label class="text-muted small"><?php echo e(__('registrations.email')); ?></label>
        <div class="fw-semibold"><?php echo e($client->email); ?></div>
      </div>

      
      <div class="col-md-6">
        <label class="text-muted small"><?php echo e(__('registrations.phone')); ?></label>
        <div class="fw-semibold"><?php echo e($client->country_code); ?> <?php echo e($client->phone); ?></div>
      </div>

      
      <div class="col-md-6">
        <label class="text-muted small"><?php echo e(__('registrations.job')); ?></label>
        <div class="fw-semibold"><?php echo e($client->job ?? '-'); ?></div>
      </div>

      
      <div class="col-md-6">
        <label class="text-muted small"><?php echo e(__('registrations.active')); ?></label>
        <div>
          <span class="badge <?php echo e($client->active ? 'bg-success' : 'bg-secondary'); ?>">
            <?php echo e($client->active ? __('registrations.active_yes') : __('registrations.active_no')); ?>

          </span>
        </div>
      </div>

      
      <div class="col-md-6">
        <label class="text-muted small"><?php echo e(__('registrations.form')); ?></label>
        <div class="fw-semibold"><?php echo e($client->form->number ?? '-'); ?></div>
      </div>

      
      <?php if($client->img): ?>
      <div class="col-md-6">
        <label class="text-muted small"><?php echo e(__('registrations.image')); ?></label>
        <div class="mt-2">
          <img src="<?php echo e(asset($client->img)); ?>" class="img-thumbnail border" width="140">
        </div>
      </div>
      <?php endif; ?>

      
      <div class="col-md-6">
        <label class="text-muted small"><?php echo e(__('registrations.created_at')); ?></label>
        <div class="fw-semibold"><?php echo e($client->created_at->format('Y-m-d H:i')); ?></div>
      </div>

    </div>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.layoutMaster', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/u552947370/domains/affiliatesummitglobal.com/public_html/resources/views/content/clients/show.blade.php ENDPATH**/ ?>