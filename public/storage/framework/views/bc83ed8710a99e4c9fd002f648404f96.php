

<?php $__env->startSection('title', (app()->getLocale()==='ar' ? 'جدول المتحدث: ' : 'Speaker Schedule: ') . (app()->getLocale()==='ar' ? ($speaker->name_ar ?? $speaker->id) : ($speaker->name_en ?? $speaker->id))); ?>

<?php $__env->startSection('content'); ?>
<?php
  $isAr = app()->getLocale()==='ar';
  $t = fn($ar,$en)=> $isAr ? $ar : $en;

  // قراءة التبويب النشط من الـ query (times | bookings)
  $activeTab = request('tab', 'times');

  // Helpers صغيرة
  function route_exists($name){
      try { return \Illuminate\Support\Facades\Route::has($name); }
      catch(\Throwable $e){ return false; }
  }

  $createTimeUrl = route_exists('dashboard.speakers.times.create')
      ? route('dashboard.speakers.times.create', $speaker)
      : (route_exists('admin.speakers.times.create') ? route('admin.speakers.times.create', $speaker) : '#');

  $editTime = fn($time)=>
      route_exists('dashboard.speakers.times.edit') ? route('dashboard.speakers.times.edit',$time)
      : (route_exists('admin.speakers.times.edit') ? route('admin.speakers.times.edit',$time) : '#');

  $toggleTime = fn($time)=>
      route_exists('dashboard.speakers.times.toggle') ? route('dashboard.speakers.times.toggle',$time)
      : (route_exists('admin.speakers.times.toggle') ? route('admin.speakers.times.toggle',$time) : '#');

  $deleteTime = fn($time)=>
      route_exists('dashboard.speakers.times.destroy') ? route('dashboard.speakers.times.destroy',$time)
      : (route_exists('admin.speakers.times.destroy') ? route('admin.speakers.times.destroy',$time) : '#');

  $backToSpeakers = route_exists('admin.speakers.index',[$speaker->type]) ? route('admin.speakers.index',[$speaker->type]) : '#';

  // فلاتر المواعيد (يُفضَّل أن تكون وصلت من الكنترولر)
  $filters = $filters ?? ['date' => request('date'), 'status' => request('status')];
?>

