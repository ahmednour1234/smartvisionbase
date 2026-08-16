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
                                <img src="{{ asset('public/'.$ad->img) }}"
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

 @if ($speaker_section && $speaker_section->media_type === 'image')
        <section class="parallax-container section mt-5" data-parallax-img="{{ asset('public/'.$speaker_section->media_path) }}">
            <div class="parallax-content section-lg context-dark text-center">
                <div class="container">
                    <h6 class="text-secondary">{{ $speaker_section->title[$locale] ?? '' }}</h6>
                    <h3>{{ $speaker_section->description[$locale] ?? '' }}</h3>

                    <div class="row row-30">
                        @foreach ($speakers as $speaker)
                            <div class="col-md-6 col-lg-4  speaker-card">
                                <div class="speaker">
                                    <div class="speaker-img" data-triangle=".speaker-overlay">
                                        <div class="speaker-overlay"></div>
                                        <a href="#">
                                            <img src="{{ asset('public/'.$speaker->image) }}"
                                                alt="{{ $locale == 'ar' ? $speaker->name_ar : $speaker->name_en }}"
                                                width="330" height="354" />
                                        </a>
                                        <ul class="speaker-social-list">
                                            @if ($speaker->linkedin)
                                                <li><a class="icon icon-xs fa-facebook-f" href="{{ $speaker->linkedin }}"
                                                        target="_blank"></a></li>
                                            @endif
                                            @if ($speaker->linkedin)
                                                <li><a class="icon icon-xs fa-twitter" href="{{ $speaker->linkedin }}"
                                                        target="_blank"></a></li>
                                            @endif
                                            @if ($speaker->linkedin)
                                                <li><a class="icon icon-xs fa-youtube-play"
                                                        href="{{ $speaker->linkedin }}" target="_blank"></a></li>
                                            @endif
                                            @if ($speaker->linkedin)
                                                <li><a class="icon icon-xs fa-linkedin" href="{{ $speaker->linkedin }}"
                                                        target="_blank"></a></li>
                                            @endif
                                        </ul>
                                    </div>
                                    <h5 class="speaker-title">
                                        <a
                                            href="#">{{ $locale == 'ar' ? $speaker->name_ar : $speaker->name_en }}</a>
                                    </h5>
                                    <p class="speaker-position">
                                        {{ $locale == 'ar' ? $speaker->title_ar : $speaker->title_en }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <a class="button button-secondary box-with-triangle-right wow fadeScale mt-4"
                        href="{{ route('web.speaker') }}" data-triangle=".button-overlay">
                        <span>{{ __('home.view_all_speakers') }}</span>
                        <span class="button-overlay"></span>
                    </a>
                </div>

            </div>
        </section>
    @endif
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
                                                                    <img src="{{ asset('public/'.$schedule->logo) }}"
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
                                                                xlink:href="{{ asset('public/images/svg/sprite.svg#earth-globe') }}">
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
        @if ($sponsor_section && $sponsor_section->media_type === 'image')
        <!-- Section Official Sponsors Scroll -->
        <section class="parallax-container section pt-5 mt-5" data-parallax-img="{{ asset('public/'.$sponsor_section->media_path) }}">
            <div class="parallax-content section-lg context-dark text-center">
                <div class="container">
                    <h6>{{ $sponsor_section->title[$locale] ?? '' }}</h6>
                    <h3>{{ $sponsor_section->description[$locale] ?? '' }}</h3>

                    <div class="sponsor-auto-scroll mt-4">
                        <div class="sponsor-track">
                            @foreach ($sponsors as $sponsor)
                                <div class="sponsor-item">
                                    <img src="{{ asset('public/'.$sponsor->image) }}" alt="sponsor" />
                                </div>
                            @endforeach

                            {{-- تكرار لنفس الشعارات لتظهر بشكل دائري مستمر --}}
                            @foreach ($sponsors as $sponsor)
                                <div class="sponsor-item">
                                    <img src="{{ asset('public/'.$sponsor->image) }}" alt="sponsor" />
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                 {{-- زر عرض الكل --}}
        <div class="text-center mt-5">
            <a class="button button-secondary box-with-triangle-right wow fadeScale mt-4"
               href="{{ route('web.gallery') }}" data-triangle=".button-overlay">
                <span>More Sponsors</span>
                <span class="button-overlay"></span>
            </a>
        </div>
            </div>
        </section>
    @endif
@if ($gallery_section && $gallery_section->media_type === 'image')

<section class="section bg-default mt-5 pt-5 parallax-container section mb-5">
<div class="parallax-content section-lg context-dark text-center" style="background-color: #F4F3F2;">

    <div class="container">
        <h6 class="text-center" style="color:#cc252e;">{{ $gallery_section->title[$locale] ?? '' }}</h6>
        <h3 class="text-center " style="color:black;">{{ $gallery_section->description[$locale] ?? '' }}</h3>

                    {{-- Slider لجميع الشاشات --}}
            <div class="position-relative">
                <div class="swiper gallerySwiper pb-5 pt-5">
                    <div class="swiper-wrapper">
                        @foreach ($gallieries as $gallery)
                            <div class="swiper-slide">
                                <a href="{{ asset('public/'.$gallery->image) }}" data-lightgallery="item">
                                    <img src="{{ asset('public/'.$gallery->image) }}" alt="gallery">
                                </a>
                            </div>
                        @endforeach
                    </div>

                    {{-- أزرار التنقل --}}
                    <div class="swiper-button-prev custom-swiper-btn"></div>
                    <div class="swiper-button-next custom-swiper-btn"></div>
                </div>
            </div>

            {{-- زر عرض الكل --}}
            <div class="text-center">
                <a class="button button-secondary box-with-triangle-right wow fadeScale mt-2"
                   href="{{ route('web.gallery') }}" data-triangle=".button-overlay">
                    <span>More Gallery</span>
                    <span class="button-overlay"></span>
                </a>
            </div>

    </div>

</section>

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
@endif


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
