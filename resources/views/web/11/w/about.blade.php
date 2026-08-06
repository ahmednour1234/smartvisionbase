@extends('web.layouts.app')

@section('content')
     <style>
        .gallery-auto-scroll {
            overflow: hidden;
            width: 100%;
            position: relative;
        }

        .gallery-track {
            display: flex;
            width: max-content;
            animation: scroll-horizontal 60s linear infinite;
            gap: 24px;
            /* المسافة بين الصور */
            padding-block: 10px;
        }

        .thumb-wrapper {
            flex: 0 0 auto;
            width: 300px;
            /* العرض أكبر */
            height: 400px;
            /* الطول أطول */
            border-radius: 12px;
            overflow: hidden;
            position: relative;
        }

        .thumb-wrapper img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
        }

        @keyframes scroll-horizontal {
            0% {
                transform: translateX(0);
            }

            100% {
                transform: translateX(-50%);
            }
        }

        .sponsor-auto-scroll {
            overflow: hidden;
            width: 100%;
            position: relative;
        }

        .sponsor-track {
            display: flex;
            width: max-content;
            animation: sponsor-scroll 50s linear infinite;
            gap: 40px;
            /* المسافة بين الشعارات */
            align-items: center;
            justify-content: center;
            margin-top: 40px;
        }

        .sponsor-item {
            flex: 0 0 auto;
            width: 190px;
            height: 190px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(to right, #000000, #5b090973);
            border-radius: 12px;
            padding: 10px;
        }

        .sponsor-item img {
            max-width: 100%;
            max-height: 90%;
            object-fit: contain;
            border-radius: 10px;
        }

        @keyframes sponsor-scroll {
            0% {
                transform: translateX(0);
            }

            100% {
                transform: translateX(-50%);
            }
        }
        .gallery-wrapper {
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
    scroll-behavior: smooth;
}

.gallery-scroll-container {
    display: flex;
    gap: 20px;
    width: max-content;
}

.gallery-thumb {
    flex: 0 0 auto;
    width: calc(25% - 15px); /* يعرض 4 صور في الصف */
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 2px 6px rgba(0,0,0,0.1);
    transition: transform 0.3s ease-in-out;
}

.gallery-thumb img {
    width: 100%;
    height: auto;
    display: block;
}

.gallery-thumb:hover {
    transform: scale(1.05);
}
.swiper-slide {
    height: 300px; /* أو أي ارتفاع تريده */
    display: flex;
    align-items: center;
    justify-content: center;
}

.swiper-slide img {
    width:400px;
    height: 400px;
    object-fit: cover;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    transition: transform 0.3s ease;
}

.swiper-slide img:hover {
    transform: scale(1.03);
}
.gallery-grid-mobile {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 15px;
  padding-bottom: 30px;
}

.gallery-grid-mobile .gallery-item img {
 width:400px;
    height: 250px;
  border-radius: 8px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

@media (max-width: 767px) {
  .speaker-card {
    display: none;
  }

  .speaker-card:nth-child(-n+4) {
    display: block;
  }
}

@media (max-width: 768px) {
  .custom-swiper-btn {
    width: 28px;
    height: 28px;
  }

  .swiper-button-next::after,
  .swiper-button-prev::after {
    font-size: 12px;
  }

  .swiper-button-prev.custom-swiper-btn {
    left: 0px;
  }

  .swiper-button-next.custom-swiper-btn {
    right: 0px;
  }
.gallery-grid-mobile .gallery-item img {
    width: 200px;
    height: 200px;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}
}

.btn-sm, .btn-group-sm > .btn {
    padding: 0.25rem 0.5rem;
    font-size: 18px;
    line-height: 1.5;
    border-radius: 0.2rem;
}
    .ads-section .ad-box {
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.15);
        border: 1px solid #ddd;
        background: #fff;
        padding: 8px;
    }

    @media (max-width: 991.98px) {
        .breadcrumbs-custom {
            height: 350px !important; /* صورة أطول في الأجهزة الصغيرة */
            background-size: cover;
            background-position: center;
        }

        .breadcrumbs-custom-title {
            font-size: 28px;
            padding-top:150px;
        }
    }

    .breadcrumbs-custom {
        background-size: cover;
        background-position: center;
    }

        /* Breadcrumbs */
    @media (max-width: 991.98px) {
        .breadcrumbs-custom {
            height: 350px !important;
            background-size: cover;
            background-position: center;
        }
        .breadcrumbs-custom-title {
            font-size: 28px;
            padding-top: 150px;
        }
    }
    .breadcrumbs-custom {
        background-size: cover;
        background-position: center;
    }

    /* Speaker Card */
    .speaker {
        position: relative;
        border-radius: 12px;
        overflow: hidden; /* عشان التأثير يبقى داخل الكارد */
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        display: flex;
        flex-direction: column;
        cursor: pointer;
        height: 100%;
        background: #fff;
        transition: box-shadow 0.3s ease, transform 0.3s ease;
        border: 1px solid #e0e0e0;
        z-index: 0;
    }
    .speaker::before {
        content: "";
        position: absolute;
        top: 8px;
        left: 8px;
        right: 8px;
        bottom: 8px;
        border-radius: 10px;
        border: 2px solid rgba(204, 37, 46, 0.4); /* ظل خفيف */
        opacity: 0;
        transition: opacity 0.3s ease;
        pointer-events: none;
        z-index: 1;
    }
    .speaker:hover {
        transform: translateY(-6px);
        box-shadow: 0 20px 40px rgba(204, 37, 46, 0.2);
    }
    .speaker:hover::before {
        opacity: 1;
    }

    /* الجزء العلوي - صورة */
    .speaker-img {
        background: #fff;
        padding: 15px;
        display: flex;
        justify-content: center;
        align-items: center;
        overflow: hidden;
    }
    .speaker-img img {
        width: 100%;
        height: auto;
        object-fit: contain;
        border-radius: 10px;
        transition: transform 0.3s ease;
        display: block;
    }
    .speaker:hover .speaker-img img {
        transform: scale(1.05);
    }

    /* الجزء السفلي - النصوص والخلفية */
    .speaker-info {
        background: #f7f7f7;
        padding: 20px 15px;
        flex-grow: 1;
        display: flex;
        flex-direction: column;
        justify-content: center;
        text-align: center;
    }
    .speaker-title {
        font-weight: 700;
        font-size: 1.5rem;
        color: #222;
        margin-bottom: 8px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .speaker-position {
        color: #666;
        font-size: 1rem;
        margin-bottom: 12px;
        word-wrap: break-word;
    }

    .speaker-social-list {
        list-style: none;
        padding: 0;
        margin: 0 auto;
        display: flex;
        justify-content: center;
        gap: 15px;
        margin-top: auto;
    }
    .speaker-social-list li a {
        color: #cc252e;
        font-size: 18px;
        transition: color 0.3s ease;
    }
    .speaker-social-list li a:hover {
        color: #e84b3a;
    }

    /* Responsive Adjustments */
    @media (max-width: 991.98px) {
        .speaker-img {
            padding: 10px;
        }
        .speaker-info {
            padding: 0;
        }
        .speaker-title {
            font-size: 1.2rem;
            white-space: normal;
        }
        .speaker-position {
            font-size: 0.9rem;
        }
    }
    @media (max-width: 576px) {
        .speaker-img {
            padding: 8px;
        }
        .speaker-title {
            font-size: 1rem;
        }
        .speaker-position {
            font-size: 0.85rem;
        }
    }

    </style>
     @php
        $locale = app()->getLocale();

    @endphp
<section class="breadcrumbs-custom bg-image context-dark"
         style="background-image: url({{ asset('web/assets/images/bg-breadcrumbs-01-1894x424.jpg') }});">
    <div class="container">

        <h3 class="breadcrumbs-custom-title">About</h3>
    </div>
</section>
            <!-- About Section -->
@php
    use App\Models\Ad;
    $ads = Ad::where('active', 1)->latest()->take(5)->get();
@endphp

<section class="container-fluid section section-lg bg-default mt-5 mb-5">
    <div class="row">
        <!-- عمود المحتوى الرئيسي -->
        <div class="{{ $ads->isNotEmpty() ? 'col-lg-8' : 'col-12' }} col-md-12">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-md-6 mb-4 mb-md-0">
                        <h3>{{ $aboutSection->title[app()->getLocale()] ?? '' }}</h3>
                        <p style="color:black;">
                            {{ $aboutSection->description[app()->getLocale()] ?? '' }}
                        </p>
                    </div>
                    <div class="col-md-6">
                        <div class="img-separated">
                            @if ($aboutSection->media_type === 'image')
                                <img src="{{ asset($aboutSection->media_path) }}"
                                     alt="About Image"
                                     class="img-fluid rounded shadow"
                                     style="max-width: 100%; height: auto;" />
                            @elseif($aboutSection->media_type === 'video')
                                <video autoplay muted loop playsinline
                                       style="width:100%; height:auto; object-fit:cover; border-radius: 8px;">
                                    <source src="{{ asset($aboutSection->media_path) }}" type="video/mp4">
                                    {{ __('home_sections.your_browser_does_not_support_video') }}
                                </video>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- عمود الإعلانات إن وُجدت -->
        @if($ads->isNotEmpty())
            <div class="col-lg-4 col-md-12 d-flex flex-column"
                 style="border-left: 1px solid #ddd; padding-left: 25px;">
                <aside class="p-3">
                    @foreach($ads as $ad)
                        <div class="ad-box mb-4 shadow-sm border rounded overflow-hidden">
                            <a href="{{ $ad->link ?? '#' }}" target="_blank">
                                <img src="{{ asset($ad->img) }}"
                                     alt="Ad Image"
                                     class="img-fluid w-100"
                                     style="object-fit: cover; max-height: 300px;">
                            </a>
                        </div>
                    @endforeach
                </aside>
            </div>
        @endif
    </div>
</section>
        </div>

        <!-- عمود الإعلانات -->
    </div>
</section>
<!-- Speakers Section -->
<section class="section section-lg bg-default text-center">
    <div class="container">
        <h4 class="font-weight-bold mb-5">{{ $speaker_section->title[$locale] ?? '' }}</h4>

        <div class="row">
            @php
                $socialPlatforms = [
                    'facebook' => 'facebook-f',
                    'twitter' => 'twitter',
                    'linkedin' => 'linkedin',
                    'youtube' => 'youtube-play',
                    'tiktok' => 'twitter',
                    'instagram' => 'instagram',
                ];
            @endphp

            @foreach ($speakers as $speaker)
                <div class="col-6 col-md-6 col-lg-4 mb-4" id="speaker-{{ $speaker->id }}">
                    <div class="speaker">
                        <div class="speaker-img">
                            <a href="#">
                                <img src="{{ asset($speaker->image) }}"
                                    alt="{{ $locale == 'ar' ? $speaker->name_ar : $speaker->name_en }}" />
                            </a>
                        </div>
                        <div class="speaker-info">
                            <h5 class="speaker-title">
                                <a href="#">{{ $locale == 'ar' ? $speaker->name_ar : $speaker->name_en }}</a>
                            </h5>
                            <p class="speaker-position">
                                {{ $locale == 'ar' ? $speaker->title_ar : $speaker->title_en }}
                            </p>

                            <ul class="speaker-social-list">
                                @foreach ($socialPlatforms as $field => $icon)
                                    @if (!empty($speaker->$field))
                                        <li>
                                            <a class="icon fa fa-{{ $icon }}" href="{{ $speaker->$field }}" target="_blank"
                                                rel="noopener noreferrer"></a>
                                        </li>
                                    @endif
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
     @php
        use Carbon\Carbon;

        $locale = app()->getLocale();

        $groupedSchedules = [];

        foreach ($schedules as $schedule) {
            $date = Carbon::parse($schedule->start_datetime)->format('Y-m-d');
            $groupedSchedules[$date][] = $schedule;
        }

        ksort($groupedSchedules);

        $navClasses = ['nav-link-secondary-darker', 'nav-link-purple-heart', 'nav-link-primary', 'nav-link-secodanry'];

        $dayNames = [__('First Day'), __('Second Day'), __('Third Day'), __('Fourth Day')];
    @endphp
        <section class="section section-lg bg-default text-center">
        <div class="container">
            <h6>{{ $schdule_section->title[$locale] ?? '' }}</h6>
            <h3 class="mt-3">{{ $schdule_section->description[$locale] ?? 'Event Agenda' }}</h3>

            <div class="tabs-custom tabs-horizontal tabs-corporate" id="tabs-1">
                <!-- Nav tabs -->
                <ul class="nav nav-tabs" id="scheduleTabs" role="tablist">
                    @foreach ($groupedSchedules as $date => $daySchedules)
                        @php
                            $index = $loop->index;
                            $class = $navClasses[$index % count($navClasses)];
                            $dayLabel = $dayNames[$index] ?? __('Day') . ' ' . ($index + 1);
                            $tabId = 'tabs-1-' . $index;
                        @endphp
                        <li class="nav-item" role="presentation">
                            <a class="nav-link {{ $class }} {{ $loop->first ? 'active' : '' }}"
                                href="#{{ $tabId }}" data-toggle="tab" role="tab"
                                data-triangle=".nav-link-overlay">
                                <span class="nav-link-overlay"></span>
                                <span class="nav-link-cite">{{ $dayLabel }}</span>
                                <span class="nav-link-title">
                                    {{ Carbon::parse($date)->translatedFormat('j F Y') }}
                                </span>
                            </a>
                        </li>
                    @endforeach
                </ul>

                <!-- Tab panes -->
                <div class="tab-content wow fadeIn">
                    @foreach ($groupedSchedules as $date => $daySchedules)
                        @php $tabId = 'tabs-1-' . $loop->index; @endphp
                        <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}" id="{{ $tabId }}">
                            <div class="card-group-custom card-group-corporate" role="tablist">
                                @foreach ($daySchedules as $schedule)
                                    <article class="card card-custom card-corporate">
                                        <div class="card-header" role="tab">
                                            <div class="card-title">
                                                <a class="collapsed" data-toggle="collapse"
                                                    href="#collapse-{{ $schedule->id }}" aria-expanded="false"
                                                    role="button">
                                                    <span class="schedule-classic">
                                                        <span
                                                            class="unit unit-spacing-md align-items-center d-block d-md-flex">
                                                            <span class="unit-left">
                                                                <span class="schedule-classic-img">
                                                                    <img src="{{ asset($schedule->logo) }}"
                                                                        alt="" width="122" height="122" />
                                                                </span>
                                                            </span>
                                                            <span class="unit-body">
                                                                <span class="schedule-classic-content">
                                                                    <span class="schedule-classic-time">
                                                                        {{ Carbon::parse($schedule->start_datetime)->format('h:i A') }}
                                                                        to
                                                                        {{ Carbon::parse($schedule->end_datetime)->format('h:i A') }}
                                                                    </span>
                                                                    <span class="schedule-classic-title heading-4">
                                                                        {{ $schedule->{'title_' . $locale} }}
                                                                    </span>
                                                                    <span class="schedule-classic-author" style="color:black;">
                                                                        {{ $schedule->{'description_' . $locale} }}
                                                                    </span>
                                                                </span>
                                                            </span>
                                                        </span>
                                                    </span>
                                                </a>
                                            </div>
                                        </div>
                                        <div class="collapse" id="collapse-{{ $schedule->id }}">
                                            <div class="card-body">
                                                <p style="color:black;">{{ $schedule->{'description_' . $locale} }}</p>
                                                <div class="unit unit-spacing-xxs">
                                                    <div class="unit-left">
                                                        <svg class="svg-icon-sm svg-icon-primary" role="img">
                                                            <use
                                                                xlink:href="{{ asset('images/svg/sprite.svg#earth-globe') }}">
                                                            </use>
                                                        </svg>
                                                    </div>
                                                    <div class="unit-body">
                                                        <h5>{{ __('Where') }}</h5>
                                                        <p class="font-secondary">{{ $schedule->{'location_' . $locale} }}
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </article>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                                <div class="text-center">
            <a class="button button-secondary box-with-triangle-right wow fadeScale mt-2"
               href="{{ route('web.schdule') }}" data-triangle=".button-overlay">
                <span>More Schdule</span>
                <span class="button-overlay"></span>
            </a>
        </div>
                </div>
            </div>
        </div>
    </section>
<section class="section bg-default mt-5 pt-5">
    <div class="section-lg context-dark text-center" style="background-color: #F4F3F2;">
        <div class="container">
            <h6 class="text-center" style="color:#E73701; margin-bottom: 8px;">
                {{ $section->title[$locale] ?? '' }}
            </h6>
            <h3 class="text-center" style="color:#111; margin-bottom: 22px;">
                {{ $section->description[$locale] ?? '' }}
            </h3>

            <div class="position-relative gallery-slider-unique" style="padding: 18px 0;">
                <div class="swiper uniqueGallerySwiper">
                    <div class="swiper-wrapper">
                        @foreach ($gallieries as $gallery)
                            <div class="swiper-slide">
                                <a href="{{ asset($gallery->image) }}" data-lightgallery="item" class="slide-link">
                                    <img src="{{ asset($gallery->image) }}" alt="gallery" class="slide-img" loading="lazy">
                                </a>
                            </div>
                        @endforeach
                    </div>

                    <!-- أزرار التنقل -->
                    <div class="gallery-prev custom-swiper-btn">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                          <path d="M15 18l-6-6 6-6" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>
                    <div class="gallery-next custom-swiper-btn">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                          <path d="M9 6l6 6-6 6" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- زر عرض المزيد -->
            <div class="text-center" style="margin-top: 14px;">
                <a class="button button-secondary box-with-triangle-right wow fadeScale"
                   href="{{ route('web.gallery') }}" data-triangle=".button-overlay" style="padding: 10px 30px;">
                    <span>More Gallery</span>
                    <span class="button-overlay"></span>
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Start Stats modification. -->

@php
$stats = $stats ?? [
  ['label' => __('Attendance'), 'value' => 30000, 'suffix' => '+'],
  ['label' => __('Speakers'), 'value' => 250, 'suffix' => '+'],
  ['label' => __('Sponsors'), 'value' => 300, 'suffix' => '+'],
  ];
  @endphp

  <section class="modern-stats-section" aria-label="Statistics">
    <div class="container modern-stats-inner">
      @foreach($stats as $stat)
      <div class="stat-card">
        <div class="stat-icon" aria-hidden="true" role="img" aria-label="check icon">
          <svg width="40" height="40" viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <circle cx="12" cy="12" r="10" stroke="#E73701" stroke-width="2" />
            <path d="M6 15L10 9L14 13L18 7" stroke="#E73701" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
          </svg>
        </div>

        <h3 class="stat-number" data-target="{{ $stat['value'] }}">0</h3>
        @if(!empty($stat['suffix']))
        <span class="stat-suffix">{{ $stat['suffix'] }}</span>
        @endif
        <p class="stat-label">{{ $stat['label'] }}</p>
      </div>
      @endforeach
    </div>
  </section>

  <style>
    /* الخلفية الأساسية */
  .modern-stats-section {
    background: #e5e5e5;
    padding: 80px 0;
    color: #fff;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  }

  .modern-stats-inner {
    display: flex;
    justify-content: center;
    gap: 40px;
    flex-wrap: wrap;
    max-width: 1080px;
    margin: 0 auto;
    padding: 0 15px;
  }

  .stat-card {
    background: linear-gradient(145deg, #ad7373, #676767);
    border: 2px solid #E73701;
    border-radius: 20px;
    width: 280px;
    min-height: 170px;
    padding: 30px 25px;
    text-align: center;
    box-shadow: 0 8px 25px rgba(231, 55, 1, 0.3);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
  }

  .stat-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 15px 45px rgba(231, 55, 1, 0.6);
  }

  .stat-icon {
    margin-bottom: 12px;
    transition: transform 0.3s ease;
  }

  .stat-card:hover .stat-icon {
    transform: scale(1.2);
  }

  .stat-number {
    font-size: 54px;
    font-weight: 900;
    color: #000;
    margin: 0;
    line-height: 1;
  }

  .stat-suffix {
    font-size: 28px;
    font-weight: 700;
    color: #FF552E;
    margin-left: 6px;
    vertical-align: super;
  }

  .stat-label {
    margin-top: 12px;
    font-size: 18px;
    font-weight: 600;
    color: #ccc;
    text-transform: uppercase;
    letter-spacing: 1.5px;
  }

  /* responsive */
  @media (max-width: 992px) {
    .stat-card {
      width: 240px;
      padding: 25px 20px;
    }
    .stat-number {
      font-size: 46px;
    }
    .stat-suffix {
      font-size: 24px;
    }
  }

  @media (max-width: 576px) {
    .modern-stats-inner {
      gap: 20px;
    }
    .stat-card {
      width: 100%;
      max-width: 360px;
      padding: 20px 18px;
      border-radius: 16px;
    }
    .stat-number {
      font-size: 40px;
    }
    .stat-suffix {
      font-size: 20px;
    }
    .stat-label {
      font-size: 16px;
    }
  }
  </style>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    const statCards = document.querySelectorAll('.stat-card');
    const options = { root: null, rootMargin: '0px', threshold: 0.35 };

    const animateCounter = (el) => {
      const counter = el.querySelector('.stat-number');
      const target = +counter.getAttribute('data-target');
      const duration = 1500; // ms
      const start = performance.now();
      const startValue = 0;

      const step = (now) => {
        const progress = Math.min((now - start) / duration, 1);
        // easing (easeOutCubic)
        const eased = 1 - Math.pow(1 - progress, 3);
        const value = Math.floor(startValue + (target - startValue) * eased);
        counter.textContent = value.toLocaleString();
        if (progress < 1) {
          requestAnimationFrame(step);
        } else {
          counter.textContent = target.toLocaleString();
        }
      };

      requestAnimationFrame(step);
    };

    const observer = new IntersectionObserver((entries, obs) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          const card = entry.target;
          if (!card.classList.contains('animated')) {
            card.classList.add('animated');
            animateCounter(card);
            obs.unobserve(card);
          }
        }
      });
    }, options);

    statCards.forEach(card => observer.observe(card));
  });
