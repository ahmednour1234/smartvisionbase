
<?php $__env->startSection('title','Create Booking'); ?>
<?php $__env->startSection('content'); ?>
<?php if($errors->any()): ?> <div class="alert alert-danger"><?php echo e($errors->first()); ?></div> <?php endif; ?>
<div class="card shadow-sm">
  <div class="card-body">
    <form method="get" class="row g-2 mb-3">
      <div class="col-md-6">
        <label class="form-label">Speaker (type=1)</label>
        <select name="speaker_id" class="form-select" onchange="this.form.submit()">
          <option value="">Choose…</option>
          <?php $__currentLoopData = $speakers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($sp->id); ?>" <?php if(request('speaker_id')==$sp->id): echo 'selected'; endif; ?>><?php echo e(app()->getLocale()==='ar'?($sp->name_ar??$sp->id):($sp->name_en??$sp->id)); ?></option>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
      </div>
    </form>

    <form method="post" action="<?php echo e(route('dashboard.bookings.store')); ?>">
      <?php echo csrf_field(); ?>
      <?php echo $__env->make('content.bookings._form', ['booking'=>null], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
      <div class="mt-3 d-flex gap-2">
        <button class="btn btn-primary"><i class="fas fa-save me-1"></i> Save</button>
        <a href="<?php echo e(route('dashboard.bookings.index')); ?>" class="btn btn-light">Back</a>
      </div>
    </form>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.layoutMaster', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/u552947370/domains/affiliatesummitglobal.com/public_html/resources/views/content/bookings/create.blade.php ENDPATH**/ ?>