<style>
/* عنوان "Typing" */
.typing-effect-container {
  font-size: 36px;
  font-weight: bold;
  background: linear-gradient(90deg, #FFE986 0%, #C48127 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  display: inline-block;
  white-space: nowrap;
  overflow: hidden;
  border-right: 3px solid #FFE986;
  padding-right: 5px;
  width: fit-content;
  margin: 0 auto;
  animation: blink-caret 0.8s step-end infinite;
}
.typing-effect-container.done { animation: none; border-right: none; }
@keyframes blink-caret { from,to{border-color:transparent} 50%{border-color:#FFE986} }

/* خانة البحث */
.search-container .form-control {
  background-color: rgba(255,255,255,0.1);
  color: #fff;
  border: 1px solid rgba(255,255,255,0.3);
}
.search-container .form-control::placeholder { color: rgba(255,255,255,0.6); }

/* كارت الشركة */
.company-card {
  background: linear-gradient(145deg, #f4f2ee 0%, #f4f2ee 50%, #cc252e 100%);
  color: white;
  padding: 10px 20px;
  border-radius: 8px;
  cursor: pointer;
  box-shadow: 0 8px 20px rgba(0,0,0,0.18), 0 2px 6px rgba(0,0,0,0.12);
  transition: box-shadow .2s ease, transform .2s ease;
}
.company-card:hover {
  box-shadow: 0 14px 32px rgba(0,0,0,0.22), 0 4px 12px rgba(0,0,0,0.18);
  transform: translateY(-2px);
}
.company-card-inner {
  background:linear-gradient(145deg, #f4f2ee 0%, #f4f2ee 50%, #cc252e 100%);
  border: none; padding: 0; border-radius: 12px;
}

/* لا توجد نتائج */
#noResults { display:none; color:#fff; opacity:.85; margin:12px 0 24px; }

/* زر التصويت */
.vote-btn {
  background: linear-gradient(90deg, #FFE986 0%, #C48127 100%);
  color: white; font-size: 22px; padding: 10px 45px; border: none;
}
.stat-number {
  margin-bottom: 10px;
  background: linear-gradient(270deg, #000000, #cc252e, #000000);
  background-size: 600% 600%;
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  animation: gradientShift 5s ease infinite;
}
@keyframes gradientShift {
  0%{background-position:0% 50%}
  50%{background-position:100% 50%}
  100%{background-position:0% 50%}
}

/* أداة إخفاء نظيفة */
.hidden { display: none !important; }

/* Sentinel لمراقبة أسفل القائمة */
#scrollSentinel { width: 100%; height: 1px; }
/* خانة البحث — تحسين التباين + ظل */
.search-container .form-control {
  background: rgba(255,255,255,0.95);       /* خلفية فاتحة وواضحة */
  color: #111;                               /* نص داكن وواضح */
  border: 1px solid rgba(0,0,0,0.12);
  box-shadow: 0 6px 20px rgba(0,0,0,0.18);   /* ظل واضح */
  backdrop-filter: blur(2px);
  -webkit-backdrop-filter: blur(2px);
  transition: box-shadow .2s ease, border-color .2s ease, background-color .2s ease;
}
.search-container .form-control::placeholder {
  color: #555;                               /* Placeholder أوضح */
  opacity: .9;
}
.search-container .form-control:focus {
  outline: none;
  border-color: #C48127;
  box-shadow: 0 10px 28px rgba(0,0,0,0.22), 0 2px 8px rgba(0,0,0,0.15); /* ظل أقوى عند التركيز */
  background: #fff;
}

</style>

<section class="section bg-default parallax-container mb-5">
  <div class="parallax-content section-lg context-dark text-center">

    <div class="section-title text-center mb-5 mt-5">
      <h2 class="section-title__title">
        <span id="typingText" class="promo-title"
          style="font-size:60px; font-weight:900; line-height:1.2; margin-bottom:10px;
                 background:linear-gradient(270deg,#000000,#E73701,#000000);
                 background-size:600% 600%;
                 -webkit-background-clip:text; -webkit-text-fill-color:transparent;
                 animation:gradientShift 5s ease infinite;"></span>
      </h2>
    </div>

    <!-- Search Box -->
    <div class="row justify-content-end mt-3 mb-4 px-4">
      <div class="col-md-4 search-container">
        <input
          type="text"
          id="searchInput"
          class="form-control"
          placeholder="<?php echo e(__('Search by name, description...')); ?>"
          autocomplete="off">
      </div>
    </div>

    <!-- No results -->
    <div id="noResults"><?php echo e(__('No results found')); ?></div>

    <!-- Companies Wrapper -->
    <div id="companiesWrapper">
      <?php $__currentLoopData = $companies; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $idx => $company): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php
          $searchData = strtolower(
            trim(
              ($company->name_en ?? '') . ' ' .
              ($company->category ?? '') . ' ' .
              ($company->regulation ?? '') . ' ' .
              ($company->description_en ?? '') . ' ' .
              ($company->company ?? '')
            )
          );
        ?>

        <div class="row justify-content-center company-row"
             data-search="<?php echo e(e($searchData)); ?>"
             data-index="<?php echo e($idx); ?>">
          <div class="col-md-10">
            <div class="card mb-4 p-3 shadow company-card" data-aos="fade-up" data-aos-delay="100">
              <div class="row g-3 align-items-center company-card-inner">

                <!-- Image -->
                <div class="col-md-3 text-center">
                  <div style="width:160px;height:160px;border-radius:50%;
                              background:linear-gradient(135deg,#000000,#cc252e);
                              padding:5px;display:flex;align-items:center;justify-content:center;margin:auto;">
                    <div style="width:100%;height:100%;border-radius:50%;overflow:hidden;background:#fff;
                                display:flex;align-items:center;justify-content:center;">
                      <img src="<?php echo e(asset('public/'.$company->image)); ?>"
                           alt="<?php echo e($company->name_en); ?>"
                           style="width:100%;height:100%;object-fit:cover;">
                    </div>
                  </div>
                </div>

                <!-- Details -->
                <div class="col-md-7 text-start">
                  <h4 class="mb-2 stat-number" style="font-size:35px;">
                    <?php echo e($company->name_en); ?>

                  </h4>
                </div>

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

    <!-- Sentinel لمراقبة أسفل القائمة -->
    <div id="scrollSentinel" aria-hidden="true"></div>

  </div>
</section>

<link href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>

<script>
/* =========================
   AOS Bootstrap (مع أسماء فريدة)
   ========================= */
(function vfx_aos_bootstrap(){
  document.addEventListener('DOMContentLoaded', function vfx_on_ready(){
    if (window.AOS && typeof window.AOS.init === 'function') {
      window.AOS.init({ once: false });
    }
  });
})();

/* =========================
   Typing Effect (أسماء مميزة)
   ========================= */
(function vfx_typing_effect(){
  document.addEventListener('DOMContentLoaded', function vfx_typing_ready(){
    const vfx_el = document.getElementById('typingText');
    if (!vfx_el || vfx_el.dataset.vfxInit === '1') return;
    vfx_el.dataset.vfxInit = '1';

    const vfx_text = 'The best 100 influencer voting';
    let vfx_idx = 0;
    let vfx_started = false;

    function vfx_type_next() {
      if (vfx_idx < vfx_text.length) {
        vfx_el.textContent += vfx_text.charAt(vfx_idx++);
        setTimeout(vfx_type_next, 120);
      } else {
        vfx_el.classList.add('done');
      }
    }
    function vfx_in_view(el) {
      const r = el.getBoundingClientRect();
      return r.top < window.innerHeight && r.bottom >= 0;
    }
    function vfx_scroll_handler() {
      if (!vfx_started && vfx_in_view(vfx_el)) {
        vfx_started = true;
        vfx_type_next();
      }
    }
    window.addEventListener('scroll', vfx_scroll_handler, { passive: true });
    vfx_scroll_handler();
  });
})();

/* ==========================================
   Infinite Scroll + Search (أسماء مميّزة)
   ========================================== */
(function is2_infinite_search(){
  const IS2_INITIAL = 8;
  const IS2_BATCH   = 8;

  const is2_wrapper  = document.getElementById('companiesWrapper');
  const is2_input    = document.getElementById('searchInput');
  const is2_nores    = document.getElementById('noResults');
  const is2_sentinel = document.getElementById('scrollSentinel');

  if (!is2_wrapper || !is2_sentinel) return;

  const is2_allRows = Array.from(is2_wrapper.querySelectorAll('.company-row'));
  let   is2_filtered = [...is2_allRows];
  let   is2_visibleN = 0;

  /* Helpers (أسماء جديدة) */
  const is2_hideEl   = (el) => el.classList.add('hidden');
  const is2_showEl   = (el) => el.classList.remove('hidden');
  const is2_hideAll  = (list) => list.forEach(is2_hideEl);

  const is2_refreshAOS = () => {
    if (window.AOS && typeof window.AOS.refreshHard === 'function') window.AOS.refreshHard();
    else if (window.AOS && typeof window.AOS.refresh === 'function') window.AOS.refresh();
  };

  const is2_anyHiddenLeft = () => is2_visibleN < is2_filtered.length;

  const is2_updateEmpty = () => {
    const anyVisible = is2_filtered.some(r => !r.classList.contains('hidden'));
    is2_nores.style.display = anyVisible ? 'none' : 'block';
  };

  function is2_showRange(from, to) {
    for (let k = from; k < to; k++) is2_showEl(is2_filtered[k]);
    is2_refreshAOS();
  }

  function is2_showNext(size) {
    if (!is2_anyHiddenLeft()) return;
    const from = is2_visibleN;
    const to   = Math.min(is2_visibleN + size, is2_filtered.length);
    is2_showRange(from, to);
    is2_visibleN = to;
    is2_updateEmpty();
    if (!is2_anyHiddenLeft() && is2_obs) is2_obs.disconnect();
  }

  function is2_resetAndFirst() {
    is2_hideAll(is2_allRows);
    is2_visibleN = 0;
    is2_showNext(IS2_INITIAL);
    is2_updateEmpty();
  }

  function is2_applySearch(q) {
    const needle = (q || '').toLowerCase().trim();
    is2_filtered = is2_allRows.filter(r => (r.dataset.search || '').includes(needle));
    is2_resetAndFirst();

    if (window.scrollY > 200) {
      window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    if (is2_obs) {
      is2_obs.disconnect();
      if (is2_anyHiddenLeft()) is2_obs.observe(is2_sentinel);
    }
  }

  /* بداية العرض */
  is2_hideAll(is2_allRows);
  is2_showNext(IS2_INITIAL);

  /* IntersectionObserver بأسماء مختلفة */
  let is2_obs = null;
  if ('IntersectionObserver' in window) {
    is2_obs = new IntersectionObserver((entries) => {
      entries.forEach(ent => {
        if (ent.isIntersecting && is2_anyHiddenLeft()) {
          is2_showNext(IS2_BATCH);
        }
      });
    }, { root: null, rootMargin: '0px 0px 400px 0px', threshold: 0 });
    is2_obs.observe(is2_sentinel);
  } else {
    /* Fallback Scroll */
    window.addEventListener('scroll', function is2_onScrollFallback(){
      const nearBottom = window.innerHeight + window.scrollY >= document.body.offsetHeight - 400;
      if (nearBottom && is2_anyHiddenLeft()) is2_showNext(IS2_BATCH);
    }, { passive: true });
  }

  /* بحث مع debounce بأسماء جديدة */
  let is2_debTimer = null;
  is2_input?.addEventListener('input', function is2_onInput(){
    clearTimeout(is2_debTimer);
    is2_debTimer = setTimeout(() => is2_applySearch(is2_input.value || ''), 160);
  });
})();
</script>
<?php /**PATH /home/u552947370/domains/affiliatesummitglobal.com/public_html/resources/views/web/content/partials/voting_partials/voting-desktop.blade.php ENDPATH**/ ?>