

<?php $__env->startSection('title', (app()->getLocale()==='ar' ? 'إضافة موعد للمتحدث' : 'Create Speaker Time')); ?>

<?php $__env->startSection('content'); ?>
<?php
  $isAr = app()->getLocale()==='ar';
  $t = fn($ar,$en)=> $isAr ? $ar : $en;

  // Helper للتأكد من وجود اسم روت
  function route_exists($name){
    try { return \Illuminate\Support\Facades\Route::has($name); }
    catch (\Throwable $e) { return false; }
  }

  $storeUrl = route_exists('dashboard.speakers.times.store')
      ? route('dashboard.speakers.times.store', $speaker)
      : (route_exists('admin.speakers.times.store')
          ? route('admin.speakers.times.store', $speaker)
          : '#');

  $backUrl = route_exists('dashboard.speakers.schedule')
      ? route('dashboard.speakers.schedule', $speaker)
      : (route_exists('admin.speakers.schedule')
          ? route('admin.speakers.schedule', $speaker)
          : (route_exists('admin.speakers.index') ? route('admin.speakers.index') : '#'));
?>

<style>
  .hero {
    position: relative;
    border-radius: 16px;
    overflow: hidden;
    background: linear-gradient(135deg, #4f46e5, #06b6d4);
    color: #fff;
    padding: 22px 24px;
    margin-bottom: 18px;
  }
  .hero .avatar {
    width: 72px; height: 72px; border-radius: 50%;
    border: 3px solid rgba(255,255,255,.7); object-fit: cover;
  }
  .hero .name { font-weight: 700; font-size: 1.15rem; }
  .soft-card {
    border: 1px solid #eef2f7; border-radius: 14px;
    box-shadow: 0 12px 30px rgba(16,24,40,.04);
  }
  .section-title {
    font-weight: 700; display: flex; align-items:center; gap:10px; margin-bottom: 12px;
  }
  .pill {
    display:inline-block; padding: 4px 10px; border-radius: 999px; background: #f1f5f9; font-size: .825rem; color:#0f172a;
  }
  .hint { color:#64748b; font-size:.9rem }
  .form-help { font-size:.8rem; color:#6b7280 }
  .divider { height:1px; background:#f1f5f9; margin:12px 0 18px }
</style>

<div class="hero">
  <div class="d-flex align-items-center gap-3">
    <div>
      <?php if($speaker->image): ?>
        <img class="avatar" src="<?php echo e(asset('public/'.$speaker->image)); ?>" alt="">
      <?php else: ?>
        <img class="avatar" src="https://via.placeholder.com/72" alt="">
      <?php endif; ?>
    </div>
    <div>
      <div class="name"><?php echo e($isAr ? ($speaker->name_ar ?? 'المتحدث') : ($speaker->name_en ?? 'Speaker')); ?></div>
      <div class="hint">
        <?php echo e($isAr ? ($speaker->title_ar ?? '') : ($speaker->title_en ?? '')); ?>

        <?php if($speaker->company_name_ar || $speaker->company_name_en): ?>
          • <?php echo e($isAr ? ($speaker->company_name_ar ?? '') : ($speaker->company_name_en ?? '')); ?>

        <?php endif; ?>
      </div>
      <span class="pill"><?php echo e($t('إضافة موعد جديد','Create New Time Slot')); ?></span>
    </div>
    <div class="ms-auto">
      <a href="<?php echo e($backUrl); ?>" class="btn btn-light text-primary">
        <i class="fas fa-arrow-left me-1"></i> <?php echo e($t('رجوع','Back')); ?>

      </a>
    </div>
  </div>
</div>

<?php if($errors->any()): ?>
  <div class="alert alert-danger soft-card p-3">
    <i class="fas fa-exclamation-triangle me-1"></i> <?php echo e($errors->first()); ?>

  </div>
<?php endif; ?>

<form method="post" action="<?php echo e($storeUrl); ?>" class="soft-card p-3 p-md-4">
  <?php echo csrf_field(); ?>

  <div class="section-title">
    <i class="far fa-clock"></i> <?php echo e($t('بيانات الموعد','Time Details')); ?>

  </div>

  <?php if ($__env->exists('content.speakers.times._form')) echo $__env->make('content.speakers.times._form', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

  <div class="divider"></div>

  <div class="d-flex gap-2">
    <button type="submit" class="btn btn-primary">
      <i class="fas fa-save me-1"></i> <?php echo e($t('حفظ','Save')); ?>

    </button>
    <a href="<?php echo e($backUrl); ?>" class="btn btn-outline-secondary">
      <?php echo e($t('إلغاء','Cancel')); ?>

    </a>
  </div>
</form>


<script>
  (function(){
    const fromInput = document.querySelector('input[name="time_from"]');
    const toInput   = document.querySelector('input[name="time_to"]');
    const dateInput = document.querySelector('input[name="date"]');
    const duration  = document.getElementById('duration');

    function pad(n){ return String(n).padStart(2,'0'); }

    function addMinutesToTime(timeHHmm, minutes){
      if(!timeHHmm) return '';
      const [hh,mm] = timeHHmm.split(':').map(Number);
      const d = new Date(2000,0,1, hh, mm, 0);
      d.setMinutes(d.getMinutes() + minutes);
      return pad(d.getHours())+':'+pad(d.getMinutes());
    }

    function updateTo(){
      if(fromInput && toInput && duration){
        const mins = parseInt(duration.value || '30', 10);
        toInput.value = addMinutesToTime(fromInput.value, mins);
      }
    }

    if(fromInput){ fromInput.addEventListener('change', updateTo); }
    if(duration){ duration.addEventListener('change', updateTo); }
  })();
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.layoutMaster', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/u552947370/domains/affiliatesummitglobal.com/public_html/resources/views/content/speakers/times/create.blade.php ENDPATH**/ ?>