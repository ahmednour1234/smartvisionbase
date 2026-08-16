    <!-- Section Who Is Speaking-->
    @php
        $locale = app()->getLocale();

    @endphp

    @if ($section && $section->media_type === 'image')
        <section class="parallax-container section mt-5" data-parallax-img="{{ asset('public/'.$speaker_section->media_path) }}">
            <div class="parallax-content section-lg context-dark text-center">
                <div class="container">
<h6 style="color:#cc252e; font-weight:bold;">
    {{ $section->title[$locale] ?? '' }}
</h6>
                    <h3>{{ $section->description[$locale] ?? '' }}</h3>

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
                                            @if ($speaker->facebook)
                                                <li><a class="icon icon-xs fa-facebook-f" href="{{ $speaker->facebook }}"
                                                        target="_blank"></a></li>
                                            @endif
                                            @if ($speaker->tiktok)
                                                <li><a class="icon icon-xs fa-tiktok" href="{{ $speaker->tiktok }}"
                                                        target="_blank"></a></li>
                                            @endif
                                            @if ($speaker->youtube)
                                                <li><a class="icon icon-xs fa-youtube-play"
                                                        href="{{ $speaker->youtube }}" target="_blank"></a></li>
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