</script>


<!-- Start Stats modification. -->

<!-- Swiper CSS & JS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.css" />
<script src="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
  new Swiper('.gallery-slider-unique .uniqueGallerySwiper', {
    loop: true,
    spaceBetween: 24,
    speed: 800,
    grabCursor: true,
    centeredSlides: true,
    autoplay: {
            delay: 3000,
            disableOnInteraction: false
          },
          slidesPerView: 1, // صورة واحدة في كل الشاشات
        navigation: {
            nextEl: '.gallery-slider-unique .gallery-next',
            prevEl: '.gallery-slider-unique .gallery-prev',
        }
    });
});
</script>

<style>
.gallery-slider-unique .swiper-slide {
  height: 500px; /* ارتفاع موحد */
  border-radius: 18px;
  overflow: hidden;
  display: flex;
  align-items: center;
  justify-content: center;
  background: #000; /* خلفية لملء الفراغات */
}

.gallery-slider-unique .slide-link {
  width: 100%;
  height: 100%;
  display: flex;
}

.gallery-slider-unique .slide-img {
  width: 100%;
  height: 100%;
  object-fit: contain; /* الصورة كاملة بدون قص أو تمدد */
  border-radius: 18px;
  background: #000;
}

/* أزرار التنقل */
.gallery-slider-unique .custom-swiper-btn {
  position: absolute;
  top: 50%;
  transform: translateY(-50%);
  width: 48px;
  height: 48px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  background: rgba(231,55,1,0.95);
  z-index: 20;
  cursor: pointer;
  transition: transform .2s ease, background .3s ease;
}
.gallery-slider-unique .custom-swiper-btn:hover {
  transform: translateY(-50%) scale(1.1);
  background: rgba(200,30,0,1);
}
.gallery-slider-unique .gallery-prev { left: 8px; }
.gallery-slider-unique .gallery-next { right: 8px; }

