

<?php $__env->startSection('title', app()->getLocale() === 'ar' ? $speaker->name_ar : $speaker->name_en); ?>

<?php $__env->startSection('content'); ?>
<?php
  $isAr   = app()->getLocale() === 'ar';
  $t = fn($ar,$en)=> $isAr ? $ar : $en;

  // نحاول تحديد مسار زر إنشاء موعد حسب أسماء الروت عندك
  $createTimeUrl = route_exists('dashboard.speakers.times.create')
      ? route('dashboard.speakers.times.create', $speaker)
      : (route_exists('admin.speakers.times.create')
          ? route('admin.speakers.times.create', $speaker)
          : '#');

  // Helper صغير للتأكد من وجود اسم روت
  function route_exists($name){
      try { return \Illuminate\Support\Facades\Route::has($name); }
      catch(\Throwable $e){ return false; }
  }
?>

<div class="card shadow-sm">
  <div class="card-body">

    
    <ul class="nav nav-tabs" id="speakerTabs" role="tablist">
      <li class="nav-item" role="presentation">
        <a class="nav-link <?php echo e(($activeTab ?? 'other') === 'other' ? 'active' : ''); ?>"
           href="<?php echo e(request()->fullUrlWithQuery(['tab'=>'other'])); ?>" role="tab">
           <?php echo e($t('تفاصيل','Other')); ?>

        </a>
      </li>
      <li class="nav-item" role="presentation">
        <a class="nav-link <?php echo e(($activeTab ?? '') === 'times' ? 'active' : ''); ?>"
           href="<?php echo e(request()->fullUrlWithQuery(['tab'=>'times'])); ?>" role="tab">
           <?php echo e($t('المواعيد','Times')); ?>

        </a>
      </li>
      <li class="nav-item" role="presentation">
        <a class="nav-link <?php echo e(($activeTab ?? '') === 'bookings' ? 'active' : ''); ?>"
           href="<?php echo e(request()->fullUrlWithQuery(['tab'=>'bookings'])); ?>" role="tab">
           <?php echo e($t('الحجوزات','Bookings')); ?>

        </a>
      </li>
    </ul>

    <div class="tab-content pt-4">
      
      <div class="tab-pane fade <?php echo e(($activeTab ?? 'other') === 'other' ? 'show active' : ''); ?>" id="tab-other">
        <div class="row g-4 align-items-center">
          <div class="col-md-4 text-center">
            <?php if($speaker->image): ?>
              <img src="<?php echo e(asset('public/'. $speaker->image)); ?>" alt="" class="img-thumbnail rounded-circle" width="200">
            <?php else: ?>
              <img src="https://via.placeholder.com/200" class="img-thumbnail rounded-circle" alt="No Image">
            <?php endif; ?>
            <h4 class="mt-3"><?php echo e($isAr ? $speaker->name_ar : $speaker->name_en); ?></h4>
            <p class="text-muted"><?php echo e($isAr ? $speaker->title_ar : $speaker->title_en); ?></p>
            <?php if($speaker->linkedin): ?>
              <a href="<?php echo e($speaker->linkedin); ?>" class="btn btn-sm btn-outline-primary" target="_blank">
                <i class="fab fa-linkedin"></i> LinkedIn
              </a>
            <?php endif; ?>
          </div>

          <div class="col-md-8">
            <h5 class="mb-3"><?php echo e(__('speaker.details')); ?></h5>
            <ul class="list-group list-group-flush">
              <li class="list-group-item">
                <strong><?php echo e(__('speaker.name')); ?>:</strong>
                <?php echo e($isAr ? $speaker->name_ar : $speaker->name_en); ?>

              </li>
              <li class="list-group-item">
                <strong><?php echo e(__('speaker.title')); ?>:</strong>
                <?php echo e($isAr ? $speaker->title_ar : $speaker->title_en); ?>

              </li>
              <li class="list-group-item">
                <strong><?php echo e(__('speaker.company')); ?>:</strong>
                <?php echo e($isAr ? $speaker->company_name_ar : $speaker->company_name_en); ?>

              </li>
              <li class="list-group-item">
                <strong><?php echo e(__('speaker.social_links')); ?>:</strong>
                <p class="mb-0"><?php echo nl2br(e($speaker->social_links)); ?></p>
              </li>
            </ul>
            <a href="<?php echo e(route('admin.speakers.index' ,[$type])); ?>" class="btn btn-outline-secondary mt-4">
              <i class="fas fa-arrow-left me-1"></i> <?php echo e(__('general.back')); ?>

            </a>
          </div>
        </div>
      </div>

      
      <div class="tab-pane fade <?php echo e(($activeTab ?? '') === 'times' ? 'show active' : ''); ?>" id="tab-times">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <h5 class="mb-0"><?php echo e($t('مواعيد المتحدث','Speaker Times')); ?></h5>
          <a href="<?php echo e($createTimeUrl); ?>" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i> <?php echo e($t('إضافة موعد','Create Time')); ?>

          </a>
        </div>

        <div class="table-responsive">
          <table class="table table-striped align-middle">
            <thead>
              <tr>
                <th><?php echo e($t('التاريخ','Date')); ?></th>
                <th><?php echo e($t('من','From')); ?></th>
                <th><?php echo e($t('إلى','To')); ?></th>
                <th><?php echo e($t('الحالة','Status')); ?></th>
                <th><?php echo e($t('مفعل؟','Active?')); ?></th>
                <th class="text-end"><?php echo e($t('إجراءات','Actions')); ?></th>
              </tr>
            </thead>
            <tbody>
            <?php $__empty_1 = true; $__currentLoopData = ($times ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tRow): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
              <tr>
                <td><?php echo e(optional($tRow->date)->format('Y-m-d') ?? $tRow->date); ?></td>
                <td><?php echo e($tRow->time_from); ?></td>
                <td><?php echo e($tRow->time_to); ?></td>
                <td>
                  <?php $st = $tRow->status; ?>
                  <?php if($st==='available'): ?>
                    <span class="badge bg-success"><?php echo e($t('متاح','Available')); ?></span>
                  <?php elseif($st==='booked'): ?>
                    <span class="badge bg-primary"><?php echo e($t('محجوز','Booked')); ?></span>
                  <?php else: ?>
                    <span class="badge bg-secondary"><?php echo e($t('غير متاح','Unavailable')); ?></span>
                  <?php endif; ?>
                </td>
                <td><?php echo $tRow->active ? '<span class="badge bg-success">'.$t('نعم','Yes').'</span>' : '<span class="badge bg-danger">'.$t('لا','No').'</span>'; ?></td>
                <td class="text-end">
                  <?php
                    $editUrl   = route_exists('dashboard.speakers.times.edit') ? route('dashboard.speakers.times.edit',$tRow) : (route_exists('admin.speakers.times.edit') ? route('admin.speakers.times.edit',$tRow) : '#');
                    $toggleUrl = route_exists('dashboard.speakers.times.toggle') ? route('dashboard.speakers.times.toggle',$tRow) : (route_exists('admin.speakers.times.toggle') ? route('admin.speakers.times.toggle',$tRow) : '#');
                    $delUrl    = route_exists('dashboard.speakers.times.destroy') ? route('dashboard.speakers.times.destroy',$tRow) : (route_exists('admin.speakers.times.destroy') ? route('admin.speakers.times.destroy',$tRow) : '#');
                  ?>

                  <a href="<?php echo e($editUrl); ?>" class="btn btn-sm btn-outline-primary"><?php echo e($t('تعديل','Edit')); ?></a>

                  <?php if($toggleUrl !== '#'): ?>
                  <form action="<?php echo e($toggleUrl); ?>" method="post" class="d-inline">
                    <?php echo csrf_field(); ?> <?php echo method_field('PATCH'); ?>
                    <button class="btn btn-sm btn-outline-warning">
                      <?php echo e($tRow->active ? $t('تعطيل','Disable') : $t('تفعيل','Enable')); ?>

                    </button>
                  </form>
                  <?php endif; ?>

                  <?php if($delUrl !== '#'): ?>
                  <form action="<?php echo e($delUrl); ?>" method="post" class="d-inline" onsubmit="return confirm('<?php echo e($t('حذف الموعد؟','Delete this time?')); ?>');">
                    <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                    <button class="btn btn-sm btn-outline-danger"><?php echo e($t('حذف','Delete')); ?></button>
                  </form>
                  <?php endif; ?>
                </td>
              </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
              <tr><td colspan="6" class="text-center text-muted"><?php echo e($t('لا توجد مواعيد','No times yet')); ?></td></tr>
            <?php endif; ?>
            </tbody>
          </table>
        </div>

        
        <?php if(method_exists(($times ?? null), 'links')): ?>
          <div class="mt-2">
            <?php echo e($times->appends(['tab'=>'times'])->links()); ?>

          </div>
        <?php endif; ?>
      </div>

      
      <div class="tab-pane fade <?php echo e(($activeTab ?? '') === 'bookings' ? 'show active' : ''); ?>" id="tab-bookings">
        <h5 class="mb-3"><?php echo e($t('حجوزات المتحدث','Speaker Bookings')); ?></h5>

        <div class="table-responsive">
          <table class="table table-hover align-middle">
            <thead>
              <tr>
                <th><?php echo e($t('العميل','Client')); ?></th>
                <th><?php echo e($t('من','From')); ?></th>
                <th><?php echo e($t('إلى','To')); ?></th>
                <th><?php echo e($t('الحالة','Status')); ?></th>
                <th><?php echo e($t('فتحة زمنية','Slot')); ?></th>
              </tr>
            </thead>
            <tbody>
            <?php $__empty_1 = true; $__currentLoopData = ($bookings ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $bk): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
              <tr>
                <td><?php echo e(optional($bk->client)->name ?? ('#'.$bk->client_id)); ?></td>
                <td><?php echo e(optional($bk->time_from)->format('Y-m-d H:i') ?? \Illuminate\Support\Str::limit($bk->time_from,16,'')); ?></td>
                <td><?php echo e(optional($bk->time_to)->format('Y-m-d H:i')   ?? \Illuminate\Support\Str::limit($bk->time_to,16,'')); ?></td>
                <td>
                  <?php $bs = $bk->status; ?>
                  <?php if($bs==='confirmed'): ?>
                    <span class="badge bg-success"><?php echo e($t('مؤكد','Confirmed')); ?></span>
                  <?php elseif($bs==='pending'): ?>
                    <span class="badge bg-warning text-dark"><?php echo e($t('معلّق','Pending')); ?></span>
                  <?php else: ?>
                    <span class="badge bg-secondary"><?php echo e($t('ملغي','Cancelled')); ?></span>
                  <?php endif; ?>
                </td>
                <td><?php echo e($bk->speaker_time_id ? '#'.$bk->speaker_time_id : $t('لا يوجد','—')); ?></td>
              </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
              <tr><td colspan="5" class="text-center text-muted"><?php echo e($t('لا توجد حجوزات','No bookings')); ?></td></tr>
            <?php endif; ?>
            </tbody>
          </table>
        </div>

        
        <?php if(method_exists(($bookings ?? null), 'links')): ?>
          <div class="mt-2">
            <?php echo e($bookings->appends(['tab'=>'bookings'])->links()); ?>

          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.layoutMaster', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/u552947370/domains/affiliatesummitglobal.com/public_html/resources/views/content/speakers/show.blade.php ENDPATH**/ ?>