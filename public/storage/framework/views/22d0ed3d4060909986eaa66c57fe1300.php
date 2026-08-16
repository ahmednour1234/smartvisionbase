


<?php $__env->startSection('title', $pageTitle ?? 'Leads'); ?>

<?php $__env->startSection('content'); ?>
<?php
  use Illuminate\Support\Facades\Schema;

  // Build users (agents) list for selects if not provided by the controller
  $usersForFilters = $users
      ?? \App\Models\User::query()
            ->when(Schema::hasColumn('users','type'), fn($q)=>$q->where('type','callcenter'))
            ->orderBy('name')
            ->select('id','name','email')
            ->get();

  // statuses (fallback if controller didn't pass $statuses)
  $statuses = $statuses ?? ['new','follow_up','accept','reject','lost'];

  // Keep filter values
  $filters = $filters ?? request()->only(['q','status','assigned_user_id','assigned_by_id','from','to']);

  // Whether we can show "Assigned By" column
  $showAssignedBy = Schema::hasColumn('leads','assigned_by_id');
?>

<div class="card">
  <div class="card-header d-flex justify-content-between align-items-center">
    <h5 class="mb-0"><?php echo e($pageTitle ?? 'Leads'); ?></h5>

    <div class="d-flex gap-2">
      <a href="<?php echo e(route('dashboard.leads.export', request()->query())); ?>" class="btn btn-outline-secondary btn-sm">
        <i class="ti ti-table-export me-1"></i> Export
      </a>
      <a href="<?php echo e(request()->url()); ?>" class="btn btn-outline-dark btn-sm">
        <i class="ti ti-rotate-2 me-1"></i> Reset
      </a>
    </div>
  </div>

  <div class="card-body">
    
    <form method="get" class="row g-2 mb-3">
      <div class="col-lg-4">
        <input type="text" name="q" value="<?php echo e($filters['q'] ?? ''); ?>" class="form-control" placeholder="Search name / phone / email / job">
      </div>



      <div class="col-lg-3">
        <select name="assigned_user_id" class="form-select">
          <option value="">Assigned to (agent): Any</option>
          <?php $__currentLoopData = $usersForFilters; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $u): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($u->id); ?>" <?php if(($filters['assigned_user_id'] ?? '')==$u->id): echo 'selected'; endif; ?>>
              <?php echo e($u->name); ?> <?php echo e($u->email ? "($u->email)" : ''); ?>

            </option>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
      </div>


      <div class="col-lg-2">
        <input type="date" name="from" value="<?php echo e($filters['from'] ?? ''); ?>" class="form-control" placeholder="From">
      </div>
      <div class="col-lg-2">
        <input type="date" name="to" value="<?php echo e($filters['to'] ?? ''); ?>" class="form-control" placeholder="To">
      </div>

      <div class="col-lg-2 d-grid">
        <button class="btn btn-primary"><i class="ti ti-filter me-1"></i> Filter</button>
      </div>
    </form>

    
    <div class="table-responsive">
      <table class="table align-middle">
        <thead>
          <tr>
            <th>#</th>
            <th>Lead</th>
            <th>Email</th>
            <th>Phone</th>
            <th>Assigned To</th>
            <?php if($showAssignedBy): ?>
              <th>Assigned By</th>
              <th>Assigned At</th>
            <?php endif; ?>
            <th>Status</th>
            <th>Created</th>
            <th style="width:120px;">Actions</th>
          </tr>
        </thead>
        <tbody>
        <?php $__empty_1 = true; $__currentLoopData = $leads; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $lead): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
          <tr class="<?php echo \Illuminate\Support\Arr::toCssClasses(['table-danger' => in_array($lead->status,['new','follow_up']) && $lead->next_call_at && \Carbon\Carbon::parse($lead->next_call_at)->lt(now())]); ?>">
            <td><?php echo e($leads->firstItem() + $i); ?></td>
            <td>
              <div class="fw-semibold"><?php echo e($lead->name); ?></div>
              <div class="text-muted small"><?php echo e($lead->job ?? '—'); ?></div>
            </td>
            <td><a href="mailto:<?php echo e($lead->email); ?>"><?php echo e($lead->email ?? '—'); ?></a></td>
            <td><a href="tel:<?php echo e($lead->phone); ?>"><?php echo e($lead->phone ?? '—'); ?></a></td>
            <td><?php echo e(optional($lead->assignedUser)->name ?? '—'); ?></td>

            <?php if($showAssignedBy): ?>
              <td><?php echo e(optional($lead->assignedBy ?? null)->name ?? '—'); ?></td>
              <td><?php echo e($lead->assigned_at ? \Carbon\Carbon::parse($lead->assigned_at)->format('Y-m-d H:i') : '—'); ?></td>
            <?php endif; ?>

            <td>
              <?php switch($lead->status):
                case ('accept'): ?>    <span class="badge bg-success">Accepted</span> <?php break; ?>
                <?php case ('follow_up'): ?> <span class="badge bg-info text-dark">Follow Up</span> <?php break; ?>
                <?php case ('new'): ?>       <span class="badge bg-secondary">New</span> <?php break; ?>
                <?php case ('reject'): ?>    <span class="badge bg-danger">Rejected</span> <?php break; ?>
                <?php case ('lost'): ?>      <span class="badge bg-dark">Lost</span> <?php break; ?>
                <?php default: ?>           <span class="badge bg-light text-dark"><?php echo e($lead->status); ?></span>
              <?php endswitch; ?>
            </td>

            <td><?php echo e($lead->created_at?->format('Y-m-d H:i')); ?></td>

            <td>
              <div class="btn-group btn-group-sm">
                <a href="<?php echo e(route('dashboard.leads.show', $lead)); ?>" class="btn btn-outline-primary">View</a>
                
                <?php if(!$lead->status || !in_array($lead->status,['accept','reject'])): ?>
                  <a href="<?php echo e(route('dashboard.leads.status', $lead)); ?>"
                     onclick="event.preventDefault(); document.getElementById('toFollowUp-<?php echo e($lead->id); ?>').submit();"
                     class="btn btn-outline-secondary" title="Mark Follow Up">F/U</a>
                  <form id="toFollowUp-<?php echo e($lead->id); ?>" action="<?php echo e(route('dashboard.leads.status', $lead)); ?>" method="post" class="d-none">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" name="status" value="follow_up">
                  </form>
                <?php endif; ?>
              </div>
            </td>
          </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
          <tr>
            <td colspan="<?php echo e(10 - ($showAssignedBy ? 0 : 2)); ?>" class="text-center text-muted">No leads found.</td>
          </tr>
        <?php endif; ?>
        </tbody>
      </table>
    </div>

    <div class="mt-3">
      <?php echo e($leads->appends(request()->query())->links()); ?>

    </div>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.layoutMaster', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/u552947370/domains/affiliatesummitglobal.com/public_html/resources/views/content/customer/accepted.blade.php ENDPATH**/ ?>