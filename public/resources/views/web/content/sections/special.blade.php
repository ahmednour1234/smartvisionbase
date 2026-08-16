@if ($section && $section->media_type === 'image')
@php
    // خريطة أعلام الدول: code => flag url
    $countryFlags = collect($countries ?? [])->mapWithKeys(function ($c) {
        $code = strtoupper($c['code'] ?? '');
        $flag = $c['flag_svg'] ?? ($c['flag_png'] ?? null);
        return $code ? [$code => $flag] : [];
    });

    // تنسيق عدد المتابعين بشكل مختصر
    $humanFollowers = function($n) {
        if (!is_numeric($n)) return $n;
        if ($n >= 1000000) return number_format($n / 1000000, fmod($n, 1000000) ? 1 : 0) . 'M';
        if ($n >= 1000)    return number_format($n / 1000,    fmod($n, 1000)    ? 1 : 0) . 'K';
        return (string) $n;
    };

    $locale = $locale ?? app()->getLocale();

    // أيقونات الشبكات (اسم العمود => أيقونة FA)
    $socialPlatforms = [
        'facebook'  => 'facebook-f',
        'twitter'   => 'twitter',
        'linkedin'  => 'linkedin',
        'youtube'   => 'youtube-play',
        'tiktok'    => 'twitter',   // غيّرها إلى 'tiktok' لو عندك FA6
        'instgram'  => 'instagram', // مطابق لاسم الحقل عندك
    ];
@endphp

