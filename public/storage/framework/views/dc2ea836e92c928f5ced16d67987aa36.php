<?php $__env->startSection('title', __('BecomeSponsors.title')); ?>

<?php $__env->startSection('content'); ?>
<div class="card shadow-sm">
  <div class="card-header d-flex justify-content-between align-items-center">
    <h4 class="mb-0"><?php echo e(__('BecomeSponsors.title')); ?></h4>

    <div class="d-flex gap-2">
      <a href="<?php echo e(route('dashboard.becomesponsor.create')); ?>" class="btn btn-success">
        <i class="fas fa-plus"></i> <?php echo e(__('BecomeSponsors.add')); ?>

      </a>
      <a href="<?php echo e(route('dashboard.becomesponsor.excel')); ?>" class="btn btn-secondary">
        <i class="fas fa-file-excel"></i> <?php echo e(__('BecomeSponsors.excel_tools')); ?>

      </a>
    </div>
  </div>

  <div class="card-body">
    
    <form method="GET" class="row g-3 mb-4">
      <div class="col-md-3">
        <input type="text" name="name" class="form-control" placeholder="<?php echo e(__('BecomeSponsors.name')); ?>" value="<?php echo e(request('name')); ?>">
      </div>
      <div class="col-md-3">
        <input type="text" name="phone" class="form-control" placeholder="<?php echo e(__('BecomeSponsors.phone')); ?>" value="<?php echo e(request('phone')); ?>">
      </div>
      <div class="col-md-3">
        <input type="date" name="date_from" class="form-control" value="<?php echo e(request('date_from')); ?>">
      </div>
      <div class="col-md-3">
        <input type="date" name="date_to" class="form-control" value="<?php echo e(request('date_to')); ?>">
      </div>
      <div class="col-12 text-end">
        <button type="submit" class="btn btn-primary"><?php echo e(__('BecomeSponsors.search')); ?></button>
      </div>
    </form>

    
    <div class="table-responsive">
      <table class="table table-bordered table-striped align-middle">
        <thead class="table-light">
          <tr>
            <th>#</th>
            <th><?php echo e(__('BecomeSponsors.name')); ?></th>
            <th><?php echo e(__('BecomeSponsors.email')); ?></th>
            <th><?php echo e(__('BecomeSponsors.country_code')); ?></th>
            <th><?php echo e(__('BecomeSponsors.phone')); ?></th>
            <th><?php echo e(__('BecomeSponsors.job')); ?></th>
            <th><?php echo e(__('BecomeSponsors.company_name')); ?></th>
            <th><?php echo e(__('BecomeSponsors.active')); ?></th>
            <th><?php echo e(__('BecomeSponsors.created_at')); ?></th>
            <th><?php echo e(__('BecomeSponsors.actions')); ?></th>
          </tr>
        </thead>
        <tbody>
          <?php $__empty_1 = true; $__currentLoopData = $clients; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $client): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <tr>
              <td><?php echo e($loop->iteration); ?></td>
              <td><?php echo e($client->name); ?></td>
              <td><?php echo e($client->email); ?></td>
              <td><?php echo e($client->country_code); ?></td>
              <td><?php echo e($client->phone); ?></td>
              <td><?php echo e($client->job); ?></td>
              <td><?php echo e($client->company_name); ?></td>
              <td>
                <span class="badge <?php echo e($client->active ? 'bg-success' : 'bg-secondary'); ?>">
                  <?php echo e($client->active ? __('BecomeSponsors.active_yes') : __('BecomeSponsors.active_no')); ?>

                </span>
              </td>
              <td><?php echo e($client->created_at->format('Y-m-d')); ?></td>
              <td>
                <a href="<?php echo e(route('dashboard.becomesponsor.show', $client->id)); ?>" class="btn btn-sm btn-info">
                  <?php echo e(__('BecomeSponsors.show')); ?>

                </a>
              </td>
            </tr>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <tr>
              <td colspan="10" class="text-center"><?php echo e(__('BecomeSponsors.no_data')); ?></td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>

    
    <div class="mt-4">
      <?php if($clients->hasPages()): ?>
        <nav>
          <ul class="pagination justify-content-center">
            
            <?php if($clients->onFirstPage()): ?>
              <li class="page-item disabled"><span class="page-link">‹</span></li>
            <?php else: ?>
              <li class="page-item">
                <a class="page-link" href="<?php echo e($clients->previousPageUrl()); ?>" rel="prev">‹</a>
              </li>
            <?php endif; ?>

            
            <?php $__currentLoopData = $clients->getUrlRange(1, $clients->lastPage()); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $page => $url): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <li class="page-item <?php echo e($clients->currentPage() == $page ? 'active' : ''); ?>">
                <a class="page-link" href="<?php echo e($url); ?>"><?php echo e($page); ?></a>
              </li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

            
            <?php if($clients->hasMorePages()): ?>
              <li class="page-item">
                <a class="page-link" href="<?php echo e($clients->nextPageUrl()); ?>" rel="next">›</a>
              </li>
            <?php else: ?>
              <li class="page-item disabled"><span class="page-link">›</span></li>
            <?php endif; ?>
          </ul>
        </nav>
      <?php endif; ?>
    </div>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.layoutMaster', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/u552947370/domains/affiliatesummitglobal.com/public_html/resources/views/content/becomesponsor/index.blade.php ENDPATH**/ ?>