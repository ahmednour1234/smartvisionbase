<?php
  $configData = Helper::appClasses();

  // نوع المستخدم الحالي كـ string (آمن لو null)
  $authType = (string) optional(auth()->user())->type;

  // دالة: هل العنصر مسموح لنوع المستخدم؟
  $isAllowedForType = function ($item) use ($authType) {
      // دعم object/array
      $visibleFor = null;
      if (is_object($item) && isset($item->visible_for_types)) {
          $visibleFor = $item->visible_for_types;
      } elseif (is_array($item) && isset($item['visible_for_types'])) {
          $visibleFor = $item['visible_for_types'];
      }

      // طبع الأنواع إلى string
      $normalize = fn($arr) => array_map('strval', (array) $arr);

      // callcenter: لازم visible_for_types يحتوي callcenter
      if ($authType === 'callcenter') {
          return is_array($visibleFor) && in_array('callcenter', $normalize($visibleFor), true);
      }

      // باقي الأنواع (مثل admin):
      // - لو مفيش visible_for_types => العنصر متاح للجميع
      // - لو موجود: لازم يحتوي نوع المستخدم أو '*' كـ وايلدكارد
      if (is_array($visibleFor)) {
          $vf = $normalize($visibleFor);
          return in_array($authType, $vf, true) || in_array('*', $vf, true);
      }

      return true;
  };

  // فلترة المنيو (recursive) مع معالجة object/array
  $filterMenu = function ($menu) use (&$filterMenu, $isAllowedForType) {
      $out = [];
      foreach ($menu as $item) {
          $obj = is_array($item) ? (object) $item : $item;

          if (!$isAllowedForType($obj)) {
              continue;
          }

          if (isset($obj->submenu) && is_array($obj->submenu)) {
              $obj->submenu = $filterMenu($obj->submenu);
              // لو مفيش url والـ submenu فاضية يبقى نخفيه
              if (empty($obj->submenu) && empty($obj->url ?? null)) {
                  continue;
              }
          }

          $out[] = $obj;
      }
      return $out;
  };

  // خُد المنيو الخام (زي ما كانت عندك)
  $rawMenu = $menuData[0]->menu ?? [];
  // فلترها
  $menuTree = $filterMenu($rawMenu);
?>

<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">

  <?php if(!isset($navbarFull)): ?>
  <div class="app-brand demo">
    <a href="<?php echo e(url('/')); ?>" class="app-brand-link">
      <span class="app-brand-logo demo">
        <?php echo $__env->make('_partials.macros',["height"=>20], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
      </span>
      <span class="app-brand-text demo menu-text fw-bold">Affaliate</span>
    </a>

    <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto">
      <i class="ti menu-toggle-icon d-none d-xl-block ti-sm align-middle"></i>
      <i class="ti ti-x d-block d-xl-none ti-sm align-middle"></i>
    </a>
  </div>
  <?php endif; ?>

  <div class="menu-inner-shadow"></div>

  <ul class="menu-inner py-1">
    <?php $__currentLoopData = $menuTree; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $menu): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
      <?php if(isset($menu->menuHeader)): ?>
        <li class="menu-header small text-uppercase">
          <span class="menu-header-text"><?php echo e(__($menu->menuHeader)); ?></span>
        </li>
      <?php else: ?>
        <?php
          $activeClass = null;
          $currentRouteName = Route::currentRouteName();
          $slugValue = $menu->slug ?? '';

          if ($currentRouteName === $slugValue) {
            $activeClass = 'active';
          } elseif (isset($menu->submenu)) {
            if (is_array($slugValue)) {
              foreach ($slugValue as $slug) {
                if (is_string($slug) && str_contains($currentRouteName, $slug) && strpos($currentRouteName, $slug) === 0) {
                  $activeClass = 'active open';
                }
              }
            } elseif (is_string($slugValue)) {
              if (str_contains($currentRouteName, $slugValue) && strpos($currentRouteName, $slugValue) === 0) {
                $activeClass = 'active open';
              }
            }
          }
        ?>

        <li class="menu-item <?php echo e($activeClass); ?>">
          <a href="<?php echo e(isset($menu->url) ? url($menu->url) : 'javascript:void(0);'); ?>"
             class="<?php echo e(isset($menu->submenu) ? 'menu-link menu-toggle' : 'menu-link'); ?>"
             <?php if(!empty($menu->target ?? null)): ?> target="_blank" <?php endif; ?>>
            <?php if(isset($menu->icon)): ?>
              <i class="<?php echo e($menu->icon); ?>"></i>
            <?php endif; ?>
            <div><?php echo e(isset($menu->name) ? __($menu->name) : ''); ?></div>
            <?php if(isset($menu->badge)): ?>
              <div class="badge bg-<?php echo e($menu->badge[0]); ?> rounded-pill ms-auto"><?php echo e($menu->badge[1]); ?></div>
            <?php endif; ?>
          </a>

          <?php if(isset($menu->submenu)): ?>
            <?php echo $__env->make('layouts.sections.menu.submenu', ['menu' => $menu->submenu], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
          <?php endif; ?>
        </li>
      <?php endif; ?>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
  </ul>
</aside>
<?php /**PATH /home/u552947370/domains/affiliatesummitglobal.com/public_html/resources/views/layouts/sections/menu/verticalMenu.blade.php ENDPATH**/ ?>