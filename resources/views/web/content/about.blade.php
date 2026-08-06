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

    /* Past Events Gallery Styles */
.past-events-section {
  background: #f4f2ee !important;
    padding: 80px 0;
    position: relative;
    overflow: hidden;
}

.past-events-section::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="50" cy="50" r="1" fill="%23ffffff" opacity="0.03"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
    pointer-events: none;
}

.past-events-title {
    color: #E73701;
    font-size: 48px;
    font-weight: 700;
    margin-bottom: 0;
    text-align: center;
    position: relative;
    z-index: 2;
    text-shadow: 0 2px 10px rgba(0,0,0,0.3);
        background: linear-gradient(270deg, #000000, #E73701, #000000);
    background-size: 600% 600%;
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    animation: gradientShift 5s ease infinite;
}

        @keyframes gradientShift {
            0% {
                background-position: 0% 50%;
            }
            50% {
                background-position: 100% 50%;
            }
            100% {
                background-position: 0% 50%;
            }
        }

.past-events-title::after {
    content: '';
    position: absolute;
    bottom: -10px;
    left: 50%;
    transform: translateX(-50%);
    width: 100px;
    height: 3px;
    background: linear-gradient(90deg, #E73701, #FF552E);
    border-radius: 2px;
}

.past-events-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 20px;
    margin-top: 60px;
    position: relative;
    z-index: 2;
}

.past-event-item {
    position: relative;
    border-radius: 15px;
    overflow: hidden;
    background: #333;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.past-event-item:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 35px rgba(231, 55, 1, 0.3);
}

.past-event-link {
    display: block;
    position: relative;
    text-decoration: none;
}

.past-event-image-container {
    position: relative;
    width: 100%;
    height: 200px;
    overflow: hidden;
}

.past-event-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.4s ease;
}

.past-event-item:hover .past-event-image {
    transform: scale(1.1);
}

.past-event-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(135deg, rgba(231, 55, 1, 0.8), rgba(255, 85, 46, 0.8));
    opacity: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: opacity 0.3s ease;
}

.past-event-item:hover .past-event-overlay {
    opacity: 1;
}

.past-event-zoom-icon {
    transform: scale(0.8);
    transition: transform 0.3s ease;
}

.past-event-item:hover .past-event-zoom-icon {
    transform: scale(1);
}