<style>
  .hero {
    position: relative; border-radius: 16px; overflow: hidden;
    background: linear-gradient(135deg, #4f46e5, #06b6d4);
    color: #fff; padding: 18px 20px; margin-bottom: 18px;
    box-shadow: 0 12px 32px rgba(2,6,23,.15);
  }
  .hero .avatar { width: 72px; height: 72px; border-radius: 50%; object-fit: cover; border: 3px solid rgba(255,255,255,.7); }
  .hero .name { font-weight: 800; font-size: 1.15rem; letter-spacing:.2px }
  .hero .meta { opacity: .95; }
  .soft-card { border: 1px solid #eef2f7; border-radius: 14px; box-shadow: 0 10px 28px rgba(16,24,40,.04); }
  .stat { display:flex; gap:12px; align-items:center; padding:10px 12px; border-radius:12px; background:#f8fafc; }
  .pill { display:inline-block; padding:4px 10px; border-radius:999px; background:#f1f5f9; font-size:.82rem; color:#0f172a; }
  .divider { height:1px; background:#f1f5f9; margin:10px 0 18px }
  .nav-tabs .nav-link { font-weight:600 }
</style>


<div class="hero">
  <div class="d-flex align-items-center gap-3">
    <div>
      <?php if($speaker->image): ?>
        <img class="avatar" src="<?php echo e(asset('public/'. $speaker->image)); ?>" alt="">
      <?php else: ?>
        <img class="avatar" src="https://via.placeholder.com/72" alt="">
      <?php endif; ?>
    </div>
    <div>
      <div class="name">
        <?php echo e($isAr ? ($speaker->name_ar ?? 'متحدث') : ($speaker->name_en ?? 'Speaker')); ?>

      </div>
      <div class="meta">
        <?php echo e($isAr ? ($speaker->title_ar ?? '') : ($speaker->title_en ?? '')); ?>

        <?php if($speaker->company_name_ar || $speaker->company_name_en): ?>
          • <?php echo e($isAr ? ($speaker->company_name_ar ?? '') : ($speaker->company_name_en ?? '')); ?>

        <?php endif; ?>
      </div>
      <div class="mt-1"><span class="pill"><?php echo e($t('جدول المتحدث','Speaker Schedule')); ?></span></div>
    </div>
    <div class="ms-auto d-flex gap-2">
      <a href="<?php echo e($createTimeUrl); ?>" class="btn btn-light text-primary">
        <i class="fas fa-plus me-1"></i> <?php echo e($t('إضافة موعد','Create Time')); ?>

      </a>
      <a href="<?php echo e($backToSpeakers); ?>" class="btn btn-outline-light">
        <i class="fas fa-arrow-left me-1"></i> <?php echo e($t('رجوع','Back')); ?>

      </a>
    </div>
  </div>
</div>

<?php if(session('success')): ?>
  <div class="alert alert-success soft-card p-3"><i class="far fa-check-circle me-1"></i> <?php echo e(session('success')); ?></div>
<?php endif; ?>
<?php if($errors->any()): ?>
  <div class="alert alert-danger soft-card p-3"><i class="fas fa-exclamation-triangle me-1"></i> <?php echo e($errors->first()); ?></div>
<?php endif; ?>


<ul class="nav nav-tabs" role="tablist">
  <li class="nav-item">
    <a class="nav-link <?php echo e($activeTab==='times' ? 'active' : ''); ?>"
       href="<?php echo e(request()->fullUrlWithQuery(['tab'=>'times'])); ?>" role="tab">
      <?php echo e($t('المواعيد','Times')); ?>

    </a>
  </li>
  <li class="nav-item">
    <a class="nav-link <?php echo e($activeTab==='bookings' ? 'active' : ''); ?>"
       href="<?php echo e(request()->fullUrlWithQuery(['tab'=>'bookings'])); ?>" role="tab">
      <?php echo e($t('الحجوزات','Bookings')); ?>

    </a>
  </li>
</ul>

<div class="tab-content pt-3">
  
  <div class="tab-pane fade <?php echo e($activeTab==='times' ? 'show active' : ''); ?>" id="tab-times">
    
    <div class="row g-3 mb-3">
      <div class="col-md-8">
        <div class="soft-card p-3">
          <div class="row g-3">
            <div class="col-sm-4">
              <div class="stat">
                <i class="far fa-calendar-check"></i>
                <div>
                  <div class="text-muted small"><?php echo e($t('مواعيد في الصفحة','Times on page')); ?></div>
                  <div class="fw-bold"><?php echo e(($times instanceof \Illuminate\Pagination\LengthAwarePaginator) ? $times->count() : count($times ?? [])); ?></div>
                </div>
              </div>
            </div>
            <div class="col-sm-4">
              <div class="stat">
                <i class="far fa-clock"></i>
                <div>
                  <div class="text-muted small"><?php echo e($t('اليوم المُصفّى','Filtered day')); ?></div>
                  <div class="fw-bold"><?php echo e($filters['date'] ?: $t('غير محدد','Not set')); ?></div>
                </div>
              </div>
            </div>
            <div class="col-sm-4">
              <div class="stat">
                <i class="far fa-flag"></i>
                <div>
                  <div class="text-muted small"><?php echo e($t('الحالة','Status')); ?></div>
                  <div class="fw-bold"><?php echo e($filters['status'] ?: $t('الكل','All')); ?></div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      
      <div class="col-md-4">
        <form class="soft-card p-3" method="get" action="">
          <input type="hidden" name="tab" value="times">
          <div class="mb-2">
            <label class="form-label"><?php echo e($t('التاريخ','Date')); ?></label>
            <input type="date" name="date" value="<?php echo e($filters['date']); ?>" class="form-control">
          </div>
          <div class="mb-3">
            <label class="form-label"><?php echo e($t('الحالة','Status')); ?></label>
            <select name="status" class="form-select">
              <option value=""><?php echo e($t('الكل','All')); ?></option>
              <option value="available"  <?php if($filters['status']==='available'): echo 'selected'; endif; ?>><?php echo e($t('متاح','Available')); ?></option>
              <option value="booked"     <?php if($filters['status']==='booked'): echo 'selected'; endif; ?>><?php echo e($t('محجوز','Booked')); ?></option>
              <option value="unavailable"<?php if($filters['status']==='unavailable'): echo 'selected'; endif; ?>><?php echo e($t('غير متاح','Unavailable')); ?></option>
            </select>
          </div>
          <div class="d-flex gap-2">
            <button class="btn btn-primary w-100"><i class="fas fa-filter me-1"></i> <?php echo e($t('تصفية','Filter')); ?></button>
            <a href="<?php echo e(route_exists('dashboard.speakers.schedule') ? route('dashboard.speakers.schedule',$speaker) : request()->url()); ?>"
               class="btn btn-light w-100"><?php echo e($t('إعادة ضبط','Reset')); ?></a>
          </div>
        </form>
      </div>
    </div>

    
    <div class="soft-card p-0">
      <div class="d-flex justify-content-between align-items-center px-3 py-2 border-bottom">
        <h6 class="mb-0"><?php echo e($t('قائمة المواعيد','Time Slots')); ?></h6>
        <a href="<?php echo e($createTimeUrl); ?>" class="btn btn-sm btn-primary"><i class="fas fa-plus me-1"></i><?php echo e($t('إضافة موعد','Add Time')); ?></a>
      </div>

      <div class="table-responsive">
        <table class="table table-striped align-middle mb-0">
          <thead>
            <tr>
              <th><?php echo e($t('التاريخ','Date')); ?></th>
              <th><?php echo e($t('من','From')); ?></th>
              <th><?php echo e($t('إلى','To')); ?></th>
              <th><?php echo e($t('الحالة','Status')); ?></th>
              <th><?php echo e($t('مفعّل؟','Active?')); ?></th>
              <th><?php echo e($t('حجوزات مؤكدة','Confirmed bookings')); ?></th>
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
              <td><?php echo e($tRow->confirmed_bookings_count ?? 0); ?></td>
              <td class="text-end">
                <?php
                  $editUrl   = $editTime($tRow);
                  $toggleUrl = $toggleTime($tRow);
                  $delUrl    = $deleteTime($tRow);
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
            <tr><td colspan="7" class="text-center text-muted py-4"><?php echo e($t('لا توجد مواعيد','No times yet')); ?></td></tr>
          <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>

    
    <?php if(method_exists(($times ?? null), 'links')): ?>
      <div class="mt-2">
        <?php echo e($times->appends(['tab'=>'times','date'=>$filters['date'],'status'=>$filters['status']])->links()); ?>

      </div>
    <?php endif; ?>
  </div>

  
  <div class="tab-pane fade <?php echo e($activeTab==='bookings' ? 'show active' : ''); ?>" id="tab-bookings">
    <div class="soft-card p-0">
      <div class="d-flex justify-content-between align-items-center px-3 py-2 border-bottom">
        <h6 class="mb-0"><?php echo e($t('الحجوزات (عرض فقط)','Bookings (read-only)')); ?></h6>
        <span class="text-muted small px-2"><?php echo e($t('الداشبورد لا ينشئ حجوزات – للمتابعة فقط','Dashboard does not create bookings – read-only')); ?></span>
      </div>

      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
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
              <td>
                <?php if($bk->time_from instanceof \Illuminate\Support\Carbon): ?>
                  <?php echo e($bk->time_from->format('Y-m-d H:i')); ?>

                <?php else: ?>
                  <?php echo e(\Illuminate\Support\Str::limit($bk->time_from, 16, '')); ?>

                <?php endif; ?>
              </td>
              <td>
                <?php if($bk->time_to instanceof \Illuminate\Support\Carbon): ?>
                  <?php echo e($bk->time_to->format('Y-m-d H:i')); ?>

                <?php else: ?>
                  <?php echo e(\Illuminate\Support\Str::limit($bk->time_to, 16, '')); ?>

                <?php endif; ?>
              </td>
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
            <tr><td colspan="5" class="text-center text-muted py-4"><?php echo e($t('لا توجد حجوزات','No bookings')); ?></td></tr>
          <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>

    
    <?php if(method_exists(($bookings ?? null), 'links')): ?>
      <div class="mt-2">
        <?php echo e($bookings->appends(['tab'=>'bookings'])->links()); ?>

      </div>
    <?php endif; ?>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.layoutMaster', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/u552947370/domains/affiliatesummitglobal.com/public_html/resources/views/content/speakers/schedule/show.blade.php ENDPATH**/ ?>