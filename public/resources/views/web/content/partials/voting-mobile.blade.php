<style>
  @import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@700&display=swap');

  /* --- قتل أي Overflow أفقي من المصدر --- */
  html, body { overflow-x: hidden; }
  .mobile-only-page,
  .parallax-content { max-width: 100%; overflow-x: hidden; }
  /* سلوك flex: اسمح بالانكماش داخل الصفوف */
  .company-row, .company-card-inner, .d-flex { min-width: 0; }
  .company-content { min-width: 0; }
  .company-content h4, .company-content p, .company-content a {
    overflow: hidden; text-overflow: ellipsis; white-space: nowrap;
  }
  /* صور آمنة */
  img { max-width: 100%; height: auto; display: block; }

  /* صفوف Bootstrap خارج container قد تعمل سالب margins → overflow */
  .parallax-content .row {
    margin-right: 0 !important;
    margin-left: 0 !important;
  }
  /* لو حابب تحافظ على مسافات داخلية للـrow بدون سالب: */
  .parallax-content .row > [class^="col"] { padding-left: 12px; padding-right: 12px; }

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

    .vote-btn {
      font-size: 15px !important; padding: 10px 20px !important; white-space: nowrap;
      background: linear-gradient(90deg, #fff 0%, #fff 100%); color:#cc252e
    }
  }

  @media (min-width: 768px) {
    .company-image-mobile { display: none !important; }
  }

  /* البحث */
  .search-container .form-control {
      background-color: rgba(0, 0, 0, 0.04);
      color: #111;
      border: 1px solid rgba(0,0,0,0.1);
      width: 100%;
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

  /* زر "Load More" */
  #loadMoreBtn{
      background-color: var(--e-global-color-primary, #cc252e);
      color: #fff; font-weight:700; border:none; padding:12px 28px; border-radius:10px;
      box-shadow:0 8px 20px rgba(0,0,0,.12)
  }
  #allShownNote{ display:none; color:#666; margin-top:8px; }
</style>

@php $locale = app()->getLocale(); @endphp

<div class="mobile-only-page">
  <section class="section bg-default parallax-container mb-5"><!-- أصلحت arallax-container -> parallax-container -->
    <div class="parallax-content section-lg context-dark text-center" style="background-color:#fff;">
      <div class="section-title text-center">
        <h2 class="section-title__title" style="padding-top:10px;">
          <span id="typingTextw" class="typing-effect-container"></span>
        </h2>
      </div>

      {{-- البحث --}}
      <div class="row justify-content-center mt-3 mb-3 px-3">
        <div class="col-12 col-sm-10 col-md-6 search-container">
          <input type="text" id="searchInput" class="form-control"
                 placeholder="{{ __('Search by name, description...') }}" autocomplete="off">
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
                <div class="d-flex flex-md-row company-row" style="min-width:0;">
                  {{-- صورة Desktop --}}
                  <div class="company-image-desktop me-3 d-none d-md-block" style="flex:0 0 auto;">
                    <div style="width:160px; height:160px; border-radius:50%; overflow:hidden;">
                      <img src="{{ asset('public/'.$company->image) }}" alt="{{ $company->name_en }}"
                           style="width:100%; height:100%; object-fit:cover;" loading="lazy" decoding="async" fetchpriority="low">
                    </div>
                  </div>

                  {{-- صورة Mobile --}}
                  <div class="company-image-mobile me-2" style="flex:0 0 auto;">
                    <div class="company-image-mobile-inner">
                      <img src="{{ asset('public/'.$company->image) }}" alt="{{ $company->name_en }}"
                           loading="lazy" decoding="async" fetchpriority="low">
                    </div>
                  </div>

                  {{-- المحتوى --}}
                  <div class="company-content text-start flex-grow-1" style="min-width:0;">
                    <h4 class="stat-number" style="font-size:20px;">{{ $company->name_en }}</h4>
                    @if($company->category)
                      <p class="fw-bold stat-number" style="font-size:14px;">{{ $company->category }}</p>
                    @endif
                  </div>

                  {{-- زر التصويت --}}
                  <div class="ms-auto mt-2 mt-md-0" style="flex:0 0 auto;">
                    <a href="{{ route('web.company_details', $company->name_en) }}"
                       class="btn custom-button-white vote-btn" style="padding:8px 14px; white-space:nowrap;">
                        {{ __('Vote') }}
                    </a>
                  </div>
                </div>
              </div>
            </div>
          </div>
        @endforeach
      </div>

      {{-- عناصر التحكم بالعرض التدريجي --}}
      <div class="mt-1" style="overflow-x:hidden;">
        <button id="loadMoreBtn" type="button">{{ __('Load More') }}</button>
        <div id="allShownNote">{{ __('All items are shown') }}</div>
      </div>
    </div>
  </section>
</div>

<link href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>

<script>
/* Init AOS (محمي) */
document.addEventListener('DOMContentLoaded', function(){
  if (window.AOS && typeof AOS.init === 'function') AOS.init({ once:false });
});

/* Typing effect (target: typingTextw) */
document.addEventListener("DOMContentLoaded", function () {
  const target = document.getElementById("typingTextw");
  if (!target || target.dataset.init === '1') return;
  target.dataset.init = '1';

  const text = "The best 100 influencer voting";
  let index = 0, isAnimated = false;

  function typeChar() {
    if (index < text.length) {
      target.textContent += text.charAt(index++);
      setTimeout(typeChar, 120);
    } else { target.classList.add("done"); }
  }
  function isInViewport(el) {
    const r = el.getBoundingClientRect();
    return r.top < window.innerHeight && r.bottom >= 0;
  }
  function handleScroll() {
    if (!isAnimated && isInViewport(target)) { isAnimated = true; typeChar(); }
  }
  window.addEventListener("scroll", handleScroll, { passive: true });
  handleScroll();
});

/* Load More + Search (بدون إعادة بناء/ بلا overflow) */
(function(){
  const INITIAL_COUNT = 8;
  const BATCH_SIZE    = 8;

  const wrapper  = document.getElementById('companiesWrapper');
  const rows     = Array.from(wrapper.querySelectorAll('.company-row-wrap'));
  const loadBtn  = document.getElementById('loadMoreBtn');
  const allNote  = document.getElementById('allShownNote');
  const input    = document.getElementById('searchInput');
  const noRes    = document.getElementById('noResults');

  if (!wrapper || rows.length === 0) return;

  let mode  = 'browse';   // browse | search

  function hideAll(){ rows.forEach(r => r.style.display = 'none'); }
  function matchesQuery(r, q){ return (r.getAttribute('data-search')||'').includes(q); }
  function visibleCount(){ return rows.filter(r => r.style.display !== 'none').length; }

  function showNextBatch(batch){
    const q = (input.value||'').toLowerCase().trim();
    let hidden = rows.filter(r => r.style.display === 'none');
    if (mode === 'search' && q) hidden = hidden.filter(r => matchesQuery(r, q));
    hidden.slice(0, batch).forEach(r => { r.style.display = ''; });
    if (window.AOS && AOS.refreshHard) AOS.refreshHard(); else if (window.AOS) AOS.refresh();
    updateControls();
  }

  function updateControls(){
    const q = (input.value||'').toLowerCase().trim();
    const anyHidden =
      mode === 'search' && q
        ? rows.some(r => matchesQuery(r, q) && r.style.display === 'none')
        : rows.some(r => r.style.display === 'none');

    loadBtn.style.display = anyHidden ? '' : 'none';
    allNote.style.display = anyHidden ? 'none' : '';

    if (noRes) noRes.style.display = visibleCount() ? 'none' : 'block';
  }

  function resetBrowse(){
    mode = 'browse';
    hideAll();
    showNextBatch(INITIAL_COUNT);
  }

  /* بداية العرض */
  hideAll();
  showNextBatch(INITIAL_COUNT);

  /* Load More */
  loadBtn.addEventListener('click', function(){ showNextBatch(BATCH_SIZE); });

  /* البحث (debounce) */
  let t = null;
  input.addEventListener('input', function(){
    clearTimeout(t);
    t = setTimeout(() => {
      const q = (input.value||'').toLowerCase().trim();
      mode = q ? 'search' : 'browse';

      hideAll();

      if (!q) { resetBrowse(); return; }

      const matches = rows.filter(r => matchesQuery(r, q));
      matches.slice(0, INITIAL_COUNT).forEach(r => r.style.display = '');
      if (window.AOS && AOS.refreshHard) AOS.refreshHard(); else if (window.AOS) AOS.refresh();
      updateControls();

      /* لمنع أي تذبذب Scroll يسبب overflow لحظي */
      if (window.scrollX > 0) window.scrollTo({ left: 0 });
    }, 150);
  });
})();
</script>
