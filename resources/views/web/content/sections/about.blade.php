@php
    $locale = app()->getLocale();
@endphp

<section class="buy-ticket">
    <div class="container">
        <div class="row">
            {{-- معلومات الفعالية --}}
            <div class="col-xl-6">
                <div class="buy-ticket__left wow fadeInLeft" data-wow-delay="100ms">
                    <ul class="buy-ticket__address list-unstyled">
                        <li>
                            <div class="icon">
                                <span class="icon-clock"></span>
                            </div>
                            <div class="text">
                                <p>{{ $event->location ?? 'Event location' }}</p>
                            </div>
                        </li>
                        <li>
                            <div class="icon">
                                <span class="icon-pin"></span>
                            </div>
                            <div class="text">
                                <p>
                                    {{ \Carbon\Carbon::parse($event->event_date)->format('h:i A d F Y') }}
                                </p>
                            </div>
                        </li>
                    </ul>

                    {{-- العنوان من JSON --}}
                <h3 class="buy-ticket__title"
    style="background: linear-gradient(90deg, #FFE986 0%, #C48127 100%);
           -webkit-background-clip: text;
           -webkit-text-fill-color: transparent;
           background-clip: text;
           color: transparent;">
    {{ $section?->title[$locale] ?? 'About Event' }}
</h3>

                    {{-- الوصف من JSON --}}
                    <p class="buy-ticket__text" style="color:white;">
                        {!! nl2br(e($section?->description[$locale] ?? 'Event description')) !!}
                    </p>

                    {{-- الأزرار --}}
                    <div class="buy-ticket__btn-box">
                        <a href="{{ route('web.becomesponsor') }}" style="text-decoration:none;" class="buy-ticket__btn-1 thm-btn">
                            {{ $locale === 'ar' ? 'احجز تذكرتك' : 'Become Sponsor' }}
                            <span class="icon-arrow-right"></span>
                        </a>
                        <a href="#" class="buy-ticket__btn-2 thm-btn" style="text-decoration:none;">
                            {{ $locale === 'ar' ? 'تواصل معنا' : 'Contact Us' }}
                            <span class="icon-arrow-right"></span>
                        </a>
                    </div>
                </div>
            </div>

            {{-- صورة القسم --}}
            <div class="col-xl-6">
                <div class="buy-ticket__right wow fadeInRight" data-wow-delay="300ms">
                    <div class="buy-ticket__img">
                        <img src="{{ asset('public/'.$section->media_path) }}" alt="{{ $section?->title[$locale] ?? 'Event Image' }}">
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
