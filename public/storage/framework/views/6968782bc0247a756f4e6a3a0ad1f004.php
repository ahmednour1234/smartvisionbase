

<?php $__env->startSection('title'); ?>
  <?php
    $isSpecial = (int)($type ?? 1) === 2;
  ?>
  <?php echo e($isSpecial ? __('speaker.special_guests_title', [], app()->getLocale()) : __('speaker.page_title', [], app()->getLocale())); ?>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<?php
  // تحديد إذا كانت صفحة الضيوف المميزين
  $isSpecial = (int)($type ?? 1) === 2;

  // عناوين الصفحة والأزرار
  $pageTitle = $isSpecial ? __('speaker.special_guests_title') : __('speaker.page_title');
  $addLabel  = $isSpecial ? __('speaker.add_special_guest') : __('speaker.add');

  // الأقسام المتاحة:
  // لو الكنترولر مرّر $sections هنستخدمه، وإلا fallback لقيم افتراضية
  /** @var array<string> $sections */
  $sections = isset($sections) && is_array($sections) && count($sections)
    ? array_values($sections)
    : ['special', 'aps'];

  // القيمة المختارة حاليًا من الـ query
  $currentSection = trim((string) request('section', ''));
  $currentSearch  = trim((string) request('search', ''));
?>

<div class="card">
  <div class="card-header d-flex justify-content-between align-items-center">
    <h5 class="mb-0"><?php echo e($pageTitle); ?></h5>
    <a href="<?php echo e(route('admin.speakers.create', $type)); ?>" class="btn btn-primary">
      <i class="fas fa-plus me-1"></i> <?php echo e($addLabel); ?>

    </a>
  </div>

  <div class="card-body">
    
    <form method="GET" action="<?php echo e(route('admin.speakers.index', $type)); ?>" class="row g-3 mb-4">
      <div class="col-md-4">
        <input
          type="text"
          name="search"
          class="form-control"
          placeholder="<?php echo e(__('speaker.search_name')); ?>"
          value="<?php echo e($currentSearch); ?>"
        >
      </div>

      <?php if($isSpecial): ?>
        <div class="col-md-4">
          <select name="section" class="form-select">
            <option value=""><?php echo e(__('speaker.choose_section')); ?></option>
            <?php $__currentLoopData = $sections; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sec): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <option value="<?php echo e($sec); ?>" <?php echo e($currentSection === $sec ? 'selected' : ''); ?>>
                <?php echo e(__("speaker.$sec")); ?>

              </option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </select>
        </div>
      <?php endif; ?>

      <div class="col-md-2">
        <button class="btn btn-outline-primary w-100" type="submit">
          <i class="fas fa-search"></i> <?php echo e(__('general.search')); ?>

        </button>
      </div>
    </form>

    
    <div class="table-responsive">
      <table class="table align-middle">
        <thead>
          <tr>
            <th>#</th>
            <th><?php echo e(__('speaker.image')); ?></th>
            <th><?php echo e(__('speaker.name')); ?></th>
            <th><?php echo e(__('speaker.title')); ?></th>
            <th><?php echo e(__('speaker.company')); ?></th>
            <th><?php echo e(__('speaker.number_of_followers')); ?></th>
            <th class="text-end"><?php echo e(__('speaker.actions')); ?></th>
          </tr>
        </thead>
        <tbody>
          <?php $__empty_1 = true; $__currentLoopData = $speakers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $speaker): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <tr>
              <td><?php echo e($loop->iteration + ($speakers->currentPage() - 1) * $speakers->perPage()); ?></td>

              <td>
                <?php if($speaker->image): ?>
                  <img
                    src="<?php echo e(asset('public/'.$speaker->image)); ?>"
                    width="60" height="60"
                    class="rounded-circle object-fit-cover"
                    alt="img"
                  >
                <?php else: ?>
                  <span class="text-muted">-</span>
                <?php endif; ?>
              </td>

              <td><?php echo e(app()->getLocale() === 'ar' ? $speaker->name_ar : $speaker->name_en); ?></td>
              <td><?php echo e(app()->getLocale() === 'ar' ? $speaker->title_ar : $speaker->title_en); ?></td>
              <td><?php echo e(app()->getLocale() === 'ar' ? $speaker->company_name_ar : $speaker->company_name_en); ?></td>
              <td><?php echo e($speaker->number_of_followers ?? ''); ?></td>

              <td class="text-end text-nowrap">
                <a href="<?php echo e(route('admin.speakers.show', [$type, $speaker->id])); ?>"
                   class="btn btn-sm btn-outline-info"
                   title="<?php echo e(__('general.view')); ?>">
                  <i class="fas fa-eye"></i>
                </a>

                <a href="<?php echo e(route('admin.speakers.edit', [$type, $speaker->id])); ?>"
                   class="btn btn-sm btn-outline-primary"
                   title="<?php echo e(__('general.edit')); ?>">
                  <i class="fas fa-edit"></i>
                </a>

                <a href="<?php echo e(route('dashboard.speakers.schedule', [$speaker->id])); ?>"
                   class="btn btn-sm btn-outline-primary"
                   title="<?php echo e(__('speaker.schedule')); ?>">
                  <i class="fas fa-clock"></i>
                </a>

                <form action="<?php echo e(route('admin.speakers.destroy', [$speaker])); ?>"
                      method="POST"
                      class="d-inline"
                      onsubmit="return confirm('<?php echo e(__('speaker.confirm_delete')); ?>')">
                  <?php echo csrf_field(); ?>
                  <?php echo method_field('DELETE'); ?>
                  <button class="btn btn-sm btn-outline-danger" title="<?php echo e(__('general.delete')); ?>">
                    <i class="fas fa-trash"></i>
                  </button>
                </form>
              </td>
            </tr>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <tr>
              <td colspan="7" class="text-center"><?php echo e(__('general.no_data')); ?></td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>

    
    <?php
      $paginator = $speakers->appends(request()->query());
      $current = $paginator->currentPage();
      $last    = $paginator->lastPage();
      $window  = 2;
      $start   = max(1, $current - $window);
      $end     = min($last, $current + $window);

      if ($current <= $window) {
          $end = min($last, max($end, 1 + ($window * 2)));
      }
      if ($current > $last - $window) {
          $start = max(1, min($start, $last - ($window * 2)));
      }

      $range = range($start, $end);
    ?>

    <?php if($paginator->hasPages()): ?>
      <div class="mt-4 d-flex justify-content-center">
        <nav class="inline-flex shadow-sm rounded-md" aria-label="Pagination">
          
          <?php if($paginator->onFirstPage()): ?>
            <span class="px-3 py-2 bg-light text-muted border rounded-start">‹</span>
          <?php else: ?>
            <a href="<?php echo e($paginator->previousPageUrl()); ?>"
               class="px-3 py-2 bg-white text-secondary border hover:bg-light rounded-start">‹</a>
          <?php endif; ?>

          
          <?php if(!in_array(1, $range)): ?>
            <a href="<?php echo e($paginator->url(1)); ?>"
               class="px-3 py-2 border <?php echo e($current === 1 ? 'bg-primary text-white' : 'bg-white text-secondary hover:bg-light'); ?>">1</a>
            <?php if($start > 2): ?>
              <span class="px-3 py-2 border bg-white text-muted">…</span>
            <?php endif; ?>
          <?php endif; ?>

          
          <?php $__currentLoopData = $range; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $page): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <a href="<?php echo e($paginator->url($page)); ?>"
               class="px-3 py-2 border <?php echo e($current === $page ? 'bg-primary text-white' : 'bg-white text-secondary hover:bg-light'); ?>">
              <?php echo e($page); ?>

            </a>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

          
          <?php if(!in_array($last, $range)): ?>
            <?php if($end < $last - 1): ?>
              <span class="px-3 py-2 border bg-white text-muted">…</span>
            <?php endif; ?>
            <a href="<?php echo e($paginator->url($last)); ?>"
               class="px-3 py-2 border <?php echo e($current === $last ? 'bg-primary text-white' : 'bg-white text-secondary hover:bg-light'); ?>"><?php echo e($last); ?></a>
          <?php endif; ?>

          
          <?php if($paginator->hasMorePages()): ?>
            <a href="<?php echo e($paginator->nextPageUrl()); ?>"
               class="px-3 py-2 bg-white text-secondary border hover:bg-light rounded-end">›</a>
          <?php else: ?>
            <span class="px-3 py-2 bg-light text-muted border rounded-end">›</span>
          <?php endif; ?>
        </nav>
      </div>
    <?php endif; ?>

  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.layoutMaster', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/u552947370/domains/affiliatesummitglobal.com/public_html/resources/views/content/speakers/index.blade.php ENDPATH**/ ?>