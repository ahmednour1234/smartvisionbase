<?php $__env->startSection('content'); ?>
<div class="container py-4">
    <h2 class="mb-4 fw-bold text-primary"><?php echo e(__('leads')); ?></h2>

    <form method="GET" class="row g-3 mb-4 align-items-end bg-light p-3 rounded shadow-sm">
        <div class="col-md-3">
            <label class="form-label"><?php echo e(__('date')); ?> (من)</label>
            <input type="date" name="start_date" class="form-control" value="<?php echo e(request('start_date')); ?>">
        </div>
        <div class="col-md-3">
            <label class="form-label"><?php echo e(__('date')); ?> (إلى)</label>
            <input type="date" name="end_date" class="form-control" value="<?php echo e(request('end_date')); ?>">
        </div>
        <div class="col-md-3">
            <label class="form-label"><?php echo e(__('campaign')); ?></label>
            <input type="text" name="campaign" class="form-control" value="<?php echo e(request('campaign')); ?>">
        </div>
        <div class="col-md-3">
            <label class="form-label"><?php echo e(__('email')); ?></label>
            <input type="text" name="email" class="form-control" value="<?php echo e(request('email')); ?>">
        </div>
        <div class="col-12 text-end">
            <button class="btn btn-success"><?php echo e(__('filter')); ?></button>
        </div>
    </form>

    <div class="mb-3 bg-white rounded p-3 shadow-sm">
        <div class="row text-center">
            <div class="col-md-4">
                <strong><?php echo e(__('total_leads')); ?>:</strong> <?php echo e($stats['total_leads']); ?>

            </div>
            <div class="col-md-4">
                <strong><?php echo e(__('today_leads')); ?>:</strong> <?php echo e($stats['today_leads']); ?>

            </div>
            <div class="col-md-4">
                <strong><?php echo e(__('unique_campaigns')); ?>:</strong> <?php echo e($stats['unique_campaigns']); ?>

            </div>
        </div>
    </div>

    <div class="table-responsive bg-white rounded shadow-sm">
        <table class="table table-hover table-bordered align-middle">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th><?php echo e(__('name')); ?></th>
                    <th><?php echo e(__('phone')); ?></th>
                    <th><?php echo e(__('email')); ?></th>
                    <th><?php echo e(__('campaign')); ?></th>
                    <th><?php echo e(__('date')); ?></th>
                    <th class="text-center"><?php echo e(__('view')); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $leads; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lead): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                    <td><?php echo e($lead->id); ?></td>
                    <td><?php echo e($lead->name); ?></td>
                    <td><?php echo e($lead->phone); ?></td>
                    <td><?php echo e($lead->email); ?></td>
                    <td><?php echo e($lead->campaign); ?></td>
                    <td><?php echo e($lead->created_at->format('Y-m-d')); ?></td>
                    <td class="text-center">
                        <a href="<?php echo e(route('dashboard.leads.show', $lead->id)); ?>" class="btn btn-sm btn-info"><?php echo e(__('view')); ?></a>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="7" class="text-center text-muted">No leads found</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div class="mt-3">
        <?php echo e($leads->withQueryString()->links()); ?>

    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.layoutMaster', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/u552947370/domains/affiliatesummitglobal.com/public_html/resources/views/content/leads/index.blade.php ENDPATH**/ ?>