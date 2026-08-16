<style>
    a{
        text-decoration: none;
    }
 .card {
    /* ... باقي المتغيرات كما هي ... */
    border: none !important;
}

</style>

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
            <h6 class="mt-1 sub-tit">{{ $section->title[$locale] ?? '' }}</h6>
            <h3 class="mt-4 gre-title" style="font-size: 40px;font-weight: bolder;">{{ $section->description[$locale] ?? 'Event Agenda' }}</h3>

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
                                                <p class="collapsed" data-toggle="collapse"
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
                                                </p>
                                            </div>
                                        </div>
                                        <div class="collapse" id="collapse-{{ $schedule->id }}">
                                            <div class="card-body">
                                                <p>{{ $schedule->{'description_' . $locale} }}</p>
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

                </div>
                 <div class="text-center">
            <a class="button button-secondary box-with-triangle-right wow fadeScale mt-2"
               href="{{ route('web.schdule') }}" data-triangle=".button-overlay">
                <span>More Schdule</span>
                <span class="button-overlay"></span>
            </a>
        </div>
            </div>

        </div>
    </section>
