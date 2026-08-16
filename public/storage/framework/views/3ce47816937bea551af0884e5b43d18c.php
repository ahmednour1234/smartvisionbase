<!-- ================= STYLES ================= -->
<style>
  @import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@700&display=swap');

  /* ضبط عام ومنع Overflow أفقي */
  html, body { overflow-x: hidden; width: 100%; }
  *, *::before, *::after { box-sizing: border-box; }
  img { max-width: 100%; height: auto; display: block; }

  /* إخفاء صارم حسب المقاس حتى لو نُسي الكلاس */
  @media (max-width: 767.98px) {
    #desktopSection { display: none !important; }   /* اخفِ الديسكتوب على الموبايل */
    #mobileSection  { display: block !important; }
  }
  @media (min-width: 768px) {
    #mobileSection  { display: none !important; }   /* اخفِ الموبايل على الديسكتوب */
    #desktopSection { display: block !important; }
  }

  /* حاويات */
  .parallax-content { max-width: 100%;  }
  /* صفوف Bootstrap خارج container قد تعمل سالب margins → overflow */
  .parallax-content .row { margin-right: 0 !important; margin-left: 0 !important; }
  /* احفظ padding معتدل بدل السالب */
  .parallax-content .row > [class^="col"] { padding-left: 12px; padding-right: 12px; }

  /* مرونة العناصر الداخلية */
  .company-row, .company-card-inner, .d-flex { min-width: 0; }
  .company-content { min-width: 0; }
  .company-content h4, .company-content p, .company-content a {
    overflow: hidden; text-overflow: ellipsis; white-space: nowrap;
  }

  /* ظهور السكاشن حسب العرض (للأمان لو حبيت تستخدمهم) */
  .desktop-only { display: none; }
  .mobile-only-page { display: none; }
  @media (min-width: 768px) { .desktop-only { display: block; } }
  @media (max-width: 767.98px) { .mobile-only-page { display: block; } }

  /* موبايل */
  @media (max-width: 767.98px) {
    .company-image-desktop { display: none !important; }

    .company-row {
      display: flex; flex-direction: row !important;
      align-items: center !important; gap: 10px; min-width: 0;
    }
    .company-card-wrapper { padding: 10px 12px !important; }

    .company-image-mobile {
      display: block !important; width: 80px; height: 80px; border-radius: 50%;
      background: linear-gradient(to right, #000000, #cc252e); padding: 3px; flex-shrink: 0;
    }
    .company-image-mobile-inner { width: 100%; height: 100%; border-radius: 50%; overflow: hidden; background: #fff; }
    .company-image-mobile-inner img { width: 100%; height: 100%; object-fit: cover; }

    .company-content h4 { font-size: 16px !important; margin-bottom: 4px; }
    .company-content p, .company-content a { font-size: 13px; margin-bottom: 2px; }
    .vote-btn {
      font-size: 15px !important; padding: 10px 20px !important; white-space: nowrap;
      background: linear-gradient(90deg, #fff 0%, #fff 100%); color: #cc252e;
    }
  }
  @media (min-width: 768px) { .company-image-mobile { display: none !important; } }

  /* صندوق البحث (لو استخدمته) */
  .search-container .form-control {
    background-color: rgba(255, 255, 255, 0.1);
    color: #fff;
    border: 1px solid rgba(255, 255, 255, 0.3);
    width: 100%;
  }
  .search-container .form-control::placeholder { color: rgba(255, 255, 255, 0.6); }
  .search-container .btn { background-color: #E73780; border: none; }

  /* تأثير الكتابة للموبايل */
  .typing-effect-container{
    font-size: 18px; font-weight: 800; font-family: 'Montserrat', sans-serif;
    background: linear-gradient(to right, #000000, #cc252e);
    -webkit-background-clip: text; -webkit-text-fill-color: transparent;
    display: inline-block; white-space: nowrap; overflow: hidden;
    border-right: 3px solid #FFE986; padding-right: 0px; width: fit-content; margin: 0 auto;
    animation: blink-caret .8s step-end infinite;
  }
  .typing-effect-container.done{ animation: none; border-right: none; }

  /* عنوان الكتابة للديسكتوب */
  #typed-title {
    display: inline-block; overflow: hidden; white-space: nowrap;
    border-right: 3px solid #FFE986;
    font-family: 'Montserrat', sans-serif; font-weight: 800;
    font-size: 60px; line-height: 1.3;
    background: linear-gradient(to right, #000000, #cc252e);
    -webkit-background-clip: text; -webkit-text-fill-color: transparent;
    animation: blink-caret 0.75s step-end infinite;
  }
  #typed-title.finished { border-right: none; animation: none; }

  @media (max-width: 1365px) { #typed-title { font-size: 60px; } }
  @media (max-width: 1023px) { #typed-title { font-size: 42px; } }
  @media (max-width: 767.98px) { #typed-title { font-size: 32px; } }

  @keyframes blink-caret { 0%,100%{border-color:transparent} 50%{border-color:#FFE986} }

  /* ألوان/تدرجات إضافية */
  .stat-number{
    margin-bottom: 10px;
    background: linear-gradient(270deg,#000000,#cc252e,#000000);
    background-size: 600% 600%;
    -webkit-background-clip: text; -webkit-text-fill-color: transparent;
    animation: gradientShift 5s ease infinite;
  }
  @keyframes gradientShift{
    0%{background-position:0% 50%} 50%{background-position:100% 50%} 100%{background-position:0% 50%}
  }

  /* الكروت */
  .company-card{
    background: linear-gradient(145deg, #f4f2ee 0%, #f4f2ee 50%, #cc252e 100%);
    color: #fff; padding: 10px 20px; border-radius: 8px;
    box-shadow: 0 8px 20px rgba(0,0,0,0.18), 0 2px 6px rgba(0,0,0,0.12);
  }
  .company-card-inner{
    background: linear-gradient(145deg, #f4f2ee 0%, #f4f2ee 50%, #cc252e 100%);
    border: none; padding: 0; border-radius: 12px;
  }

  /* لا توجد نتائج */
  .js-no-results { display: none; color: #666; margin: 10px 0 16px; }

  /* زر Load More */
  .js-load-more{
    background-color: var(--e-global-color-primary, #cc252e);
    color: #fff; font-weight: 700; border: none; padding: 12px 28px; border-radius: 10px;
    box-shadow: 0 8px 20px rgba(0,0,0,.12);
  }
  .js-all-shown { display: none; color: #666; margin-top: 8px; }

  /* أداة إخفاء */
  .hidden { display: none !important; }
</style>

<?php $locale = app()->getLocale(); ?>

<!-- ================= MOBILE SECTION ================= -->
<div id="mobileSection" class="mobile-only-page">
  <section class="section bg-default parallax-container mb-5">
    <div class="parallax-content section-lg context-dark text-center" style="background-color:#fff;">
      <div class="section-title text-center">
        <h2 class="section-title__title" style="padding-top:10px; font-size:20px; font-weight:800;">
          <span class="typing-effect-container" data-typing="mobile"></span>
        </h2>
      </div>

      <!-- (اختياري) صندوق بحث للموبايل -->
      <!--
      <div class="row justify-content-center mt-3 mb-3 px-3">
        <div class="col-12 col-sm-10 col-md-6 search-container">
          <input type="text" class="form-control js-search" placeholder="<?php echo e(__('Search by name, description...')); ?>" autocomplete="off">
        </div>
      </div>
      -->

      <div class="js-no-results"><?php echo e(__('No results found')); ?></div>

      <div class="js-wrapper">
        <?php $__currentLoopData = $companies; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $idx => $company): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <?php
            $searchData = strtolower(trim(
              ($company->name_en ?? '') . ' ' .
              ($company->category ?? '') . ' ' .
              ($company->regulation ?? '') . ' ' .
              ($company->description_en ?? '') . ' ' .
              ($company->company ?? '')
            ));
          ?>

          <div class="row justify-content-center company-row-wrap" data-search="<?php echo e(e($searchData)); ?>" data-index="<?php echo e($idx); ?>">
            <div class="col-md-10">
              <div class="card mb-3 p-3 shadow company-card-wrapper"
                   style="background:linear-gradient(90deg,#f4f2ee 40%,#cc252e 70%); color:#fff; border-radius:10px;"
                   data-aos="fade-up">
                <div class="d-flex flex-md-row company-row" style="min-width:0;">
                  <!-- صورة Desktop (تختفي بالميديا) -->
                  <div class="company-image-desktop me-3 d-none d-md-block" style="flex:0 0 auto;">
                    <div style="width:160px; height:160px; border-radius:50%; overflow:hidden;">
                      <img src="<?php echo e(asset('public/'.$company->image)); ?>" alt="<?php echo e($company->name_en); ?>"
                           style="width:100%; height:100%; object-fit:cover;" loading="lazy" decoding="async" fetchpriority="low">
                    </div>
                  </div>

                  <!-- صورة Mobile -->
                  <div class="company-image-mobile me-2" style="flex:0 0 auto;">
                    <div class="company-image-mobile-inner">
                      <img src="<?php echo e(asset('public/'.$company->image)); ?>" alt="<?php echo e($company->name_en); ?>"
                           loading="lazy" decoding="async" fetchpriority="low">
                    </div>
                  </div>

                  <!-- المحتوى -->
                  <div class="company-content text-start flex-grow-1" style="min-width:0;">
                    <h4 class="stat-number" style="font-size:20px;"><?php echo e($company->name_en); ?></h4>
                    <?php if($company->category): ?>
                      <p class="fw-bold stat-number" style="font-size:14px;"><?php echo e($company->category); ?></p>
                    <?php endif; ?>
                  </div>

                  <!-- زر التصويت -->
                  <div class="ms-auto mt-2 mt-md-0" style="flex:0 0 auto;">
                    <a href="<?php echo e(route('web.company_details', $company->name_en)); ?>"
                       class="btn custom-button-white vote-btn" style="padding:8px 14px; white-space:nowrap;">
                      <?php echo e(__('Vote')); ?>

                    </a>
                  </div>
                </div>
              </div>
            </div>
          </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </div>

      <div class="mt-1">
        <button type="button" class="js-load-more"><?php echo e(__('Load More')); ?></button>
        <div class="js-all-shown"><?php echo e(__('All items are shown')); ?></div>
      </div>
    </div>
  </section>
</div>

<!-- ================= DESKTOP SECTION ================= -->
<section id="desktopSection" class="desktop-only section bg-default parallax-container mb-5">
  <div class="parallax-content section-lg context-dark text-center">
    <div class="section-title text-center mb-5 mt-5">
      <h2 class="section-title__title">
        <span id="typed-title" data-typing="desktop"></span>
      </h2>
    </div>

    <!-- (اختياري) صندوق بحث للديسكتوب -->
    <!--
    <div class="row justify-content-end mt-3 mb-4 px-4">
      <div class="col-md-4 search-container">
        <input type="text" class="form-control js-search" placeholder="<?php echo e(__('Search by name, description...')); ?>" autocomplete="off">
      </div>
    </div>
    -->

    <div class="js-no-results"><?php echo e(__('No results found')); ?></div>

    <div class="js-wrapper">
      <?php $__currentLoopData = $companies; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $idx => $company): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php
          $searchData = strtolower(trim(
            ($company->name_en ?? '') . ' ' .
            ($company->category ?? '') . ' ' .
            ($company->regulation ?? '') . ' ' .
            ($company->description_en ?? '') . ' ' .
            ($company->company ?? '')
          ));
        ?>

        <div class="row justify-content-center company-row" data-search="<?php echo e(e($searchData)); ?>" data-index="<?php echo e($idx); ?>">
          <div class="col-md-10">
            <div class="card mb-4 p-3 shadow company-card" data-aos="fade-up" data-aos-delay="100">
              <div class="row g-3 align-items-center company-card-inner">
                <!-- صورة -->
                <div class="col-md-3 text-center">
                  <div style="width:160px;height:160px;border-radius:50%;
                              background:linear-gradient(135deg,#000000,#cc252e);
                              padding:5px;display:flex;align-items:center;justify-content:center;margin:auto;">
                    <div style="width:100%;height:100%;border-radius:50%;overflow:hidden;background:#fff;
                                display:flex;align-items:center;justify-content:center;">
                      <img src="<?php echo e(asset('public/'.$company->image)); ?>" alt="<?php echo e($company->name_en); ?>"
                           style="width:100%;height:100%;object-fit:cover;" loading="lazy" decoding="async" fetchpriority="low">
                    </div>
                  </div>
                </div>

                <!-- تفاصيل -->
                <div class="col-md-7 text-start">
                  <h4 class="mb-2 stat-number" style="font-size:35px;"><?php echo e($company->name_en); ?></h4>
                  <?php if($company->category): ?>
                    <p class="fw-bold"><?php echo e($company->category); ?></p>
                  <?php endif; ?>
                </div>

                <!-- زر -->
                <div class="col-md-2 d-flex align-items-start justify-content-end mt-3 mt-md-0 pe-5">
                  <a href="<?php echo e(route('web.company_details', $company->name_en)); ?>"
                     class="btn custom-button-white px-4 py-3 fs-5">
                    <?php echo e(__('Vote')); ?>

                  </a>
                </div>

              </div>
            </div>
          </div>
        </div>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>

    <div class="mt-2">
      <button type="button" class="js-load-more"><?php echo e(__('Load More')); ?></button>
      <div class="js-all-shown"><?php echo e(__('All items are shown')); ?></div>
    </div>
  </div>
</section>

<link href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>

<!-- ================= SCRIPTS ================= -->
<script>
(function(){
  // Init AOS (محمي)
  document.addEventListener('DOMContentLoaded', function(){
    if (window.AOS && typeof AOS.init === 'function') AOS.init({ once: false });
  });

  // تأثير الكتابة—مرة واحدة عند الظهور
  function setupTyping(el, text){
    if (!el || el.dataset.init === '1') return;
    el.dataset.init = '1';

    const prefersReduced = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    if (prefersReduced) { el.textContent = text; el.classList.add('done','finished'); return; }

    let i = 0, started = false;
    function typeChar(){
      if (i < text.length){
        el.textContent += text.charAt(i++);
        setTimeout(typeChar, 110);
      } else {
        el.classList.add('done','finished');
      }
    }
    function isInViewport(node){
      const r = node.getBoundingClientRect();
      return r.top < window.innerHeight && r.bottom >= 0;
    }
    function onScroll(){
      if (!started && isInViewport(el)){ started = true; typeChar(); }
    }
    window.addEventListener('scroll', onScroll, { passive: true });
    onScroll();
  }

  // طبّق الكتابة على عناصر data-typing
  document.addEventListener('DOMContentLoaded', function(){
    const TYPING_TEXT = "The best 100 influencer voting";
    document.querySelectorAll('[data-typing]').forEach(el => setupTyping(el, TYPING_TEXT));
  });

  // Search + Load More — Scoped لكل سكشن
  function initList(section){
    const wrapper  = section.querySelector('.js-wrapper');
    const loadBtn  = section.querySelector('.js-load-more');
    const allNote  = section.querySelector('.js-all-shown');
    const input    = section.querySelector('.js-search'); // اختياري
    const noRes    = section.querySelector('.js-no-results');

    if (!wrapper) return;

    const INITIAL_COUNT = 8;
    const BATCH_SIZE    = 8;

    // دعم .company-row و .company-row-wrap
    const allRows = Array.from(wrapper.querySelectorAll('.company-row, .company-row-wrap'));
    let filteredRows = [...allRows];
    let shown = 0;

    function hideAll(list){ list.forEach(r => r.classList.add('hidden')); }
    function showNext(batch){
      const toShow = filteredRows.slice(shown, shown + batch);
      toShow.forEach(r => r.classList.remove('hidden'));
      shown += toShow.length;
      if (window.AOS && (AOS.refreshHard || AOS.refresh)){
        if (AOS.refreshHard) AOS.refreshHard(); else AOS.refresh();
      }
      updateControls();
    }
    function updateControls(){
      const anyHiddenLeft = shown < filteredRows.length;
      if (loadBtn) loadBtn.style.display = anyHiddenLeft ? '' : 'none';
      if (allNote) allNote.style.display = (!anyHiddenLeft && filteredRows.length > 0) ? '' : 'none';

      const anyVisible = filteredRows.some(r => !r.classList.contains('hidden'));
      if (noRes) noRes.style.display = anyVisible ? 'none' : 'block';
    }
    function resetBrowse(){
      filteredRows = [...allRows];
      hideAll(allRows);
      shown = 0;
      showNext(INITIAL_COUNT);
    }
    function applySearch(q){
      const needle = (q || '').toLowerCase().trim();
      filteredRows = allRows.filter(r => (r.getAttribute('data-search') || '').includes(needle));
      hideAll(allRows);
      shown = 0;
      showNext(INITIAL_COUNT);
      if (window.scrollX > 0) window.scrollTo({ left: 0 });
    }

    // بداية: عرض أول دفعة
    hideAll(allRows);
    showNext(INITIAL_COUNT);

    // Load More
    if (loadBtn && !loadBtn.dataset.bound){
      loadBtn.dataset.bound = '1';
      loadBtn.addEventListener('click', function(){ showNext(BATCH_SIZE); });
    }

    // البحث (لو موجود)
    let t = null;
    if (input && !input.dataset.bound){
      input.dataset.bound = '1';
      input.addEventListener('input', function(){
        clearTimeout(t);
        t = setTimeout(() => {
          const q = input.value || '';
          if (!q.trim()) resetBrowse(); else applySearch(q);
        }, 160);
      });
    }
  }

  // فعّل لكل سكشن (موبايل + ديسكتوب)
  document.addEventListener('DOMContentLoaded', function(){
    document.querySelectorAll('#mobileSection, #desktopSection').forEach(initList);
  });
})();
</script>
<?php /**PATH /home/u552947370/domains/affiliatesummitglobal.com/public_html/resources/views/web/content/sections/voting.blade.php ENDPATH**/ ?>