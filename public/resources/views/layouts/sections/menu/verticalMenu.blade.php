@php
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
@endphp

<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">

  @if(!isset($navbarFull))
  <div class="app-brand demo">
    <a href="{{ url('/') }}" class="app-brand-link">
      <span class="app-brand-logo demo">
        @include('_partials.macros',["height"=>20])
      </span>
      <span class="app-brand-text demo menu-text fw-bold">Affaliate</span>
    </a>

    <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto">
      <i class="ti menu-toggle-icon d-none d-xl-block ti-sm align-middle"></i>
      <i class="ti ti-x d-block d-xl-none ti-sm align-middle"></i>
    </a>
  </div>
  @endif

  <div class="menu-inner-shadow"></div>

  <ul class="menu-inner py-1">
    @foreach ($menuTree as $menu)
      @if (isset($menu->menuHeader))
        <li class="menu-header small text-uppercase">
          <span class="menu-header-text">{{ __($menu->menuHeader) }}</span>
        </li>
      @else
        @php
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
        @endphp

        <li class="menu-item {{ $activeClass }}">
          <a href="{{ isset($menu->url) ? url($menu->url) : 'javascript:void(0);' }}"
             class="{{ isset($menu->submenu) ? 'menu-link menu-toggle' : 'menu-link' }}"
             @if (!empty($menu->target ?? null)) target="_blank" @endif>
            @isset($menu->icon)
              <i class="{{ $menu->icon }}"></i>
            @endisset
            <div>{{ isset($menu->name) ? __($menu->name) : '' }}</div>
            @isset($menu->badge)
              <div class="badge bg-{{ $menu->badge[0] }} rounded-pill ms-auto">{{ $menu->badge[1] }}</div>
            @endisset
          </a>

          @isset($menu->submenu)
            @include('layouts.sections.menu.submenu', ['menu' => $menu->submenu])
          @endisset
        </li>
      @endif
    @endforeach
  </ul>
</aside>
