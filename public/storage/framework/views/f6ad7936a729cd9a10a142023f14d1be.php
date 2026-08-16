

<?php $__env->startSection('title', 'Thank You'); ?>

<?php $__env->startSection('content'); ?>
<section class="breadcrumbs-custom bg-image context-dark"
         style="background-image: url(<?php echo e(asset('public/web/assets/images/bg-breadcrumbs-01-1894x424.jpg')); ?>);">
    <div class="container">

        <h3 class="breadcrumbs-custom-title">Thank You</h3>
    </div>
</section>
    
    <meta http-equiv="refresh" content="60;url=<?php echo e($nextUrl); ?>">

    <div class="container py-5 text-center">
        
        <h1 class="mb-3">Thank you!</h1>
        <p class="mb-4">
            You will be redirected in
            <strong><span id="countdown">60</span></strong>
            seconds...
        </p>

        <script>
            (function () {
                const target = <?php echo json_encode($nextUrl, 15, 512) ?>;
                let seconds = 60;
                const el = document.getElementById('countdown');

                const interval = setInterval(() => {
                    seconds--;
                    if (el) el.textContent = seconds;
                    if (seconds <= 0) {
                        clearInterval(interval);
                        try { window.location.replace(target); }
                        catch (e) { window.location.href = target; }
                    }
                }, 1000);

                // أمان إضافي: التحويل بعد ~60 ثانية حتى لو توقف العداد
                setTimeout(() => {
                    try { window.location.replace(target); }
                    catch (e) { window.location.href = target; }
                }, 60000);
            })();
        </script>

        <noscript>
            <p>If you are not redirected automatically, click the button below.</p>
            <a class="btn btn-primary" href="<?php echo e($nextUrl); ?>">Continue</a>
        </noscript>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('web.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/u552947370/domains/affiliatesummitglobal.com/public_html/resources/views/web/thank-you.blade.php ENDPATH**/ ?>