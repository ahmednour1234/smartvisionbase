{{-- resources/views/web/content/voting.blade.php --}}
@extends('web.layouts.app')

@section('content')

<style>
    @import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@700&display=swap');

    .page-header__inner { text-align: center; padding: 95px 0 60px; }

    #typed-title{
        display:inline-block; overflow:hidden; white-space:nowrap;
        border-right:3px solid #FFE986;
        font-family:'Montserrat',sans-serif;
        font-size:60px;
        background:linear-gradient(90deg,#FFE986 0%,#C48127 100%);
        -webkit-background-clip:text; -webkit-text-fill-color:transparent;
        animation:blink-caret .75s step-end infinite;
    }
    #typed-title.finished{ border-right:none; animation:none; }
    @keyframes blink-caret{ 0%,100%{border-color:transparent} 50%{border-color:#FFE986} }

    .section-title-responsive{
        font-size:60px; width:80%; max-width:800px; font-weight:800;
        background:linear-gradient(90deg,#fff,#fff);
        -webkit-background-clip:text; -webkit-text-fill-color:transparent;
        margin:0 auto 40px; line-height:1.3;
    }

    @media (max-width:1365px){ #typed-title{ font-size:60px; } }
    @media (max-width:1023px){ #typed-title{ font-size:42px; } }
    @media (max-width:767.98px){
        #typed-title{ font-size:32px; }
        .section-title-responsive{ font-size:37px !important; }
    }

    .desktop-only{ display:none; }
    .mobile-only-page{ display:none; }
    @media (min-width:768px){ .desktop-only{ display:block; } }
    @media (max-width:767.98px){ .mobile-only-page{ display:block; } }

    .section-title{ display:none !important; }

    /* حاوية البحث */
    .search-container .form-control{
        background-color: rgba(255,255,255,.1);
        color:#fff; border:1px solid rgba(255,255,255,.3);
    }
    .search-container .form-control::placeholder{ color:rgba(255,255,255,.6); }

    /* رسالة لا توجد نتائج */
    .no-results{
        display:none; text-align:center; color:#fff; opacity:.8; margin:24px 0;
    }
</style>

<section class="page-header">
    <div class="container">
        <div class="page-header__inner">
            <h2 id="typed-title"></h2>
        </div>
    </div>
</section>

@php $locale = app()->getLocale(); @endphp

<div class="row justify-content-end mt-3 mb-4 px-4">
  <div class="col-md-4 search-container">
    <input type="text" id="searchInput" class="form-control"
           placeholder="{{ __('Search by name, description...') }}">
  </div>
</div>

<!-- Desktop Section -->
<div class="desktop-only" id="desktopList">
    @include('web.content.partials.voting-desktop', ['companies' => $companies])
</div>

<!-- Mobile Section -->
<div class="mobile-only-page" id="mobileList">
    @include('web.content.partials.voting-mobile', ['companies' => $companies])
</div>

<p class="no-results" id="noResults">{{ __('No matching companies found.') }}</p>

<section class="section section-lg text-center" style="color:#fff; padding:80px 0;">
    <div class="container">
        <div class="block-lg block-center">
            <h6 style="font-size:20px; font-weight:600; color:#FFE986; margin-bottom:10px; letter-spacing:1px;">
                {{ __('Become a sponsor.') }}
            </h6>
            <h2 class="section-title-responsive">
                {{ __("Let’s become a part of our conference") }}
            </h2>
            <a href="{{ route('web.becomesponsor') }}" class="btn btn-lg"
               style="padding:14px 32px; font-size:18px; font-weight:bold; color:#1a120b;
                      background:linear-gradient(90deg,#FFE986,#C48127);
                      border-radius:50px; text-decoration:none; transition:all .3s ease-in-out;"
               onmouseover="this.style.background='white'; this.style.color='#1a120b'"
               onmouseout="this.style.background='linear-gradient(90deg, #FFE986, #C48127)'; this.style.color='#1a120b'">
                {{ __('Become a Sponsor') }}
            </a>
        </div>
    </div>
</section>

<script>
(function(){
    // ===== Typewriter =====
    document.addEventListener('DOMContentLoaded', function () {
        const text = @json(__('Voting'));
        const target = document.getElementById('typed-title');
        let i = 0;
        (function type(){
            if(i < text.length){ target.innerHTML += text.charAt(i++); setTimeout(type, 100); }
            else { target.classList.add('finished'); }
        })();
    });

    // ===== Utilities =====
    function normalize(str){
        if(!str) return '';
        return str
            .toLowerCase()
            // إزالة التشكيل العربي لو موجود
            .replace(/[\u064B-\u065F]/g, '')
            // مسافات زائدة
            .replace(/\s+/g, ' ')
            .trim();
    }

    // ===== Search =====
    const input = document.getElementById('searchInput');
    const items = Array.from(document.querySelectorAll('[data-search]')); // يعمل على الديسكتوب والموبايل معًا
    // احفظ قيمة display الأصلية (flex/grid/inline-block...)
    items.forEach(el => el.dataset._display = getComputedStyle(el).display || 'block');

    const noResults = document.getElementById('noResults');

    // Debounce بسيط
    let t = null;
    input && input.addEventListener('input', function(){
        clearTimeout(t);
        const q = normalize(this.value);
        t = setTimeout(function(){
            let shown = 0;
            for(const el of items){
                const hay = el.getAttribute('data-search') || '';
                const match = hay.indexOf(q) !== -1; // hay مُهيأ أصلاً lowercase من السيرفر
                el.style.display = match ? el.dataset._display : 'none';
                if(match) shown++;
            }
            if(noResults){
                noResults.style.display = (shown === 0 && q.length) ? 'block' : 'none';
            }
        }, 100);
    });
})();
</script>

@endsection
