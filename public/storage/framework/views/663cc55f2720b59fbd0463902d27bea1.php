

<?php $__env->startSection('title', __('Registrations.title')); ?>

<?php $__env->startSection('content'); ?>
<div class="card shadow-sm">
  <div class="card-header d-flex justify-content-between align-items-center">
    <h4 class="mb-0"><?php echo e(__('Registrations.title')); ?></h4>

    <div class="d-flex gap-2">
      <a href="<?php echo e(route('dashboard.clients.create')); ?>" class="btn btn-success">
        <i class="fas fa-plus"></i> <?php echo e(__('Registrations.add')); ?>

      </a>
      <a href="<?php echo e(route('dashboard.clients.excel')); ?>" class="btn btn-secondary">
        <i class="fas fa-file-excel"></i> <?php echo e(__('Registrations.excel_tools')); ?>

      </a>
    </div>
  </div>

  <div class="card-body">

    
    <?php
      // variables passed from controller
      // $sections, $selectedSectionKey
      $tabs = [
        null   => 'All',
        'zone1'=> 'Zone 1',
        'zone2'=> 'Zone 2',
        'zone3'=> 'Zone 3',
      ];
      $currentParams = request()->except('page'); // نحتفظ بكل الفلاتر
    ?>

    <ul class="nav nav-pills mb-3">
      <?php $__currentLoopData = $tabs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php
          $isActive = ($key === null && empty($selectedSectionKey)) || ($key !== null && $selectedSectionKey === $key);
          $url = request()->fullUrlWithQuery(array_merge($currentParams, ['section' => $key, 'page' => 1]));
        ?>
        <li class="nav-item">
          <a class="nav-link <?php echo e($isActive ? 'active' : ''); ?>" href="<?php echo e($url); ?>">
            <?php echo e($label); ?>

          </a>
        </li>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </ul>

    
    <form method="GET" class="row g-3 mb-4">
        
        <input type="hidden" name="section" value="<?php echo e(request('section')); ?>"/>

        <div class="col-md-3">
            <input type="text" name="name" class="form-control" placeholder="<?php echo e(__('Registrations.name')); ?>" value="<?php echo e(request('name')); ?>">
        </div>
        <div class="col-md-3">
            <input type="text" name="phone" class="form-control" placeholder="<?php echo e(__('Registrations.phone')); ?>" value="<?php echo e(request('phone')); ?>">
        </div>
        <div class="col-md-3">
            <input type="date" name="date_from" class="form-control" value="<?php echo e(request('date_from')); ?>">
        </div>
        <div class="col-md-3">
            <input type="date" name="date_to" class="form-control" value="<?php echo e(request('date_to')); ?>">
        </div>

        <div class="col-md-3">
            <select name="form_id" class="form-select">
                <option value=""><?php echo e(__('Registrations.all_forms')); ?></option>
                <?php $__currentLoopData = $forms; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $form): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($form->id); ?>" <?php echo e(request('form_id') == $form->id ? 'selected' : ''); ?>>
                        <?php echo e($form->number); ?>

                    </option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>

        
        <div class="col-md-3">
            <select name="category" id="category" class="form-select">
                <option value=""><?php echo e(__('Registrations.all_categories')); ?></option>
                <?php $__currentLoopData = $availableCategories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($cat); ?>" <?php echo e(request('category') === $cat ? 'selected' : ''); ?>>
                        <?php echo e($cat); ?>

                    </option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>

        <div class="col-12 text-end">
            <button type="submit" class="btn btn-primary"><?php echo e(__('Registrations.search')); ?></button>
        </div>
    </form>

    
    <div class="table-responsive">
      <table class="table table-bordered table-striped align-middle">
        <thead class="table-light">
          <tr>
            <th>#</th>
            <th><?php echo e(__('Registrations.name')); ?></th>
            <th><?php echo e(__('Registrations.email')); ?></th>
            <th><?php echo e(__('Registrations.country_code')); ?></th>
            <th><?php echo e(__('Registrations.phone')); ?></th>
            <th><?php echo e(__('Registrations.job')); ?></th>
            <th>Experience</th>
            <th>Section</th>
            <th>Category</th>
            <th>Status</th>
            <th><?php echo e(__('Registrations.created_at')); ?></th>
            <th><?php echo e(__('Registrations.actions')); ?></th>
          </tr>
        </thead>
        <tbody>
          <?php $__empty_1 = true; $__currentLoopData = $clients; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $client): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <tr id="row-<?php echo e($client->id); ?>">
              <td><?php echo e($loop->iteration + ($clients->currentPage()-1)*$clients->perPage()); ?></td>
              <td><?php echo e($client->name); ?></td>
              <td><?php echo e($client->email); ?></td>
              <td><?php echo e($client->country_code); ?></td>
              <td><?php echo e($client->phone); ?></td>
              <td><?php echo e($client->job); ?></td>
              <td>
                <span class="badge <?php echo e($client->do_you_have_experince ? 'bg-success' : 'bg-danger'); ?>">
                  <?php echo e($client->do_you_have_experince ? 'Yes' : 'No'); ?>

                </span>
              </td>
              <td><?php echo e($client->section ?? '-'); ?></td>
              <td><?php echo e($client->category ?? '-'); ?></td>
              <td>
                <span class="badge <?php echo e($client->status == 'complete' ? 'bg-success' : 'bg-warning'); ?>">
                  <?php echo e(ucfirst($client->status)); ?>

                </span>
              </td>
              <td><?php echo e($client->created_at->format('Y-m-d')); ?></td>
              <td>
                <div class="mb-2">
                  <a href="<?php echo e(route('dashboard.clients.show', $client->id)); ?>" class="btn btn-sm btn-info w-100">
                    Show
                  </a>
                </div>
                <div>
                  <button class="btn btn-sm btn-outline-primary w-100 send-code-btn" data-id="<?php echo e($client->id); ?>">
                    Send Code
                  </button>
                </div>
              </td>
            </tr>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <tr>
              <td colspan="12" class="text-center">No data found</td>
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

            
            <?php
              $total = $clients->lastPage();
              $current = $clients->currentPage();
              $range = 1;
              $showPages = [];

              for ($i = 1; $i <= 2 && $i <= $total; $i++) $showPages[] = $i;
              for ($i = $current - $range; $i <= $current + $range; $i++) if ($i > 2 && $i < $total - 1) $showPages[] = $i;
              for ($i = $total - 1; $i <= $total; $i++) if ($i > 2) $showPages[] = $i;

              $showPages = array_values(array_unique($showPages));
              sort($showPages);
              $last = 0;
            ?>

            <?php $__currentLoopData = $showPages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $page): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <?php if($last + 1 < $page): ?>
                <li class="page-item disabled"><span class="page-link">...</span></li>
              <?php endif; ?>

              <li class="page-item <?php echo e($page == $current ? 'active' : ''); ?>">
                <a class="page-link" href="<?php echo e($clients->url($page)); ?>"><?php echo e($page); ?></a>
              </li>
              <?php $last = $page; ?>
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


<script>
  document.querySelectorAll('.send-code-btn').forEach(button => {
    button.addEventListener('click', function () {
      const clientId = this.dataset.id;
      this.disabled = true;
      this.innerText = 'Sending...';

      fetch(`/api/dashboard/clients/${clientId}/send-code`, {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>',
          'Accept': 'application/json',
          'Content-Type': 'application/json'
        }
      })
      .then(res => res.json())
      .then(data => {
        if (data.success) {
          alert('Verification code sent successfully!');
        } else {
          alert('Failed to send code.');
        }
      })
      .catch(() => alert('Error sending code.'))
      .finally(() => {
        this.disabled = false;
        this.innerText = 'Send Code';
      });
    });
  });
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.layoutMaster', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/u552947370/domains/affiliatesummitglobal.com/public_html/resources/views/content/clients/index.blade.php ENDPATH**/ ?>