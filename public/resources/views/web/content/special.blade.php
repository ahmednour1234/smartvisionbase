@extends('web.layouts.app')

@section('content')
@php
  $locale  = app()->getLocale();
  // $section يُمرّر من الكنترولر (قيمته قد تكون 'aps' أو 'special')
@endphp

<style>
  :root { --brand:#cc252e; }
  html, body { overflow-x: hidden; }

  .speaker{ position:relative; display:flex; flex-direction:column; height:100%;
    border:1px solid #e0e0e0; border-radius:14px; overflow:hidden; background:#fff;
    box-shadow:0 10px 30px rgba(0,0,0,.08); transition:transform .25s ease, box-shadow .25s ease; cursor:pointer; }
  .speaker::before{ content:""; position:absolute; inset:8px; border-radius:10px;
    border:2px solid rgba(204,37,46,.35); opacity:0; transition:opacity .25s ease; pointer-events:none; z-index:1; }
  .speaker:hover{ transform:translateY(-6px); box-shadow:0 20px 40px rgba(204,37,46,.18); }
  .speaker:hover::before{ opacity:1; }

  .speaker-img{ position:relative; border-radius:14px; overflow:hidden; aspect-ratio:4/5; }
  .speaker-img img{ width:100%; height:100%; object-fit:cover; object-position:center; transition:transform .25s ease; display:block; }
  .speaker:hover .speaker-img img{ transform:scale(1.04); }

  .followers-inline{
    position:absolute; left:0; right:0; bottom:0;
    background:rgba(0,0,0,.55); color:#fff; padding:6px 10px;
    font-weight:700; font-size:14px; display:flex; align-items:center; justify-content:center;
    gap:12px; backdrop-filter:blur(2px); -webkit-backdrop-filter:blur(2px);
  }
  .followers-inline .followers-item{ display:inline-flex; align-items:center; gap:6px; }
  .followers-inline .sep{ opacity:.9; }

  .speaker-info{ flex:1 1 auto; background:#f7f7f7; padding:14px 12px;
    display:flex; flex-direction:column; align-items:center; text-align:center; }
  .speaker-title{ font-weight:800; font-size:1.25rem; color:#111; margin:4px 0 6px; }
  .speaker-title a{ color:inherit; text-decoration:none; display:inline-flex; align-items:center; gap:8px; }
  .country-flag{ width:28px; height:24px; object-fit:cover; border:1px solid #ddd; border-radius:2px; display:inline-block; }
  .speaker-position{ color:#555; font-size:.98rem; margin:0 0 10px; word-break:break-word; }
  .speaker-social-list{ list-style:none; padding:0; margin:8px 0 0; display:flex; gap:10px; }
  .speaker-social-list a{ width:36px; height:36px; display:flex; align-items:center; justify-content:center;
    border-radius:50%; color:#333; background:#fff; box-shadow:0 2px 8px rgba(0,0,0,.08);
    transition:background .2s ease, transform .2s ease; }
  .speaker-social-list a:hover{ background:#f1f1f1; transform:translateY(-1px); }

  @media (max-width: 991.98px){ .speaker-img{ aspect-ratio:3/4; } .speaker-position{ font-size:.95rem; } }
  @media (max-width: 575.98px){
    .speaker-title{ font-size:1rem; } .speaker-position{ font-size:.9rem; }
    .followers-inline{ font-size:12px; padding:5px 8px; gap:10px; }
  }

  .sp-col{ display:flex; }
  .sp-card{ width:100%; }

  .search-row { display:flex; align-items:center; justify-content:flex-end; }
  .search-toolbar{
    width: 240px; background:#fff; border:1px solid #e5e7eb; border-radius:10px;
    padding:6px 10px; display:flex; gap:8px; align-items:center; box-shadow:0 4px 20px rgba(0,0,0,.06);
  }
  .search-toolbar input{ flex:1; border:0; outline:none; font-size:14px; padding:6px 2px; }
  .search-toolbar .clear-btn{
    border:none; background:#f3f4f6; color:#111; padding:6px 10px; border-radius:8px; cursor:pointer; font-size:12px; }

  .infinite-loader{ display:none; align-items:center; justify-content:center; padding:16px; color:#555; }
  .infinite-loader.active{ display:flex; }
  .spinner{ width:22px; height:22px; border:3px solid #eee; border-top-color:var(--brand); border-radius:50%; animation:spin .8s linear infinite; margin-inline-end:10px; }
  @keyframes spin{ to { transform:rotate(360deg); } }

  .no-results{ display:none; color:#777; padding:16px 0; }
  .no-results.show{ display:block; }
</style>

<section class="breadcrumbs-custom bg-image context-dark"
  style="background-image:url({{ asset('public/web/assets/images/bg-breadcrumbs-01-1894x424.jpg') }});">
  <div class="container">
    <h3 class="breadcrumbs-custom-title  pt-5">
      {{ ($section ?? '') == 'aps' ? 'IBS & Affiliate' : 'Special Guest' }}
    </h3>
  </div>
</section>

<section class="section section-lg bg-default text-center" style="background:#f5f5f5;">
  <div class="container">
    <h3 class="font-weight-bold gre-title" style="color:var(--brand);">
      {{ ($section ?? '') == 'aps' ? 'IBS & Affiliate' : 'Special Guest' }}
    </h3>

    <div class="search-row mt-4 mb-3">
      <div class="search-toolbar">
        <input id="q" type="text" placeholder="{{ __('Search by name or title...') }}" value="{{ e($q ?? '') }}">
        <button id="clearBtn" class="clear-btn" type="button">{{ __('Clear') }}</button>
      </div>
    </div>

    <div id="speakersGrid" class="row mt-3">
      @include('web.content.partials._cards_special', [
        'speakers'        => $speakers,
        'countryFlags'    => $countryFlags ?? [],
        'socialPlatforms' => $socialPlatforms ?? [],
        'locale'          => $locale
      ])
    </div>

    <div id="noResults" class="no-results {{ $speakers->count() ? '' : 'show' }}">{{ __('No results found.') }}</div>

    <div id="infiniteLoader" class="infinite-loader">
      <div class="spinner" aria-hidden="true"></div>
      <span>{{ __('Loading more...') }}</span>
    </div>

    <div id="infiniteSentinel" data-next-url="{{ $speakers->nextPageUrl() }}" style="height:1px;"></div>
  </div>
</section>

<script>
(function(){
  const grid      = document.getElementById('speakersGrid');
  const sentinel  = document.getElementById('infiniteSentinel');
  const loader    = document.getElementById('infiniteLoader');
  const searchInp = document.getElementById('q');
  const clearBtn  = document.getElementById('clearBtn');
  const noResults = document.getElementById('noResults');

  let isLoading = false;
  let observer  = null;
  let debounceTimer = null;

  function setNextUrl(url){ if (sentinel) sentinel.dataset.nextUrl = url || ''; }
  function getNextUrl(){ return sentinel?.dataset?.nextUrl || ''; }
  function showLoader(state){ loader?.classList.toggle('active', !!state); }

  /**
   * يبني URL بالاعتماد على الرابط الحالي (مع كل باراميتراته)
   * ويعدّل/يستبدل فقط q، ويضيف ajax=1
   */
  function buildUrlFromCurrent(newQValue){
    const current = new URL(window.location.href);
    // استبدال q فقط
    if (newQValue && newQValue.trim() !== '') {
      current.searchParams.set('q', newQValue.trim());
    } else {
      current.searchParams.delete('q'); // امسح q فقط واترك الباقي كما هو
    }
    // علشان طلب Ajax
    current.searchParams.set('ajax', '1');
    return current.toString();
  }

  async function fetchChunk(url){
    if(!url) return null;
    try{
      showLoader(true);
      isLoading = true;
      const res = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' }});
      if(!res.ok) throw new Error('Network error');
      return await res.json();
    }catch(e){
      console.error(e);
      return null;
    }finally{
      isLoading = false;
      showLoader(false);
    }
  }

  async function onIntersect(entries){
    for (const entry of entries){
      if(entry.isIntersecting && !isLoading){
        const next = getNextUrl();
        if(!next){ observer && observer.disconnect(); return; }
        // الـ next_url جاي من السيرفر، نضيف عليه ajax=1 فقط ونترك باقي باراميترات السيرفر
        const nextUrl = new URL(next, window.location.origin);
        nextUrl.searchParams.set('ajax', '1');
        const data = await fetchChunk(nextUrl.toString());
        if(data && data.html){
          const temp = document.createElement('div');
          temp.innerHTML = data.html;
          while(temp.firstChild){ grid.appendChild(temp.firstChild); }
          setNextUrl(data.next_url || '');
          if(!data.next_url){ observer && observer.disconnect(); }
        }
      }
    }
  }

  function initObserver(){
    if(!sentinel) return;
    observer && observer.disconnect();
    observer = new IntersectionObserver(onIntersect, { root:null, rootMargin:'400px 0px', threshold:0 });
    observer.observe(sentinel);
  }

  async function runServerSearch(){
    const qVal = (searchInp?.value || '').trim();
    const url  = buildUrlFromCurrent(qVal);
    const data = await fetchChunk(url);
    if (data){
      grid.innerHTML = data.html || '';
      setNextUrl(data.next_url || '');
      noResults && noResults.classList.toggle('show', grid.children.length === 0);
      initObserver();

      // حدّث العنوان في المتصفح بنفس الباراميترات مع تعديل/حذف q فقط
      const newUrl = new URL(window.location.href);
      if (qVal) newUrl.searchParams.set('q', qVal);
      else newUrl.searchParams.delete('q');
      // لا نحتفظ بـ ajax في عنوان المتصفح
      newUrl.searchParams.delete('ajax');
      window.history.replaceState({}, '', newUrl.toString());
    }
  }

  searchInp && searchInp.addEventListener('input', function(){
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(runServerSearch, 250);
  });

  clearBtn && clearBtn.addEventListener('click', function(){
    if (searchInp && searchInp.value !== ''){
      searchInp.value = '';
      runServerSearch(); // هيحذف q ويحتفظ بكل الباقي (زي section)
    }
  });

  document.addEventListener('DOMContentLoaded', function(){
    initObserver();
    noResults && noResults.classList.toggle('show', grid.children.length === 0);
  });
})();
</script>
@endsection
