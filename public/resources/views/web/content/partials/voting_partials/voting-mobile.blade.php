<style>
  @import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@700&display=swap');

  .mobile-only-page { display: none; }
  @media (max-width: 767.98px) { .mobile-only-page { display: block; } }

  /* موبايل */
  @media (max-width: 767.98px) {
    #typed-title { font-size: 32px; }

    .company-row { flex-direction: row !important; align-items: center !important; gap: 10px; }
    .company-card-wrapper { padding: 10px 12px !important; }

    .company-image-desktop { display: none !important; }

    .company-image-mobile {
      display: block !important;
      width: 80px; height: 80px; border-radius: 50%;
      background:linear-gradient(to right, #000000, #cc252e);
      padding: 3px; flex-shrink: 0;
    }
    .company-image-mobile-inner { width: 100%; height: 100%; border-radius: 50%; overflow: hidden; background: #fff; }
    .company-image-mobile-inner img { width: 100%; height: 100%; object-fit: cover; }

    .company-content h4 { font-size: 16px !important; margin-bottom: 4px; }
    .company-content p, .company-content a { font-size: 13px; margin-bottom: 2px; }

    .vote-btn { font-size: 15px !important; padding: 10px 20px !important; white-space: nowrap;  background: linear-gradient(90deg, #fff 0%, #fff 100%);color:#cc252e }
  }

  @media (min-width: 768px) {
    .mobile-only-page { display: none !important; }
    .company-image-mobile { display: none !important; }
  }

  /* البحث */
  .search-container .form-control {
    background-color: rgba(0, 0, 0, 0.04);
    color: #111;
    border: 1px solid rgba(0,0,0,0.1);
  }
  .search-container .form-control::placeholder { color: rgba(0,0,0,0.45); }
  .search-container .btn { background-color: #cc252e; border: none; }

  /* تأثير الكتابة */
  @keyframes blink-caret { 0%,100%{border-color:transparent} 50%{border-color:#FFE986} }
  .typing-effect-container{
    font-size: 30px; font-weight: bold;
    background:linear-gradient(to right, #000000, #cc252e);
    -webkit-background-clip:text; -webkit-text-fill-color:transparent;
    display:inline-block; white-space:nowrap; overflow:hidden;
    border-right:3px solid #FFE986; padding-right:5px; width:fit-content; margin:0 auto;
    animation: blink-caret 0.8s step-end infinite;
  }
  .typing-effect-container.done{ animation:none; border-right:none; }
  span#typingTextw { font-size: 25px; }

  /* تدرّج العناوين */
  .stat-number {
    margin-bottom: 10px;
    background: linear-gradient(270deg, #000000, #cc252e, #000000);
    background-size: 600% 600%;
    -webkit-background-clip: text; -webkit-text-fill-color: transparent;
    animation: gradientShift 5s ease infinite;
  }
  @keyframes gradientShift {
    0%{background-position:0% 50%} 50%{background-position:100% 50%} 100%{background-position:0% 50%}
  }

  /* لا توجد نتائج */
  #noResults { display:none; color:#555; margin:10px 0 16px; }

  /* إخفاء نظيف */
  .hidden { display: none !important; }

  /* Sentinel لمراقبة أسفل القائمة */
  #scrollSentinel { width:100%; height:1px; }
</style>

@php $locale = app()->getLocale(); @endphp

<div class="mobile-only-page">
  <section class="section bg-default parallax-container mb-5">
    <div class="parallax-content section-lg context-dark text-center" style="background-color:#fff;">
      <div class="section-title text-center">
        <h2 class="section-title__title" style="padding-top:10px;">
          <span id="typingTextw" class="typing-effect-container"></span>
        </h2>
      </div>

      {{-- البحث --}}
      <div class="row justify-content-center mt-3 mb-3 px-3">
        <div class="col-12 col-sm-10 col-md-6 search-container">
          <input type="text" id="searchInput" class="form-control" placeholder="{{ __('Search by name, description...') }}" autocomplete="on">
        </div>
      </div>

      <div id="noResults">{{ __('No results found') }}</div>

      {{-- الشركات --}}
      <div id="companiesWrapper">
        @foreach ($companies as $idx => $company)
          @php
            $searchData = strtolower(trim(
              ($company->name_en ?? '') . ' ' .
              ($company->category ?? '') . ' ' .
              ($company->regulation ?? '') . ' ' .
              ($company->description_en ?? '') . ' ' .
              ($company->company ?? '')
            ));
          @endphp

          <div class="row justify-content-center company-row-wrap" data-search="{{ e($searchData) }}" data-index="{{ $idx }}">
            <div class="col-md-10">
              <div class="card mb-3 p-3 shadow wow fadeInUp company-card-wrapper"
                   style="background:linear-gradient(90deg, #f4f2ee 40%, #cc252e 70%); color:#fff; border-radius:10px;"
                   data-aos="fade-up">
                <div class="d-flex flex-md-row company-row">
                  {{-- صورة Desktop --}}
                  <div class="company-image-desktop me-3 d-none d-md-block">
                    <div style="width:160px; height:160px; border-radius:50%; overflow:hidden;">
                      <img src="{{ asset('public/'.$company->image) }}" alt="{{ $company->name_en }}"
                           style="width:100%; height:100%; object-fit:cover;">
                    </div>
                  </div>

                  {{-- صورة Mobile --}}
                  <div class="company-image-mobile me-2">
                    <div class="company-image-mobile-inner">
                      <img src="{{ asset('public/'.$company->image) }}" alt="{{ $company->name_en }}">
                    </div>
                  </div>

                  {{-- المحتوى --}}
                  <div class="company-content text-start flex-grow-1">
                    <h4 class="stat-number" style="font-size:20px;">{{ $company->name_en }}</h4>
                    @if($company->category)
                      <p class="fw-bold stat-number" style="font-size:14px;">{{ $company->category }}</p>
                    @endif
                  </div>

                  {{-- زر التصويت --}}
                  <div class="ms-auto mt-2 mt-md-0">
                    <a href="{{ route('web.company_details', $company->name_en) }}"
                       class="btn custom-button-white vote-btn" style="padding:8px 14px;">
                      {{ __('Vote') }}
                    </a>
                  </div>
                </div>
              </div>
            </div>
          </div>
        @endforeach
      </div>

      {{-- Sentinel للـ Infinite Scroll --}}
      <div id="scrollSentinel" aria-hidden="true"></div>
    </div>
  </section>
</div>

<link href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>

<script>
/* Init AOS */
document.addEventListener('DOMContentLoaded', function(){ if (window.AOS) AOS.init({ once:false }); });

/* Typing effect (target: typingTextw) — تشغيل عند الظهور */
document.addEventListener('DOMContentLoaded', function () {
  const target = document.getElementById('typingTextw');
  if (!target || target.dataset.init === '1') return;
  target.dataset.init = '1';

  const text = 'The best 100 influencer voting';
  let i = 0, started = false;

  const typeChar = () => {
    if (i < text.length) {
      target.textContent += text.charAt(i++);
      setTimeout(typeChar, 120);
    } else {
      target.classList.add('done');
    }
  };
  const inViewport = (el) => {
    const r = el.getBoundingClientRect();
    return r.top < window.innerHeight && r.bottom >= 0;
  };
  const onScroll = () => {
    if (!started && inViewport(target)) { started = true; typeChar(); }
  };
  window.addEventListener('scroll', onScroll, { passive: true });
  onScroll();
});

/* Infinite Scroll + Search (بحث على الظاهر فقط) */
(function(){
  const INITIAL_COUNT = 8;   // عدد العناصر عند التحميل الأول
  const BATCH_SIZE    = 8;   // عدد العناصر في كل دفعة

  const wrapper  = document.getElementById('companiesWrapper');
  const noRes    = document.getElementById('noResults');
  const input    = document.getElementById('searchInput');
  const sentinel = document.getElementById('scrollSentinel');

  if (!wrapper || !sentinel) return;

  const allRows = Array.from(wrapper.querySelectorAll('.company-row-wrap'));

  let filteredRows = [...allRows]; // قاعدة الـinfinite scroll
  let visibleCount = 0;            // كم عنصر ظاهر من filteredRows
  let searchActive = false;        // وضع البحث على الظاهر فقط

  // Helpers
  const hide = (el) => el.classList.add('hidden');
  const show = (el) => el.classList.remove('hidden');
  const hideAll = (list) => list.forEach(hide);
  const getVisibleRows = () => allRows.filter(r => !r.classList.contains('hidden'));

  const aosRefresh = () => {
    if (window.AOS && typeof AOS.refreshHard === 'function') AOS.refreshHard();
    else if (window.AOS && typeof AOS.refresh === 'function') AOS.refresh();
  };

  const updateNoResults = () => {
    const anyVisible = getVisibleRows().length > 0;
    noRes.style.display = anyVisible ? 'none' : 'block';
  };

  const showSlice = (from, to) => {
    for (let i = from; i < to; i++) show(filteredRows[i]);
    aosRefresh();
  };

  const anyHiddenLeft = () => visibleCount < filteredRows.length;

  const showNextBatch = (size) => {
    if (searchActive) return; // أثناء البحث لا نحمّل دفعات جديدة
    if (!anyHiddenLeft()) return;
    const from = visibleCount;
    const to   = Math.min(visibleCount + size, filteredRows.length);
    showSlice(from, to);
    visibleCount = to;
    updateNoResults();
    if (!anyHiddenLeft()) observer.disconnect();
  };

  // بداية: إخفِ الكل ثم اعرض الدفعة الأولى
  hideAll(allRows);
  showNextBatch(INITIAL_COUNT);

  // IntersectionObserver للتحميل التلقائي
  const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting && !searchActive) {
        showNextBatch(BATCH_SIZE);
      }
    });
  }, { root: null, rootMargin: '0px 0px 400px 0px', threshold: 0 });
  observer.observe(sentinel);

  // Fallback لو ما في IntersectionObserver
  if (!('IntersectionObserver' in window)) {
    window.addEventListener('scroll', function(){
      if (searchActive) return;
      const nearBottom = window.innerHeight + window.scrollY >= document.body.offsetHeight - 400;
      if (nearBottom) showNextBatch(BATCH_SIZE);
    }, { passive: true });
  }

  // منطق البحث على العناصر "الظاهرة فقط"
  // - عند وجود نص بحث: يتم فلترة العناصر الظاهرة حاليًا فقط.
  // - عند مسح البحث: نرجع لنفس حالة العرض (visibleCount) ونوصل المراقب.
  let debounceTimer = null;

  const applyVisibleOnlySearch = (q) => {
    const needle = (q || '').toLowerCase().trim();

    if (needle === '') {
      // إنهاء وضع البحث: رجّع الحالة كما كانت
      searchActive = false;
      // أعد ضبط العرض بحسب visibleCount من filteredRows
      hideAll(allRows);
      showSlice(0, visibleCount);
      updateNoResults();
      // أعد توصيل الـobserver لو في عناصر لسه
      if (anyHiddenLeft()) observer.observe(sentinel);
      return;
    }

    // تفعيل وضع البحث
    searchActive = true;
    observer.disconnect(); // إيقاف تحميل دفعات جديدة أثناء البحث

    // فلترة "العناصر الظاهرة فقط" حاليًا
    const currentlyVisible = getVisibleRows();
    let matchCount = 0;

    currentlyVisible.forEach(row => {
      const hay = (row.dataset.search || '').toLowerCase();
      if (hay.includes(needle)) {
        show(row);
        matchCount++;
      } else {
        hide(row);
      }
    });

    // لا تلمس العناصر المخفية مسبقًا (بتفضل مخفية)
    // تحديث لا توجد نتائج
    noRes.style.display = matchCount > 0 ? 'none' : 'block';

    aosRefresh();
  };

  input?.addEventListener('input', function(){
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(() => applyVisibleOnlySearch(input.value), 160);
  });
})();
</script>
