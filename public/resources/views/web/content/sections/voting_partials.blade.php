@php
    // اكتشاف بسيط للموبايل/التابلت من الـ User-Agent بدون باكچات
    $ua = request()->header('User-Agent', '') ?? '';
    $isMobile = preg_match(
        '/Mobile|Android|iPhone|iPad|iPod|IEMobile|BlackBerry|Opera Mini|webOS|Kindle|Silk|Opera Mobi/i',
        $ua
    ) === 1;

    // لو مش عايز تعتبر iPad "موبايل"، احذف iPad من الريجيكس فوق
@endphp

@if ($isMobile)
    {{-- موبايل فقط --}}
    <div class="mobile-only-page">
        <div id="mobile-list">
            @include('web.content.partials.voting_partials.voting-mobile', ['companies' => $companies])
        </div>
    </div>
@else
    {{-- ديسكتوب فقط --}}
    <div class="desktop-only">
        <div id="desktop-list">
            @include('web.content.partials.voting_partials.voting-desktop', ['companies' => $companies])
        </div>
    </div>
@endif
