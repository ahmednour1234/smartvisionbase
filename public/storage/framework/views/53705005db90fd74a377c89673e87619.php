<?php $__env->startSection('title', __('clients.excel_tools')); ?>

<?php $__env->startSection('content'); ?>
<div class="card">
  <div class="card-header">
    <h4><?php echo e(__('clients.excel_tools')); ?></h4>
  </div>

  <div class="card-body">
    <ul class="nav nav-tabs mb-4" id="excelTabs" role="tablist">
      <li class="nav-item" role="presentation">
        <button class="nav-link active" id="download-tab" data-bs-toggle="tab" data-bs-target="#download" type="button">
          <?php echo e(__('clients.download_template')); ?>

        </button>
      </li>
      <li class="nav-item" role="presentation">
        <button class="nav-link" id="upload-tab" data-bs-toggle="tab" data-bs-target="#upload" type="button">
          <?php echo e(__('clients.upload_excel')); ?>

        </button>
      </li>
      <li class="nav-item" role="presentation">
        <button class="nav-link" id="export-tab" data-bs-toggle="tab" data-bs-target="#export" type="button">
          <?php echo e(__('clients.export_clients')); ?>

        </button>
      </li>
    </ul>

    <div class="tab-content" id="excelTabsContent">
      
      <div class="tab-pane fade show active" id="download" role="tabpanel">
        <div class="alert alert-warning">
          <strong><?php echo e(__('clients.download_alert')); ?></strong>
        </div>

        <a href="<?php echo e(route('dashboard.clients.export.template')); ?>" class="btn btn-primary">
          <?php echo e(__('clients.download_template_btn')); ?>

        </a>
      </div>

      
      <div class="tab-pane fade" id="upload" role="tabpanel">
        <div class="alert alert-info">
          <?php echo e(__('clients.upload_alert')); ?>

        </div>

        <form action="<?php echo e(route('dashboard.clients.import')); ?>" method="POST" enctype="multipart/form-data" class="mt-3">
          <?php echo csrf_field(); ?>

          
          <div class="mb-3">
            <label class="form-label"><?php echo e(__('clients.select_form')); ?></label>
            <select name="form_id" class="form-select" required>
              <option value=""><?php echo e(__('clients.choose_form')); ?></option>
              <?php $__currentLoopData = $forms; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $form): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($form->id); ?>"><?php echo e($form->number); ?></option>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
          </div>

          
          <div class="mb-3">
            <label class="form-label"><?php echo e(__('clients.choose_excel_file')); ?></label>
            <input type="file" name="file" class="form-control" accept=".xlsx,.csv" required>
          </div>

          <button type="submit" class="btn btn-success"><?php echo e(__('clients.import_btn')); ?></button>
        </form>
      </div>

      
      <div class="tab-pane fade" id="export" role="tabpanel">
        <form action="<?php echo e(route('dashboard.clients.export')); ?>" method="GET" class="mt-3">
          <div class="row g-3">
            <div class="col-md-4">
              <label><?php echo e(__('clients.form')); ?></label>
              <select name="form_id" class="form-select">
                <option value=""><?php echo e(__('clients.all_forms')); ?></option>
                <?php $__currentLoopData = $forms; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $form): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                  <option value="<?php echo e($form->id); ?>"><?php echo e($form->number); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              </select>
            </div>

            <div class="col-md-4">
              <label><?php echo e(__('clients.date_from')); ?></label>
              <input type="date" name="date_from" class="form-control">
            </div>

            <div class="col-md-4">
              <label><?php echo e(__('clients.date_to')); ?></label>
              <input type="date" name="date_to" class="form-control">
            </div>
          </div>

          <div class="text-end mt-3">
            <button type="submit" class="btn btn-info"><?php echo e(__('clients.export_btn')); ?></button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.layoutMaster', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/u552947370/domains/affiliatesummitglobal.com/public_html/resources/views/content/clients/excel.blade.php ENDPATH**/ ?>