<section class="parallax-container section mt-5" style="background:#f5f5f5;">
  <style>
    .speaker{
      position:relative;border-radius:12px;overflow:hidden;background:#fff;border:1px solid #e0e0e0;
      display:flex;flex-direction:column;height:100%;
      box-shadow:0 10px 30px rgba(0,0,0,.08);transition:box-shadow .3s, transform .3s
    }
    .speaker:hover{ transform:translateY(-6px); box-shadow:0 20px 40px rgba(204,37,46,.2) }
    .speaker::before{
      content:""; position:absolute; inset:8px; border-radius:10px; border:2px solid rgba(204,37,46,.4);
      opacity:0; transition:opacity .3s; pointer-events:none; z-index:1
    }
    .speaker:hover::before{ opacity:1 }

    .speaker-img{ position:relative; background:#fff; padding:15px; display:flex; justify-content:center; align-items:center; overflow:hidden; border-radius:14px }
    .speaker-img .photo{
      width:100%; height:320px; object-fit:cover; border-radius:10px; display:block; transition:transform .3s
    }
    .speaker:hover .speaker-img .photo{ transform:scale(1.03) }

    /* شريط واحد يجمع IG + TikTok */
    .followers-inline{
      position:absolute; left:12px; right:12px; bottom:12px;
      background:rgba(0,0,0,.58); color:#fff; padding:6px 10px; border-radius:9px;
      font-weight:700; font-size:14px; line-height:1.1;
      display:flex; align-items:center; justify-content:center; gap:12px;
      backdrop-filter:blur(2px); -webkit-backdrop-filter:blur(2px)
    }
    .followers-inline .fi{ display:inline-flex; align-items:center; gap:6px; white-space:nowrap }
    .followers-inline .sep{ opacity:.95 }
    .followers-inline svg{ width:22px; height:22px; display:block }

    .speaker-info{ background:#f7f7f7; padding:20px 15px; flex-grow:1; display:flex; flex-direction:column; justify-content:center; text-align:center }
    .speaker-title{ font-weight:700; font-size:1.5rem; color:#222; margin-bottom:8px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis }
    .speaker-title a{ color:#111; text-decoration:none; display:inline-flex; align-items:center; gap:8px }
    .speaker-position{ color:#555; font-size:1rem; margin:6px 0 0; word-wrap:break-word }
    .country-flag{ width:18px; height:12px; object-fit:cover; border:1px solid #ddd; border-radius:2px; display:inline-block }

    .speaker-social-list{ display:flex; justify-content:center; gap:10px; margin:12px 0 0; padding:0; list-style:none }
    .speaker-social-list a{
      color:#333; background:#fff; width:36px; height:36px; display:flex; align-items:center; justify-content:center;
      border-radius:50%; box-shadow:0 2px 8px rgba(0,0,0,.08); transition:background .2s
    }
    .speaker-social-list a:hover{ background:#f1f1f1 }

    /* ===== الموبايل: الصورة مربعة ===== */
    @media (max-width:575.98px){
      /* اجعل ارتفاع الصورة مساويًا لعرضها (fallback) */
      .speaker-img{ padding:12px }
      .speaker-img .photo{
        height:auto;            /* ألغي الارتفاع الثابت */
        aspect-ratio:1/1;       /* مربعة على المتصفحات الحديثة */
        width:100%;
        object-fit:cover;
        border-radius:10px;
      }
      /* للمتصفحات القديمة بدون aspect-ratio */
      .speaker-img.square-fallback{ position:relative; }
      .speaker-img.square-fallback .photo{
        height:100%; width:100%;
        position:absolute; inset:0;
      }
      .speaker-img.square-fallback::before{
        content:""; display:block; padding-top:100%; /* يحجز مربع */
      }

      .followers-inline{
        font-size:11px; padding:5px 8px; gap:8px; left:10px; right:10px; bottom:10px
      }
      .followers-inline svg{ width:18px; height:18px }
      .speaker-title{ font-size:1.1rem; white-space:normal }
      .speaker-position{ font-size:.9rem }
    }

    /* موبايل صغير جداً (≤ 375px) */
    @media (max-width:375.98px){
      .followers-inline{
        font-size:10px; padding:4px 7px; gap:6px; left:8px; right:8px; bottom:8px
      }
      .followers-inline svg{ width:16px; height:16px }
    }

    @media (max-width:991.98px){
      .speaker-info{ padding:0 12px 14px }
      .speaker-title{ font-size:1.25rem }
      .speaker-position{ font-size:.95rem }
    }
  </style>

  <div class="parallax-content section-lg context-dark text-center">
    <div class="container">
      <h6 class="sub-tit">{{ $section->title[$locale] ?? '' }}</h6>
      <h3 class="gre-title" style="font-size:40px;font-weight:bolder;margin-top:15px">
        {{ $section->description[$locale] ?? '' }}
      </h3>

      <div class="row mt-4">
        @foreach ($special_guests as $speaker)
          @php
              $spkCode     = strtoupper($speaker->country_code ?? '');
              $flagUrl     = $countryFlags[$spkCode] ?? null;
              $imgReal     = asset('public/'.$speaker->image);
              $altName     = $locale === 'ar' ? ($speaker->name_ar ?? '') : ($speaker->name_en ?? '');
              $igRaw       = $speaker->number_of_followers ?? '';
              $ttRaw       = $speaker->followers_ticktock ?? '';
              $followersIg = $humanFollowers($igRaw);
              $followersTt = $humanFollowers($ttRaw);
              $hasIg       = !empty($igRaw);
              $hasTt       = !empty($ttRaw);
          @endphp

          <div class="col-6 col-md-6 col-lg-4 mb-4">
            <div class="speaker">
              <!-- أضف class square-fallback لو تريد إجبار المربع في متصفحات قديمة -->
              <div class="speaker-img">
                <a href="#" tabindex="-1" aria-label="{{ $altName }}">
                  <img
                    class="photo"
                    src="{{ $imgReal }}"
                    alt="{{ $altName }}"
                    loading="lazy" decoding="async" fetchpriority="low"
                    width="600" height="320"
                    style="border-radius:10px"
                  />
                </a>

                @if($hasIg || $hasTt)
                  <div class="followers-inline" aria-label="Followers">
                    @if($hasIg)
                      <span class="fi">
                        {{-- Instagram outline --}}
                        <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                          <rect x="3" y="3" width="18" height="18" rx="5" ry="5"
                                fill="none" stroke="currentColor" stroke-width="1.5"/>
                          <circle cx="12" cy="12" r="4.5"
                                  fill="none" stroke="currentColor" stroke-width="1.5"/>
                          <circle cx="17.5" cy="6.5" r="1.25" fill="currentColor"/>
                        </svg>
                        <span>
                          <strong>{{ $followersIg }}</strong>
                          {{ ($locale ?? 'ar') === 'ar' ? ($igRaw == 1 ? 'متابع' : 'متابعين') : 'Followers' }}
                        </span>
                      </span>
                    @endif

                    @if($hasIg && $hasTt)
                      <span class="sep" aria-hidden="true">-</span>
                    @endif

                    @if($hasTt)
                      <span class="fi">
                        {{-- TikTok icon --}}
                        <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                          <path d="M14 3v3.6c1.4 1.2 3.2 2 5 2v2.5c-2.1 0-3.9-.6-5-1.4v4.4A6.5 6.5 0 1 1 9 8.5c.5 0 1 .05 1.5.16v2.6A3.5 3.5 0 1 0 12.5 15V3h1.5z"
                                fill="currentColor"/>
                        </svg>
                        <span>
                          <strong>{{ $followersTt }}</strong>
                          {{ ($locale ?? 'ar') === 'ar' ? ($ttRaw == 1 ? 'متابع' : 'متابعين') : 'Followers' }}
                        </span>
                      </span>
                    @endif
                  </div>
                @endif
              </div>

              <div class="speaker-info">
                <h5 class="speaker-title">
                  <a href="#">
                    {{ $altName }}
                    @if($flagUrl)
                      <img class="country-flag" src="{{ $flagUrl }}" alt="flag" loading="lazy" decoding="async" fetchpriority="low">
                    @endif
                  </a>
                </h5>

                <p class="speaker-position">
                  {{ $locale == 'ar' ? ($speaker->title_ar ?? '') : ($speaker->title_en ?? '') }}
                </p>

                <ul class="speaker-social-list" aria-label="Social links">
                  @foreach ($socialPlatforms as $field => $icon)
                    @php $url = trim((string) ($speaker->$field ?? '')); @endphp
                    @if ($url !== '')
                      <li>
                        <a class="icon fa fa-{{ $icon }}"
                           href="{{ $url }}"
                           target="_blank"
                           rel="noopener noreferrer"
                           aria-label="{{ $field }}"></a>
                      </li>
                    @endif
                  @endforeach
                </ul>
              </div>
            </div>
          </div>
        @endforeach
      </div>

      <a class="button button-secondary box-with-triangle-right wow fadeScale mt-4"
         href="{{ route('web.special') }}" data-triangle=".button-overlay">
        <span>{{ $locale === 'ar' ? 'عرض جميع الضيوف المميزين' : 'View All Special Guests' }}</span>
        <span class="button-overlay"></span>
      </a>
    </div>
  </div>
</section>
@endif