@media (max-width: 768px) {
  .gallery-slider-unique .swiper-slide { height: 320px; }
}
</style>


{{-- Swiper CSS & JS --}}
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.css" />
<script src="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.js"></script>

{{-- Swiper init --}}
<script>
    const swiper = new Swiper('.gallerySwiper', {
        loop: true,
        spaceBetween: 24,
        effect: 'slide', // ممكن تغيّره لـ "fade" لو تحب
        speed: 800,
        grabCursor: true,
        navigation: {
            nextEl: '.swiper-button-next',
            prevEl: '.swiper-button-prev',
        },
          breakpoints: {
            0: {
              slidesPerView: 1,
              },
          768: {
              slidesPerView: 2,
          },
          1024: {
              slidesPerView: 4,
          },
          },
    });
</script>


<!-- Swiper JS -->
<script src="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.js"></script>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const swiper = new Swiper(".gallerySwiper", {
            slidesPerView: 4,
            spaceBetween: 20,
            loop: true,
            navigation: {
                nextEl: ".button-next",
                prevEl: ".button-prev",
            },
            breakpoints: {
                320: { slidesPerView: 1 },
                576: { slidesPerView: 2 },
                768: { slidesPerView: 3 },
                992: { slidesPerView: 4 }
            }
        });

        // تفعيل lightgallery على الصور
        lightGallery(document.querySelector('.gallerySwiper .swiper-wrapper'), {
            selector: 'a[data-lightgallery="item"]',
            plugins: [lgZoom],
            speed: 500,
        });
    });
</script>

@endsection
