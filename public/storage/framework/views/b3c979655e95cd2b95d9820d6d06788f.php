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
  border: none;
  padding: 0;
  border-radius: 12px;
}

/* لا توجد نتائج */
#noResults {
  display: none;
  color: #fff;
  opacity: .85;
  margin: 12px 0 24px;
}

/* زر التصويت */
.vote-btn {
  background: linear-gradient(90deg, #FFE986 0%, #C48127 100%);
  color: white;
  font-size: 22px;
  padding: 10px 45px;
  border: none;
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

/* زر "Load More" */
#loadMoreBtn {
  background-color: var(--e-global-color-primary, #cc252e);
  color: white;
  font-weight: 700;
  border: none;
  padding: 12px 28px;
  border-radius: 10px;
  box-shadow: 0 8px 20px rgba(0, 0, 0, .12);
}
#allShownNote{ display:none; color:#fff; opacity:.8; margin-top:10px; }

/* أداة إخفاء نظيفة بدل inline style */
.hidden { display: none !important; }
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

    <!-- Companies Wrapper (الكروت فقط هنا) -->
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
                           style="width:100%;height:100%;object-fit:cover;"    loading="lazy" decoding="async" fetchpriority="low">
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

    <!-- Load More controls (ثابتة خارج الـ wrapper) -->
    <div class="mt-2" id="loadMoreControls">
      <button id="loadMoreBtn" type="button"><?php echo e(__('Load More')); ?></button>
      <div id="allShownNote"><?php echo e(__('All items are shown')); ?></div>
    </div>
  </div>
</section>

<link href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>

<script>
// ========== Init AOS ==========
document.addEventListener('DOMContentLoaded', function(){ AOS.init({ once:false }); });

// ========== Typing effect (محمي من التشغيل مرتين) ==========
document.addEventListener("DOMContentLoaded", function () {
  const target = document.getElementById("typingText");
  if (!target || target.dataset.init === '1') return; // منع تكرار
  target.dataset.init = '1';

  const text = "The best 100 influencer voting";
  let index = 0, started = false;

  function typeChar() {
    if (index < text.length) {
      target.textContent += text.charAt(index++);
      setTimeout(typeChar, 120);
    } else {
      target.classList.add("done");
    }
  }
  function isInViewport(el) {
    const r = el.getBoundingClientRect();
    return r.top < window.innerHeight && r.bottom >= 0;
  }
  function handleScroll() {
    if (!started && isInViewport(target)) { started = true; typeChar(); }
  }
  window.addEventListener("scroll", handleScroll, { passive: true });
  handleScroll();
});

// ========== Load More + Search (بدون تكرار الزر) ==========
(function(){
  const INITIAL_COUNT = 8; // المعروض أول مرة
  const BATCH_SIZE    = 8; // عدد الكروت كل مرة

  const wrapper  = document.getElementById('companiesWrapper');
  const loadBtn  = document.getElementById('loadMoreBtn');
  const allNote  = document.getElementById('allShownNote');
  const input    = document.getElementById('searchInput');
  const noRes    = document.getElementById('noResults');

  if (!wrapper) return;

  // اجمع كل الصفوف مرة واحدة
  const allRows = Array.from(wrapper.querySelectorAll('.company-row'));
  let filteredRows = [...allRows];     // تتغير مع البحث
  let visibleCount = 0;                // كام كارت ظاهر حاليًا

  // أدوات مساعدة
  function hideAll(list){ list.forEach(r => r.classList.add('hidden')); }
  function showNextBatch(batchSize){
    const toShow = filteredRows.slice(visibleCount, visibleCount + batchSize);
    toShow.forEach(r => r.classList.remove('hidden'));
    visibleCount += toShow.length;
    AOS.refreshHard();
    updateControls();
  }
  function updateControls(){
    const anyHiddenLeft = visibleCount < filteredRows.length;
    loadBtn.style.display = anyHiddenLeft ? '' : 'none';
    allNote.style.display = (!anyHiddenLeft && filteredRows.length > 0) ? '' : 'none';

    // لا توجد نتائج
    const anyVisible = filteredRows.some(r => !r.classList.contains('hidden'));
    noRes.style.display = anyVisible ? 'none' : 'block';
  }
  function resetToBrowse(){
    filteredRows = [...allRows];
    hideAll(allRows);
    visibleCount = 0;
    showNextBatch(INITIAL_COUNT);
  }
  function applySearch(q){
    const needle = q.toLowerCase().trim();
    filteredRows = allRows.filter(r => (r.dataset.search || '').includes(needle));
    hideAll(allRows);
    visibleCount = 0;
    showNextBatch(INITIAL_COUNT);
  }

  // بداية: اخفِ الكل ثم اعرض دفعة أولى
  hideAll(allRows);
  showNextBatch(INITIAL_COUNT);

  // Load More (لا ينشئ عناصر جديدة، بس بيكشف المخفي)
  if (loadBtn && !loadBtn.dataset.bound) {
    loadBtn.dataset.bound = '1';
    loadBtn.addEventListener('click', function(){
      showNextBatch(BATCH_SIZE);
    });
  }

  // البحث (debounced)
  let t = null;
  input?.addEventListener('input', function(){
    clearTimeout(t);
    t = setTimeout(() => {
      const q = input.value || '';
      if (!q.trim()) {
        resetToBrowse();
      } else {
        applySearch(q);
      }
    }, 160);
  });

})();
</script>
<?php /**PATH /home/u552947370/domains/affiliatesummitglobal.com/public_html/resources/views/web/content/partials/voting-desktop.blade.php ENDPATH**/ ?>