.past-events-btn {
    background: linear-gradient(135deg, #E73701, #FF552E);
    color: white;
    padding: 15px 35px;
    border-radius: 50px;
    text-decoration: none;
    font-weight: 600;
    font-size: 16px;
    display: inline-flex;
    align-items: center;
    transition: all 0.3s ease;
    border: none;
    box-shadow: 0 5px 20px rgba(231, 55, 1, 0.3);
}

.past-events-btn:hover {
    background: linear-gradient(135deg, #d63301, #e64a2e);
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(231, 55, 1, 0.4);
    color: white;
    text-decoration: none;
}

    </style>
     @php
        $locale = app()->getLocale();

    @endphp
<section class="breadcrumbs-custom bg-image context-dark"
         style="background-image: url({{ asset('public/web/assets/images/bg-breadcrumbs-01-1894x424.jpg') }});">
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
                                <img src="{{ asset('public/'.$aboutSection->media_path) }}"
                                     alt="About Image"
                                     class="img-fluid rounded shadow"
                                     style="max-width: 100%; height: auto;" />
                            @elseif($aboutSection->media_type === 'video')
                                <video autoplay muted loop playsinline
                                       style="width:100%; height:auto; object-fit:cover; border-radius: 8px;">
                                    <source src="{{ asset('public/'.$aboutSection->media_path) }}" type="video/mp4">
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
<!-- Start Stats modification. -->



<!-- Speakers Section -->
@php
use App\Models\HomeSection;
$section=HomeSection::where('id','4')->first();
@endphp
@if($section->is_active==1)
            @include('web.content.sections.speaker', ['section' => $section, 'speakers' => $speakers])
            @endif
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
@php
    use Carbon\Carbon;

    $locale = app()->getLocale();

    // تجميع الـ schedules حسب اليوم (تاريخ البداية)
    $groupedSchedules = [];

    foreach ($schedules as $schedule) {
        $date = Carbon::parse($schedule->start_datetime)->format('Y-m-d');
        $groupedSchedules[$date][] = $schedule;
    }

    // ترتيب الأيام تصاعديًا
    ksort($groupedSchedules);

    // ألوان/كلاسات التابات
    $navClasses = ['nav-link-secondary-darker', 'nav-link-purple-heart', 'nav-link-primary', 'nav-link-secodanry'];

    // أسماء الأيام (مترجمة)
    $dayNames = [__('First Day'), __('Second Day'), __('Third Day'), __('Fourth Day')];
@endphp

    @php
$section=HomeSection::where('id','7')->first();
@endphp
@if($section->is_active==1)

                @include('web.content.sections.sponsor', ['section' => $section, 'sponsors' => $sponsors])
@endif
<section class="section bg-dark past-events-section">
    <div class="container">
        <!-- العنوان -->
        <div class="text-center mb-5">
            <h2 class="past-events-title">Past Event Photos</h2>
        </div>

        <!-- شبكة الصور -->
        <div class="past-events-grid">
            @foreach ($gallieries as $index => $gallery)
                <div class="past-event-item" data-aos="fade-up" data-aos-delay="{{ $index * 100 }}">
                    <a href="{{ asset('public/'.$gallery->image) }}" data-lightgallery="item" class="past-event-link">
                        <div class="past-event-image-container">
                            <img src="{{ asset('public/'.$gallery->image) }}" alt="Past Event {{ $index + 1 }}" class="past-event-image" loading="lazy">
                            <div class="past-event-overlay">
                                <div class="past-event-zoom-icon">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                                        <circle cx="11" cy="11" r="8" stroke="white" stroke-width="2"/>
                                        <path d="m21 21-4.35-4.35" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        <line x1="11" y1="8" x2="11" y2="14" stroke="white" stroke-width="2" stroke-linecap="round"/>
                                        <line x1="8" y1="11" x2="14" y2="11" stroke="white" stroke-width="2" stroke-linecap="round"/>
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>

        <!-- زر عرض المزيد -->
        <div class="text-center mt-5">
            <a class="button button-primary past-events-btn"
               href="{{ route('web.gallery') }}">
                <span>View All Gallery</span>
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" class="ml-2">
                    <path d="M9 6l6 6-6 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </a>
        </div>
    </div>
</section>


  <style>
    /* الخلفية الأساسية */
  .modern-stats-section {
    background: #FFF;
    padding: 40px 0;
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
    background: linear-gradient(145deg, #FFFFFF 0%, #FFFFFF 50%, rgba(231, 55, 1, 0.3) 100%);
    border: 2px solid #ffffff;
    border-radius: 20px;
    width: 280px;
    min-height: 170px;
    padding: 30px 25px;
    text-align: center;
    box-shadow: 0 8px 25px rgb(0 0 0 / 30%);
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
    font-size: 60px;
    font-weight: 900;
    line-height: 1.2;
    margin-bottom: 10px;
    background: linear-gradient(270deg, #000000, #E73701, #000000);
    background-size: 600% 600%;
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    animation: gradientShift 5s ease infinite;
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

  @media (max-width: 576px) {
    .modern-stats-inner {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 15px;
        justify-items: center;
    }

    /* الصف الأول: بوكس واحد في المنتصف */
    .modern-stats-inner .stat-card:nth-child(1) {
        grid-column: span 2;
        justify-self: center;
    }

    /* المربعات بطول وعرض متساوي */
    .stat-card {
        aspect-ratio: 1 / 1;
        width: 100%;
        max-width: 160px; /* حجم أصغر للموبايل */
        padding: 12px 10px; /* تقليل المسافات الداخلية */
    }

    /* الأيقونة أصغر وتبقى جوا البوكس */
    .stat-icon svg {
        width: 28px;
        height: 28px;
    }
    .stat-icon {
        margin-bottom: 6px; /* تقليل المسافة تحت الأيقونة */
    }

    /* الرقم */
    .stat-number {
        font-size: 28px;
        margin-bottom: 4px;
    }

    /* اللاحقة مثل + */
    .stat-suffix {
        font-size: 16px;
        margin-left: 4px;
    }

    /* العنوان */
    .stat-label {
        font-size: 12px;
        margin-top: 4px;
        letter-spacing: 0.5px;
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
