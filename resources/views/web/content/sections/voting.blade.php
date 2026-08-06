
<style>
    @import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@700&display=swap');

    .search-container .form-control {
        background-color: rgba(255, 255, 255, 0.1);
        color: #fff;
        border: 1px solid rgba(255, 255, 255, 0.3);
    }

    .search-container .form-control::placeholder {
        color: rgba(255, 255, 255, 0.6);
    }

    .search-container .btn {
        background-color: #E73780;
        border: none;
    }

    .page-header__inner {
        text-align: center;
        padding: 60px 0;
    }

    #typed-title {
        display: inline-block;
        overflow: hidden;
        white-space: nowrap;
        border-right: 3px solid #FFE986;
        font-family: 'Montserrat', sans-serif;
        font-size: 60px;
        background: linear-gradient(90deg, #FFE986 0%, #C48127 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        animation: blink-caret 0.75s step-end infinite;
    }

    #typed-title.finished {
        border-right: none;
        animation: none;
    }

    @keyframes blink-caret {
        0%, 100% { border-color: transparent; }
        50% { border-color: #FFE986; }
    }

    .section-title-responsive {
        font-size: 60px;
        width: 80%;
        max-width: 800px;
        font-weight: 800;
        background: linear-gradient(90deg, #fff, #fff);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        margin: 0 auto 40px auto;
        line-height: 1.3;
    }

    @media (max-width: 1365px) {
        #typed-title { font-size: 60px; }
    }

    @media (max-width: 1023px) {
        #typed-title { font-size: 42px; }
    }

    @media (max-width: 767.98px) {
        #typed-title { font-size: 32px; }
        .section-title-responsive { font-size: 37px !important; }
    }

    .desktop-only { display: none; }
    .mobile-only-page { display: none; }

    @media (min-width: 768px) {
        .desktop-only { display: block; }
    }

    @media (max-width: 767.98px) {
        .mobile-only-page { display: block; }
    }
     #searchInput {
    display: none !important;
}
/* حاوية العنوان في المنتصف */
.page-title-center {
  display: flex;
  align-items: center;
  justify-content: center;
  min-height: 180px;       /* مساحة مرئية للتمركز العمودي */
  padding: 40px 0;         /* مسافة علوية/سفلية لطيفة */
  text-align: center;
}

/* حذف أي فراغات غير مرغوبة حول العنوان */
.section-title__title {
  margin: 0;
}

/* طبّق نفس ستايل الكتابة على #typingTextw (بدل #typed-title) */
#typingTextw {
  display: inline-block;
  overflow: hidden;
  white-space: nowrap;
  border-right: 3px solid #FFE986;
  font-family: 'Montserrat', sans-serif;
  font-size: 60px;
  background: linear-gradient(90deg, #FFE986 0%, #C48127 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  animation: blink-caret 0.75s step-end infinite;
}
#typingTextw.finished { border-right: none; animation: none; }

/* استجابات */
@media (max-width: 1365px) { #typingTextw { font-size: 60px; } }
@media (max-width: 1023px) { #typingTextw { font-size: 42px; } }
@media (max-width: 767.98px) {
  #typingTextw { font-size: 32px; }
  .section-title-responsive { font-size: 37px !important; }
}
.page-title-center {
    display: flex
;
    align-items: center;
    justify-content: center;
    min-height: 0px;
    padding: 20px 0;
    text-align: center;
}
</style>


{{-- العنوان --}}
{{-- العنوان في المنتصف --}}
<div class="page-title-center">
  <h2 class="section-title__title">
    <span id="typingTextw" class="typing-effect-container"></span>
  </h2>
</div>

{{-- سطر البحث (ثابت ولا يتكرر) --}}
<div class="row justify-content-end mt-3 mb-4 px-4">
  <div class="col-md-4 search-container">
    <input type="text" id="searchInput" class="form-control" placeholder="Search by name, description...">
  </div>
</div>
<div class="desktop-only">
  <div id="companiesWrapper">
    @include('web.content.partials.voting-desktop', ['companies' => $companies])
  </div>
</div>

<div class="mobile-only-page">
  <div id="mobile-list">
    @include('web.content.partials.voting-mobile', ['companies' => $companies])
  </div>
</div>

@if($companies->hasMorePages())
  <div class="text-center mt-1">
    <button id="loadMoreBtn"
            style="background: linear-gradient(90deg, #FFE986 0%, #C48127 50%); color: white; font-size: 22px; padding: 10px 45px; border: none; border-radius: 20px; margin-bottom: 30px"
            data-page="2"
            data-per="{{ $perPage ?? 25 }}">
      Load more
    </button>
  </div>
@endif



<script>
(function(){
  const btn = document.getElementById('loadMoreBtn');
  if(!btn) return;

  const endpoint = @json(route('voting.loadMore'));

  btn.addEventListener('click', async function(){
    const page    = parseInt(btn.dataset.page || '2', 10);
    const perPage = parseInt(btn.dataset.per  || '25', 10);

    btn.disabled = true; btn.textContent = 'Loading...';

    try {
      const res  = await fetch(`${endpoint}?page=${page}&per_page=${perPage}`, {
        headers: { 'X-Requested-With':'XMLHttpRequest', 'Accept':'application/json' }
      });
      if (!res.ok) {
        console.error('HTTP', res.status, await res.text());
        throw new Error('HTTP '+res.status);
      }

      const json = await res.json();
      const isDesktop = window.matchMedia('(min-width: 768px)').matches;

      if (isDesktop) {
        const holder = document.getElementById('companiesWrapper');
        holder.insertAdjacentHTML('beforeend', json.desktop);
      } else {
        const holder = document.getElementById('mobile-list');
        holder.insertAdjacentHTML('beforeend', json.mobile);
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
