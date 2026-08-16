<?php $__env->startSection('title', 'Analytics'); ?>

<?php $__env->startSection('vendor-style'); ?>
    <link rel="stylesheet" href="<?php echo e(asset('assets/vendor/libs/apex-charts/apex-charts.css')); ?>" />
    <link rel="stylesheet" href="<?php echo e(asset('assets/vendor/libs/swiper/swiper.css')); ?>" />
    <link rel="stylesheet" href="<?php echo e(asset('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css')); ?>" />
    <link rel="stylesheet" href="<?php echo e(asset('assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css')); ?>" />
    <link rel="stylesheet" href="<?php echo e(asset('assets/vendor/libs/datatables-checkboxes-jquery/datatables.checkboxes.css')); ?>" />
<?php $__env->stopSection(); ?>

<?php $__env->startSection('page-style'); ?>
    <!-- Page -->
    <link rel="stylesheet" href="<?php echo e(asset('assets/vendor/css/pages/cards-advance.css')); ?>">
<?php $__env->stopSection(); ?>

<?php $__env->startSection('vendor-script'); ?>
    <script src="<?php echo e(asset('assets/vendor/libs/apex-charts/apexcharts.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/vendor/libs/swiper/swiper.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js')); ?>"></script>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('page-script'); ?>
    <script src="<?php echo e(asset('assets/js/dashboards-analytics.js')); ?>"></script>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('page-script'); ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            <?php if($eventDate): ?>
                let eventDate = new Date("<?php echo e(\Carbon\Carbon::parse($eventDate)->format('Y-m-d H:i:s')); ?>")
                .getTime();
                let countdown = setInterval(function() {
                    let now = new Date().getTime();
                    let distance = eventDate - now;

                    if (distance < 0) {
                        clearInterval(countdown);
                        document.getElementById("eventCountdown").innerHTML = "<?php echo e(__('Event started')); ?>";
                        return;
                    }

                    let days = Math.floor(distance / (1000 * 60 * 60 * 24));
                    let hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                    let minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                    let seconds = Math.floor((distance % (1000 * 60)) / 1000);

                    document.getElementById("eventCountdown").innerHTML =
                        days + "d " + hours + "h " + minutes + "m " + seconds + "s";
                }, 1000);
            <?php endif; ?>
        });
    </script>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <div class="row g-4 mb-4">

        <div class="col-sm-6 col-xl-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div class="content-left">
                            <h6><?php echo e(__('Events')); ?></h6>
                            <div class="d-flex align-items-center my-2">
                                <h3 class="mb-0 me-2"><?php echo e($eventsCount); ?></h3>
                            </div>
                            <p class="mb-0"><?php echo e(__('Total')); ?> <?php echo e(__('Events')); ?></p>
                        </div>
                        <div class="avatar">
                            <span class="avatar-initial rounded bg-label-danger">
                                <i class="ti ti-user-plus ti-sm"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div class="content-left">
                            <span><?php echo e(__('Speakers')); ?></span>
                            <div class="d-flex align-items-center my-2">
                                <h3 class="mb-0 me-2"><?php echo e($speackerscount); ?></h3>
                            </div>
                            <p class="mb-0"><?php echo e(__('Total')); ?> <?php echo e(__('Speakers')); ?></p>
                        </div>
                        <div class="avatar">
                            <span class="avatar-initial rounded bg-label-danger">
                                <i class="ti ti-user-plus ti-sm"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div class="content-left">
                            <span><?php echo e(__('Sponsors')); ?></span>
                            <div class="d-flex align-items-center my-2">
                                <h3 class="mb-0 me-2"><?php echo e($sponsorscount); ?></h3>
                            </div>
                            <p class="mb-0"><?php echo e(__('Total')); ?> <?php echo e(__('Sponsors')); ?></p>
                        </div>
                        <div class="avatar">
                            <span class="avatar-initial rounded bg-label-success">
                                <i class="ti ti-user-check ti-sm"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div class="content-left">
                            <span><?php echo e(__('Users')); ?></span>
                            <div class="d-flex align-items-center my-2">
                                <h3 class="mb-0 me-2"><?php echo e($usercount); ?></h3>
                            </div>
                            <p class="mb-0"><?php echo e(__('Total')); ?> <?php echo e(__('Users')); ?></p>
                        </div>
                        <div class="avatar">
                            <span class="avatar-initial rounded bg-label-warning">
                                <i class="ti ti-user-exclamation ti-sm"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row g-4 mb-4">

        <div class="col-sm-6 col-xl-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div class="content-left">
                            <span><?php echo e(__('Regitration Today')); ?></span>
                            <div class="d-flex align-items-center my-2">
                                <h3 class="mb-0 me-2"><?php echo e($number_register_today); ?></h3>
                            </div>
                            <p class="mb-0"><?php echo e(__('Today')); ?> <?php echo e(__('Regitration')); ?></p>
                        </div>
                        <div class="avatar">
                            <span class="avatar-initial rounded bg-label-danger">
                                <i class="ti ti-user-plus ti-sm"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div class="content-left">
                            <span><?php echo e(__('Become Sponsor')); ?></span>
                            <div class="d-flex align-items-center my-2">
                                <h3 class="mb-0 me-2"><?php echo e($number_become_sponsor_today); ?></h3>
                            </div>
                            <p class="mb-0"><?php echo e(__('Become')); ?> <?php echo e(__('Sponsor')); ?></p>
                        </div>
                        <div class="avatar">
                            <span class="avatar-initial rounded bg-label-danger">
                                <i class="ti ti-user-plus ti-sm"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <!-- Earning Reports -->
        <div class="col-lg-6 mb-4">
            <div class="card h-100">
                <div class="card-header pb-0 d-flex justify-content-between mb-lg-n4">
                    <div class="card-title mb-0">
                        <h5 class="mb-0"><?php echo e(__('Event Schedules Today')); ?></h5>
                        <small class="text-muted"><?php echo e(__('Event Schedules Today')); ?></small>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-12 col-md-4 d-flex flex-column align-self-end">
                            <div class="d-flex gap-2 align-items-center mb-2 pb-1 flex-wrap">
                                <h1 class="mb-0"><?php echo e($scheduletoday); ?></h1>
                            </div>
                            <small><?php echo e(__('Event Schedules Today')); ?></small>
                        </div>
                        <div class="col-12 col-md-8">
                            <div id="weeklyEarningReports"></div>
                        </div>
                    </div>
                    <div class="border rounded p-3 mt-4">
                        <div class="row gap-4 gap-sm-0">
                            <div class="col-12 col-sm-4">
                                <div class="d-flex gap-2 align-items-center">
                                    <div class="badge rounded bg-label-primary p-1">
                                        <i class="ti ti-chart-pie-2 ti-sm"></i>
                                    </div>
                                    <h6 class="mb-0"><?php echo e(__('Blogs')); ?></h6>
                                </div>
                                <h4 class="my-2 pt-1"><?php echo e($blogcount); ?></h4>
                                <div class="progress w-75" style="height:4px">
                                    <div class="progress-bar" role="progressbar" style="width: 65%" aria-valuenow="65"
                                        aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </div>
                            <div class="col-12 col-sm-4">
                                <div class="d-flex gap-2 align-items-center">
                                    <div class="badge rounded bg-label-info p-1">
                                        <i class="ti ti-chart-pie-2 ti-sm"></i>
                                    </div>
                                    <h6 class="mb-0"><?php echo e(__('Event Schedules Today')); ?></h6>
                                </div>
                                <h4 class="my-2 pt-1"><?php echo e($scheduletoday); ?></h4>
                                <div class="progress w-75" style="height:4px">
                                    <div class="progress-bar bg-info" role="progressbar" style="width: 50%"
                                        aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </div>
                            <div class="col-12 col-sm-4">
                                <div class="d-flex gap-2 align-items-center">
                                    <div class="badge rounded bg-label-danger p-1">
                                        <i class="ti ti-chart-pie-2 ti-sm"></i>
                                    </div>
                                    <h6 class="mb-0"><?php echo e(__('Schedules for today')); ?></h6>
                                </div>
                                <h4 class="my-2 pt-1"><?php echo e($scheduletoday); ?></h4>
                                <div class="progress w-75" style="height:4px">
                                    <div class="progress-bar bg-danger" role="progressbar" style="width: 65%"
                                        aria-valuenow="65" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--/ Earning Reports -->
        <!-- Support Tracker -->
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between pb-0">
                    <div class="card-title mb-0">
                        <h5 class="mb-0"><?php echo e(__('Days Left for Event')); ?></h5>
                        <small class="text-muted"><?php echo app('translator')->get('Today'); ?></small>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-12 col-sm-4 col-md-12 col-lg-4">
                            <div class="mt-lg-4 mt-lg-2 mb-lg-4 mb-2 pt-1">
                                <h1 class="mb-0"><?php echo e(\Carbon\Carbon::parse($eventDate)->format('Y-m-d H:i')); ?></h1>
                                <div id="eventCountdown" class="text-danger"></div>

                                <p class="mb-0"><?php echo e(__('Days Left for Event')); ?></p>
                            </div>

                        </div>
                        <div class="col-12 col-sm-8 col-md-12 col-lg-8">
                            <div id="supportTracker"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

  <div class="row g-4">
  
  <div class="col-md-6">
    <div class="card shadow-sm">
      <div class="card-header bg-primary text-white">
        <h5 class="mb-0"><?php echo e(__('dashboard.latest_registration')); ?></h5>
      </div>
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-hover mb-0">
            <thead class="table-light">
              <tr>
                <th>#</th>
                <th><?php echo e(__('dashboard.name')); ?></th>
                <th><?php echo e(__('dashboard.phone')); ?></th>
                <th><?php echo e(__('dashboard.actions')); ?></th>
              </tr>
            </thead>
            <tbody>
              <?php $__empty_1 = true; $__currentLoopData = $latest_register; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $client): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                  <td><?php echo e($loop->iteration); ?></td>
                  <td><?php echo e($client->name); ?></td>
                  <td><?php echo e($client->phone); ?></td>
                  <td>
                    <a href="<?php echo e(route('dashboard.clients.show', $client->id)); ?>" class="btn btn-sm btn-outline-info">
                      <?php echo e(__('dashboard.view')); ?>

                    </a>
                  </td>
                </tr>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                  <td colspan="4" class="text-center"><?php echo e(__('dashboard.no_data')); ?></td>
                </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  
  <div class="col-md-6">
    <div class="card shadow-sm">
      <div class="card-header bg-warning text-dark">
        <h5 class="mb-0"><?php echo e(__('dashboard.latest_sponsor_requests')); ?></h5>
      </div>
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-hover mb-0">
            <thead class="table-light">
              <tr>
                <th>#</th>
                <th><?php echo e(__('dashboard.name')); ?></th>
                <th><?php echo e(__('dashboard.phone')); ?></th>
                <th><?php echo e(__('dashboard.actions')); ?></th>
              </tr>
            </thead>
            <tbody>
              <?php $__empty_1 = true; $__currentLoopData = $latest_become_sponsor; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $client): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                  <td><?php echo e($loop->iteration); ?></td>
                  <td><?php echo e($client->name); ?></td>
                  <td><?php echo e($client->phone); ?></td>
                  <td>
                    <a href="<?php echo e(route('dashboard.becomesponsor.show', $client->id)); ?>" class="btn btn-sm btn-outline-info">
                      <?php echo e(__('dashboard.view')); ?>

                    </a>
                  </td>
                </tr>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                  <td colspan="4" class="text-center"><?php echo e(__('dashboard.no_data')); ?></td>
                </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts/layoutMaster', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/u552947370/domains/affiliatesummitglobal.com/public_html/resources/views/content/dashboard/dashboards-analytics.blade.php ENDPATH**/ ?>