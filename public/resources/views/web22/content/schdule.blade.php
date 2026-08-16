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
        }

        .sponsor-item {
            flex: 0 0 auto;
            width: 160px;
            height: 100px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            padding: 10px;
        }

        .sponsor-item img {
            max-width: 100%;
            max-height: 80px;
            object-fit: contain;
        }

        @keyframes sponsor-scroll {
            0% {
                transform: translateX(0);
            }

            100% {
                transform: translateX(-50%);
            }
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
        style="background-image: url({{asset('web/assets/images/bg-breadcrumbs-01-1894x424.jpg')}});">
        <div class="container">

            <h3 class="breadcrumbs-custom-title">Schdule Event</h3>
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
            <h6 class="mt-1">{{ $section->title[$locale] ?? '' }}</h6>
            <h3 class="mt-3">{{ $section->description[$locale] ?? 'Event Agenda' }}</h3>

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
@if ($schedule->speakers->count())
    <span class="schedule-classic-author" style="color:black;">
        by&nbsp;
        @foreach ($schedule->speakers as $index => $speaker)
            <span
                style="color: #cc252e; cursor: pointer;"
                onclick="window.location.href='{{ route('web.speaker') }}#speaker-{{ $speaker->id }}'"
            >
                {{ $speaker->name_en }}
            </span>
            @if (!$loop->last)
                <span style="color: black;"> - </span>
            @endif
        @endforeach
    </span>
@endif




                                                                </span>
                                                            </span>
                                                        </span>
                                                    </span>
                                                </a>
                                            </div>
                                        </div>
                                        <div class="collapse" id="collapse-{{ $schedule->id }}">
                                            <div class="card-body">
                                                <p>{{ $schedule->{'description_' . $locale} }}</p>
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

                </div>

            </div>

        </div>
    </section>


@endsection
