<?php
    // اكتشاف بسيط للموبايل/التابلت من الـ User-Agent بدون باكچات
    $ua = request()->header('User-Agent', '') ?? '';
    $isMobile = preg_match(
        '/Mobile|Android|iPhone|iPad|iPod|IEMobile|BlackBerry|Opera Mini|webOS|Kindle|Silk|Opera Mobi/i',
        $ua
    ) === 1;

    // لو مش عايز تعتبر iPad "موبايل"، احذف iPad من الريجيكس فوق
?>

<?php if($isMobile): ?>
    
    <div class="mobile-only-page">
        <div id="mobile-list">
            <?php echo $__env->make('web.content.partials.voting_partials.voting-mobile', ['companies' => $companies], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        </div>
    </div>
<?php else: ?>
    
    <div class="desktop-only">
        <div id="desktop-list">
            <?php echo $__env->make('web.content.partials.voting_partials.voting-desktop', ['companies' => $companies], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        </div>
    </div>
<?php endif; ?>
<?php /**PATH /home/u552947370/domains/affiliatesummitglobal.com/public_html/resources/views/web/content/sections/voting_partials.blade.php ENDPATH**/ ?>