<?php
$containerFooter = (isset($configData['contentLayout']) && $configData['contentLayout'] === 'compact') ? 'container-xxl' : 'container-fluid';
?>

<!-- Footer-->
<footer class="content-footer footer bg-footer-theme">
  <div class="<?php echo e($containerFooter); ?>">
    <div class="footer-container d-flex align-items-center justify-content-between py-2 flex-md-row flex-column">
      <div>
        © <script>document.write(new Date().getFullYear())
      </script>,  North Sea
      </div>
      <div class="d-none d-lg-inline-block">
      
      </div>
    </div>
  </div>
</footer>
<!--/ Footer-->
<?php /**PATH /home/u552947370/domains/affiliatesummitglobal.com/public_html/resources/views/layouts/sections/footer/footer.blade.php ENDPATH**/ ?>