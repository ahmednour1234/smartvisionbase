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
    border: 3px solid #3c2a1e;
    background: linear-gradient(90deg, #3c2a1e 0%, #1a120b 70%);
    color: white;
    padding: 10px 20px;
    border-radius: 8px;
    cursor: pointer;
}
.company-card-inner {
    background: linear-gradient(90deg, #3c2a1e 0%, #1a120b 70%);
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
</style>

<section class="section bg-default arallax-container">


    <!-- No results -->
    <div id="noResults">{{ __('No results found') }}</div>

    <!-- Companies -->
    <div id="companiesWrapper">
      @foreach ($companies as $company)
        @php
          // نجمع نص قابل للبحث (اسم/تصنيف/قوانين/وصف/شركة...)
          $searchData = strtolower(
            trim(
              ($company->name_en ?? '') . ' ' .
              ($company->category ?? '') . ' ' .
              ($company->regulation ?? '') . ' ' .
              ($company->description_en ?? '') . ' ' .
              ($company->company ?? '')
            )
          );
        @endphp

        <div class="row justify-content-center company-row" data-search="{{ e($searchData) }}">
          <div class="col-md-10">
            <div class="card mb-4 p-3 shadow wow fadeInUp company-card" data-wow-delay="100ms">
              <div class="row g-3 align-items-center company-card-inner">

                <!-- Image -->
                <div class="col-md-3 text-center">
                  <div style="
                      width:160px;height:160px;border-radius:50%;
                      background:linear-gradient(135deg,#FFE986,#C48127);
                      padding:5px;display:flex;align-items:center;justify-content:center;margin:auto;">
                    <div style="
                        width:100%;height:100%;border-radius:50%;overflow:hidden;background:#fff;
                        display:flex;align-items:center;justify-content:center;">
                      <img src="{{ asset('public/'.$company->image) }}"
                           alt="{{ $company->name_en }}"
                           style="width:100%;height:100%;object-fit:cover;">
                    </div>
                  </div>
                </div>

                <!-- Details -->
                <div class="col-md-7 text-start">
                  <h4 class="mb-2 text-white" style="font-size:38px;">
                    {{ $company->name_en }}
                  </h4>

                  <!-- Stars -->
               @php
  // قاعدة العرض:
  // 1) لو orders > 100 ⇒ 3 نجوم و Category = "Average"
  // 2) غير كده: لو Category = "Top 100 Member" ⇒ 5 نجوم
  // 3) غير كده: استخدم قيمة stars كما هي
  $displayCategory = $company->category ?? null;

  if (isset($company->orders) && (int)$company->orders > 100) {
      $rating = 3;
      $displayCategory = 'Average';
  } else {
      $forceFive = isset($company->category) && strcasecmp(trim($company->category), 'Top 100 Member') === 0;
      $rating    = $forceFive ? 5 : (float)($company->stars ?? 0);
  }
@endphp

<div class="mb-2">
  @for ($i = 1; $i <= 5; $i++)
    @php
      $class = $rating >= $i
        ? 'fas fa-star'
        : ($rating >= ($i - 0.5) ? 'fas fa-star-half-alt' : 'far fa-star');
    @endphp
    <i class="{{ $class }} text-warning"></i>
  @endfor
</div>

@if($displayCategory)
  <p class="mb-1 fw-bold text-white">{{ $displayCategory }}</p>
@endif

                  @if($company->regulation)
                    <p style="color:white;">Regulation: {{ $company->regulation }}</p>
                  @endif

                  @if ($company->link)
                    <a href="{{ $company->link }}" target="_blank" class="d-inline-block mt-1" style="color:white;">
                      Visit Website
                    </a>
                  @endif
                </div>

                <!-- Vote Button -->
                <div class="col-md-2 d-flex align-items-start justify-content-end mt-3 mt-md-0 pe-4">
                  <a href="{{ route('web.company_details', $company->name_en) }}" class="btn vote-btn">
                    {{ __('Vote') }}
                  </a>
                </div>

              </div>
            </div>
          </div>
        </div>
      @endforeach
    </div>
  </div>
</section>

<link href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>

<script>
(function(){
  const btn = document.getElementById('loadMoreBtn');
  if(!btn) return;

  const endpoint = @json(route('voting.loadMore'));

  // دالة تنظف أي عناصر غير الكروت قبل الإضافة
  function safeAppend(holder, html){
    const temp = document.createElement('div');
    temp.innerHTML = html.trim();

    // شيل أي عناصر مفروض تكون ثابتة وما تتكرر
    temp.querySelectorAll('h2.section-title__title, .search-container, #searchInput, .row.justify-content-end.mt-3.mb-4.px-4').forEach(n => n.remove());

    // أضف فقط العناصر المباشرة (الكروت = .col-...)
    const frag = document.createDocumentFragment();
    Array.from(temp.children).forEach(el => frag.appendChild(el));
    holder.appendChild(frag);
  }

  btn.addEventListener('click', async function(){
    const page    = parseInt(btn.dataset.page || '2', 10);
    const perPage = parseInt(btn.dataset.per  || '25', 10);

    btn.disabled = true; btn.textContent = 'Loading...';

    try {
      const res  = await fetch(`${endpoint}?page=${page}&per_page=${perPage}`, {
        headers: { 'X-Requested-With':'XMLHttpRequest', 'Accept':'application/json' }
      });
      if (!res.ok) throw new Error('HTTP '+res.status);

      const json = await res.json();
      const isDesktop = window.matchMedia('(min-width: 768px)').matches;

      if (isDesktop) {
        const holder = document.getElementById('companiesWrapper');
        safeAppend(holder, json.desktop);
      } else {
        const holder = document.getElementById('mobile-list');
        safeAppend(holder, json.mobile);
      }

      if (json.hasMore) {
        btn.dataset.page = json.nextPage;
        btn.disabled = false; btn.textContent = 'Load more';
      } else {
        btn.remove();
      }
    } catch (e) {
      btn.disabled = false; btn.textContent = 'Load more';
      alert('Network error, please try again.');
    }
  });
})();
</script>
