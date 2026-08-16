  @if ($section && $section->media_type === 'image')
        <!-- Section Official Sponsors Scroll -->
        <section class="parallax-container section pt-5 mt-5" data-parallax-img="{{ asset('public/'.$sponsor_section->media_path) }}">
            <div class="parallax-content section-lg context-dark text-center">
                <div class="container">
                    <h6>{{ $section->title[$locale] ?? '' }}</h6>
                    <h3>{{ $section->description[$locale] ?? '' }}</h3>

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
               href="{{ route('web.sponsors') }}" data-triangle=".button-overlay">
                <span>More Sponsors</span>
                <span class="button-overlay"></span>
            </a>
        </div>
            </div>
        </section>
    @endif
