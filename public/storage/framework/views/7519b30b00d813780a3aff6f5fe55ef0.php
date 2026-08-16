<?php
  // Boolean حقيقي بدل json_encode(false/true)
  $menuCollapsed = ($configData['menuCollapsed'] === 'layout-menu-collapsed');
?>

<!-- laravel style -->
<script src="<?php echo e(asset('public/assets/vendor/js/helpers.js')); ?>"></script>

<?php if($configData['hasCustomizer']): ?>
  <!-- Template customizer -->
  <script src="<?php echo e(asset('public/assets/vendor/js/template-customizer.js')); ?>"></script>
<?php endif; ?>

<!-- Mandatory theme config -->
<script src="<?php echo e(asset('public/assets/js/config.js')); ?>"></script>

<?php if($configData['hasCustomizer']): ?>
<script>
  window.templateCustomizer = new TemplateCustomizer({
    cssPath: '',
    themesPath: '',
    defaultStyle: "<?php echo e($configData['styleOpt']); ?>",
    defaultShowDropdownOnHover: "<?php echo e($configData['showDropdownOnHover']); ?>", // true/false (for horizontal layout only)
    displayCustomizer: "<?php echo e($configData['displayCustomizer']); ?>",
    lang: '<?php echo e(app()->getLocale()); ?>',
    pathResolver: function(path) {
      var resolvedPaths = {
        // Core stylesheets
        'core.css': '<?php echo e(asset('public/assets/vendor/css' . $configData['rtlSupport'] . '/core.css')); ?>',
        'core-dark.css': '<?php echo e(asset('public/assets/vendor/css' . $configData['rtlSupport'] . '/core-dark.css')); ?>',

        // Themes
        'theme-default.css': '<?php echo e(asset('public/assets/vendor/css' . $configData['rtlSupport'] . '/theme-default.css')); ?>',
        'theme-default-dark.css': '<?php echo e(asset('public/assets/vendor/css' . $configData['rtlSupport'] . '/theme-default-dark.css')); ?>',

        'theme-bordered.css': '<?php echo e(asset('public/assets/vendor/css' . $configData['rtlSupport'] . '/theme-bordered.css')); ?>',
        'theme-bordered-dark.css': '<?php echo e(asset('public/assets/vendor/css' . $configData['rtlSupport'] . '/theme-bordered-dark.css')); ?>',

        'theme-semi-dark.css': '<?php echo e(asset('public/assets/vendor/css' . $configData['rtlSupport'] . '/theme-semi-dark.css')); ?>',
        'theme-semi-dark-dark.css': '<?php echo e(asset('public/assets/vendor/css' . $configData['rtlSupport'] . '/theme-semi-dark-dark.css')); ?>',
      };
      return resolvedPaths[path] || path;
    },
    controls: <?php echo json_encode($configData['customizerControls']); ?>

  });
</script>
<?php endif; ?>
<?php /**PATH /home/u552947370/domains/affiliatesummitglobal.com/public_html/resources/views/layouts/sections/scriptsIncludes.blade.php ENDPATH**/ ?>