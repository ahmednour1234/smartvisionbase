@php
    use Illuminate\Support\Carbon;

    $locale = app()->getLocale();

    // ✅ date_active: default 1
    $dateActive = (int)($event->date_active ?? 1);

    // ✅ لو date_active = 0 => ممنوع parse للتاريخ
    $eventDateText = null;
    if ($dateActive === 1 && $event && $event->event_date) {
        $eventDateText = Carbon::parse($event->event_date)->format('h:i A d F Y');
    }

    $eventLocation = $event->location ?? 'Event location';
    $sectionTitle = $section?->title[$locale] ?? 'About Event';
    $sectionDesc  = $section?->description[$locale] ?? 'Event description';
    $sectionImg   = $section?->media_path ? asset('public/'.$section->media_path) : null;
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
                                <p>{{ $eventLocation }}</p>
                            </div>
                        </li>

                        <li>
                            <div class="icon">
                                <span class="icon-pin"></span>
                            </div>
                            <div class="text">
                                @if($dateActive === 0)
                                    <p>{{ $locale === 'ar' ? 'قريبًا' : 'Coming Soon' }}</p>
                                @else
                                    <p>{{ $eventDateText ?? ($locale === 'ar' ? 'لم يتم تحديد التاريخ' : 'Date not set') }}</p>
                                @endif
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
                        {{ $sectionTitle }}
                    </h3>

                    {{-- الوصف من JSON --}}
                    <p class="buy-ticket__text" style="color:white;">
                        {!! nl2br(e($sectionDesc)) !!}
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
                        @if($sectionImg)
                            <img src="{{ $sectionImg }}" alt="{{ $sectionTitle }}">
                        @else
                            <div style="width:100%;height:320px;border-radius:12px;background:rgba(255,255,255,0.08);display:flex;align-items:center;justify-content:center;color:#fff;">
                                {{ $locale === 'ar' ? 'لا توجد صورة' : 'No image' }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>
