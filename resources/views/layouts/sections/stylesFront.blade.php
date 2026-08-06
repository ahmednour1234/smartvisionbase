<!-- BEGIN: Theme CSS-->
<!-- Fonts -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Cairo:wght@200..1000&display=swap" rel="stylesheet">

<!-- Vendor Fonts -->
<link rel="stylesheet" href="{{ asset('public/assets/vendor/fonts/tabler-icons.css') }}" />

<!-- Core CSS -->
<link rel="stylesheet" href="{{ asset('public/assets/vendor/css' . $configData['rtlSupport'] . '/core.css') }}" class="{{ $configData['hasCustomizer'] ? 'template-customizer-core-css' : '' }}" />
<link rel="stylesheet" href="{{ asset('public/assets/vendor/css' . $configData['rtlSupport'] . '/' . $configData['theme'] . '.css') }}" class="{{ $configData['hasCustomizer'] ? 'template-customizer-theme-css' : '' }}" />

<!-- Extras -->
<link rel="stylesheet" href="{{ asset('public/assets/css/demo.css') }}" />
<link rel="stylesheet" href="{{ asset('public/assets/vendor/libs/node-waves/node-waves.css') }}" />
<link rel="stylesheet" href="{{ asset('public/assets/vendor/css/pages/front-page.css') }}" />

<style>
  body,h1,h2,h3,h4,h5,h6,p,div,a,li,button { font-family: "Cairo", serif; }
</style>

<!-- Vendor Styles -->
@yield('vendor-style')

<!-- Page Styles -->
@yield('page-style')
