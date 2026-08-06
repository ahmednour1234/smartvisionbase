@php
  // Boolean حقيقي بدل json_encode(false/true)
  $menuCollapsed = ($configData['menuCollapsed'] === 'layout-menu-collapsed');
@endphp

<!-- laravel style -->
<script src="{{ asset('public/assets/vendor/js/helpers.js') }}"></script>

@if ($configData['hasCustomizer'])
  <!-- Template customizer -->
  <script src="{{ asset('public/assets/vendor/js/template-customizer.js') }}"></script>
@endif

<!-- Mandatory theme config -->
<script src="{{ asset('public/assets/js/config.js') }}"></script>

@if ($configData['hasCustomizer'])
<script>
  window.templateCustomizer = new TemplateCustomizer({
    cssPath: '',
    themesPath: '',
    defaultStyle: "{{ $configData['styleOpt'] }}",
    defaultShowDropdownOnHover: "{{ $configData['showDropdownOnHover'] }}", // true/false (for horizontal layout only)
    displayCustomizer: "{{ $configData['displayCustomizer'] }}",
    lang: '{{ app()->getLocale() }}',
    pathResolver: function(path) {
      var resolvedPaths = {
        // Core stylesheets
        'core.css': '{{ asset('public/assets/vendor/css' . $configData['rtlSupport'] . '/core.css') }}',
        'core-dark.css': '{{ asset('public/assets/vendor/css' . $configData['rtlSupport'] . '/core-dark.css') }}',

        // Themes
        'theme-default.css': '{{ asset('public/assets/vendor/css' . $configData['rtlSupport'] . '/theme-default.css') }}',
        'theme-default-dark.css': '{{ asset('public/assets/vendor/css' . $configData['rtlSupport'] . '/theme-default-dark.css') }}',

        'theme-bordered.css': '{{ asset('public/assets/vendor/css' . $configData['rtlSupport'] . '/theme-bordered.css') }}',
        'theme-bordered-dark.css': '{{ asset('public/assets/vendor/css' . $configData['rtlSupport'] . '/theme-bordered-dark.css') }}',

        'theme-semi-dark.css': '{{ asset('public/assets/vendor/css' . $configData['rtlSupport'] . '/theme-semi-dark.css') }}',
        'theme-semi-dark-dark.css': '{{ asset('public/assets/vendor/css' . $configData['rtlSupport'] . '/theme-semi-dark-dark.css') }}',
      };
      return resolvedPaths[path] || path;
    },
    controls: {!! json_encode($configData['customizerControls']) !!}
  });
</script>
@endif
