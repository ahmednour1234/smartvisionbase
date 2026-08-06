  <!-- Section Biggest 2019 Digital Conference-->
    @php
        $section = \App\Models\HomeSection::where('is_active', true)->where('id', 3)->first();
    @endphp

    @if ($section)
        <section class="section section-lg bg-default wow fadeIn mt-5 mb-5">
            <div class="container">
                <div class="row row-30 justify-content-center">
                    <div class="col-md-10 col-lg-6 col-xl-5">
                        <h3 class="heading-lg-postfix-15">{{ $section->title[app()->getLocale()] ?? '' }}</h3>
                        <p style="color:black;">{{ $section->description[app()->getLocale()] ?? '' }}</p>

                        <!-- List Inline-->
                        <!--<ul class="list-inline list-inline-xl">-->
                        <!--    <li>-->
                        <!--        <div class="unit">-->
                        <!--            <div class="unit-left">-->
                        <!--                <svg class="svg-icon-sm svg-icon-primary" role="img">-->
                        <!--                    <use xlink:href="{{ asset('web/assets/images/svg/sprite.svg#earth-globe') }}">-->
                        <!--                    </use>-->
                        <!--                </svg>-->
                        <!--            </div>-->
                        <!--            <div class="unit-body">-->
                        <!--                <h5>{{ __('home.where') }}</h5>-->
                        <!--                <p>{{ app()->getLocale() == 'ar' ? $event->address_ar : $event->address_en }}</p>-->
                        <!--            </div>-->

                        <!--        </div>-->
                        <!--    </li>-->
                        <!--    <li>-->
                        <!--        <div class="unit">-->
                        <!--            <div class="unit-left">-->
                        <!--                <svg class="svg-icon-sm svg-icon-primary" role="img">-->
                        <!--                    <use-->
                        <!--                        xlink:href="{{ asset('web/assets/images/svg/sprite.svg#small-calendar') }}">-->
                        <!--                    </use>-->
                        <!--                </svg>-->
                        <!--            </div>-->
                        <!--            <div class="unit-body">-->
                        <!--                <h5>{{ __('home.when') }}</h5>-->
                        <!--                <p>-->
                        <!--                    <time datetime="{{ $event->event_date }}">-->
                        <!--                        {{ \Carbon\Carbon::parse($event->event_date)->locale(app()->getLocale())->isoFormat('D MMMM YYYY') }}-->
                        <!--                    </time>-->
                        <!--                </p>-->
                        <!--            </div>-->
                        <!--        </div>-->
                        <!--    </li>-->
                        <!--</ul>-->

                        <a class="button button-primary" href="{{route('web.becomesponsor')}}" data-triangle=".button-overlay">
                            <span>Become Sponsor</span>
                            <span class="button-overlay"></span>
                        </a>
                    </div>

                    <div class="col-md-10 col-lg-6 col-xl-7 text-md-right">
                        <div class="images-box">
                            <div class="images-box-item images-box-item-right">
                                <div class="wow fadeScale">
                                    @if ($section->media_type === 'image')
                                        <img src="{{ asset('public/'.$section->media_path) }}" alt="About Image"
                                            style="max-width:100%; height:auto;" />
                                    @elseif($section->media_type === 'video')
                                        <video autoplay muted loop playsinline
                                            style="width:100%; height:auto; object-fit:cover; border-radius: 8px;">
                                            <source src="{{ asset('public/'.$section->media_path) }}" type="video/mp4">
                                            {{ __('home_sections.your_browser_does_not_support_video') }}
                                        </video>
                                    @endif
                                </div>
                            </div>

                        </div>
                    </div>

                </div>
            </div>
        </section>
    @endif
