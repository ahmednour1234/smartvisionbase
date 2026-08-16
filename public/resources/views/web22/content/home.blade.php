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

@media (max-width: 767px) {
  .speaker-card {
    display: none;
  }

  .speaker-card:nth-child(-n+4) {
    display: block;
  }
}

.btn-sm, .btn-group-sm > .btn {
    padding: 0.25rem 0.5rem;
    font-size: 18px;
    line-height: 1.5;
    border-radius: 0.2rem;
}
  @media (max-width: 576px) {
    .promo-title {
      font-size: 45px !important;
    }
  }
    @media (max-width: 991.98px) { /* أقل من شاشة اللابتوب */
        .responsive-title {
            font-size: 3rem !important;
        }
    }
    </style>
             @php
    use Carbon\Carbon;

    $start = Carbon::parse($event->event_date);
    $end = Carbon::parse($event->end_date);

    $startDay = $start->translatedFormat('d');
    $endDay = $end->translatedFormat('d');

    $startMonth = $start->translatedFormat('F');
    $endMonth = $end->translatedFormat('F');

    $startYear = $start->translatedFormat('Y');
    $endYear = $end->translatedFormat('Y');

    $durationDays = $start->diffInDays($end) + 1;

    // النص الإنجليزي حسب الفروقات
    if ($startYear === $endYear && $startMonth === $endMonth) {
        $rangeText = "{$durationDays} days event: {$startDay}–{$endDay} {$startMonth} {$startYear}";
    } elseif ($startYear === $endYear) {
        $rangeText = "Event: {$startDay} {$startMonth} – {$endDay} {$endMonth} {$startYear}";
    } else {
        $rangeText = "Event: {$startDay} {$startMonth} {$startYear} – {$endDay} {$endMonth} {$endYear}";
    }
@endphp
@php

    $start = Carbon::parse($event->event_date);
    $end = Carbon::parse($event->end_date);

    $startDay = $start->translatedFormat('d');
    $endDay = $end->translatedFormat('d');

    $startMonth = $start->translatedFormat('F');
    $endMonth = $end->translatedFormat('F');

    $startYear = $start->translatedFormat('Y');
    $endYear = $end->translatedFormat('Y');

    $startTime = $start->translatedFormat('h:i A');
    $endTime = $end->translatedFormat('h:i A');

    $durationDays = $start->diffInDays($end) + 1;

    if ($startYear === $endYear && $startMonth === $endMonth) {
        $rangeText = " {$startDay}–{$endDay} {$startMonth} {$startYear}";
    } elseif ($startYear === $endYear) {
        $rangeText = "{$startDay} {$startMonth} – {$endDay} {$endMonth} {$startYear}";
    } else {
        $rangeText = "{$startDay} {$startMonth} {$startYear} – {$endDay} {$endMonth} {$endYear}";
    }
@endphp

    @if ($home_slider)
        <section class="section section-swiper-absoulte context-dark text-center wow fadeIn">
            <!-- Waves-->
            <!--<canvas class="waves" data-speed="5" data-wave-width="100%" data-animation="SineInOut"></canvas>-->
            <!-- Swiper Content-->
            <div class="section-swiper-content">
                <div class="container">
                    <div class="row justify-content-lg-center">
                        <div class="col-lg-10 homecontent">

                            @if ($home_slider)
                                @php
                                    $locale = app()->getLocale();
                                    $title = $home_slider->title[$locale] ?? '';
                                    $description = $home_slider->description[$locale] ?? '';
                                @endphp

                                <h4 class="wow fadeInUp text-spacing-200" data-wow-delay=".8s"
                                    data-caption-animate="fadeInUp" data-caption-delay="300" data-caption-duration="900">
                                    {{ $description }}
                                </h4>



<h1 class="wow fadeScale responsive-title" data-caption-delay="100" data-caption-duration="900" style="font-size:6rem;">
    {{ $title }}
</h1>

                            @endif
                           <ul class="list-inline list-inline-md wow mt-4" data-splitting data-wow-delay="1.5s">
                                     <li>
                    <div class="unit unit-spacing-xs align-items-center">
                      <div class="unit-left line-height-reset">
                        <svg class="svg-icon-sm svg-icon-primary" role="img">
                          <use xlink:href="{{asset('public/web/assets/images/svg/sprite.svg#small-calendar')}}"></use>
                        </svg>
                      </div>
                      <div class="unit-body">


<h5 class="text-spacing-100">
    <span class="big">

<time datetime="{{ $event->event_date }}">
    {{ $rangeText }} | {{ $startTime }} – {{ $endTime }}
</time>
    </span>

</h5>


                      </div>
                    </div>
                  </li>
                  <li>
                    <div class="unit unit-spacing-xs align-items-center">
                      <div class="unit-left line-height-reset">
                        <svg class="svg-icon-sm svg-icon-primary" role="img">
                          <use xlink:href="{{asset('public/web/assets/images/svg/sprite.svg#earth-globe')}}"></use>
                        </svg>
                      </div>
                      <div class="unit-body">
                        <h5 class="text-spacing-100"><span class="big">{{$event->location??''}}</span></h5>
                      </div>
                    </div>
                  </li>

                </ul>
                        </div>
                    </div>
                </div>
                <!-- Thumbnail Video Link-->

            </div>
            <!-- Swiper Slider Absolute-->
            <div class="swiper-container swiper-slider swiper-slider-1" data-loop="true" data-simulate-touch="false"
                data-autoplay="8500" data-direction="horizontal" data-effect="fade">
                <div class="swiper-wrapper">
                    <!-- Swiper Slide 01-->
                    @if ($home_slider)
                        @if ($home_slider->media_type === 'image')
                            <div class="swiper-slide with-dark-overlay" data-slide-bg="{{ asset('public/'.$home_slider->media_path) }}"></div>
                        @elseif($home_slider->media_type === 'video')
                            <div class="swiper-slide">
                                <video autoplay muted loop playsinline
                                    style="width: 100%; height: 100vh; object-fit: cover; display: block;">
                                    <source src="{{ asset('public/'.$home_slider->media_path) }}" type="video/mp4">
                                    {{ __('home_sections.your_browser_does_not_support_video') }}
                                </video>
                            </div>
                        @endif
                    @endif <!-- Swiper Slide 02-->
                </div>
            </div>
        </section>
    @endif
@foreach ($sections as $section)
    @switch($section->id)


        @case(2)
            @include('web.content.sections.promo', ['section' => $section])
            @break

        @case(3)
            @include('web.content.sections.about', ['section' => $section, 'event' => $event])
            @break

        @case(4)
            @include('web.content.sections.speaker', ['section' => $section, 'speakers' => $speakers])
            @break

        @case(5)
            @include('web.content.sections.schedule', ['section' => $section, 'schedules' => $schedules])
            @break

        @case(6)
            @include('web.content.sections.gallery', ['section' => $section, 'gallieries' => $gallieries])
            @break

        @case(7)
            @include('web.content.sections.sponsor', ['section' => $section, 'sponsors' => $sponsors])
            @break

        @case(8)
            @include('web.content.sections.blog', ['section' => $section, 'blogs' => $blogs])
            @break
    @endswitch
@endforeach


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

    <!-- Section Newsletter Sign Up-->
@endsection
