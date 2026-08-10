<section class="banner-one">
    {{-- صورة أو فيديو --}}
    @if($home_slider && $home_slider->media_type == 'image')
        <div class="banner-one__bg jarallax"
             data-jarallax
             data-speed="0.2"
             data-imgPosition="50% 0%"
             style="background-image: url('{{ asset('public/'.$home_slider->media_path) }}');">
        </div>
    @elseif($home_slider && $home_slider->media_type == 'video')
        <div class="banner-one__bg" style="padding: 0;">
            <video autoplay muted loop playsinline
                   style="width: 100%; height: 100vh; object-fit: cover; display: block;">
                <source src="{{ asset('public/'.$home_slider->media_path) }}" type="video/mp4">
                {{ __('home_sections.your_browser_does_not_support_video') }}
            </video>
        </div>
    @endif

    <div class="container">
        <div class="banner-one__inner text-center px-3">
            {{-- العداد الزمني --}}
            @if($event && $event->event_date)
                <div class="banner-one__countdown-timer-box"
                     data-aos="fade-up"
                     data-aos-delay="200"
                     style="font-family: 'Open Sans', sans-serif; font-size: 25px;">
                    <div class="countdown time-countdown-two"
                         data-countdown-time="{{ \Carbon\Carbon::parse($event->event_date)->format('Y/m/d H:i:s') }}">
                    </div>
                </div>
            @endif

            {{-- اسم الحدث --}}
            <h2 class="banner-one__title"
                style="color:white; font-family: 'Barlow Condensed', sans-serif; font-size: 60px;"
                data-aos="fade-in"
                data-aos-delay="600">
                {{ $event->name_en ?? 'Event Title' }}
            </h2>

            {{-- التاريخ --}}
            <p class="banner-one__date"
               data-aos="fade-up"
               data-aos-delay="500"
               style="font-family: 'Open Sans', sans-serif; font-size: 24px; color: #fff;">
            {{ \Carbon\Carbon::parse($event->event_date)->translatedFormat('F jS, Y') }}

            </p>

            {{-- العنوان --}}
            <p class="banner-one__address"
               data-aos="fade-up"
               data-aos-delay="700"
               style="font-family: 'Open Sans', sans-serif; font-size: 24px; color: #fff;">
                {{ $event->location ?? 'Event Location' }}
            </p>

            {{-- الزر --}}
            <div class="banner-one__btn-box" data-aos="fade-up" data-aos-delay="900">
                <a href="{{ route('web.becomesponsor') }}" class="banner-one__btn thm-btn" style="text-decoration: none;">
                    Become Sponsor <span class="icon-arrow-right"></span>
                </a>
            </div>
        </div>
    </div>
</section>

{{-- AOS Animation --}}
<link href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>
<script>
    AOS.init({
        duration: 500,
        once: true
    });
</script>

{{-- Responsive Custom Style --}}
<style>
    @media (max-width: 768px) {
        .banner-one__title {
            font-size: 32px !important;
        }

        .banner-one__date,
        .banner-one__address,
        .banner-one__countdown-timer-box {
            font-size: 18px !important;
        }

        .banner-one__btn {
            font-size: 16px;
            padding: 10px 20px;
        }

        .banner-one__inner {
            padding-top: 40px;
            padding-bottom: 40px;
        }
    }

    @media (max-width: 480px) {
        .banner-one__title {
            font-size: 28px !important;
        }

        .banner-one__btn {
            padding: 8px 16px;
            font-size: 14px;
        }
    }


@media (max-width: 576px) {
  .banner-one__countdown-timer-box {
    font-size: 14px;
  }

  .banner-one__countdown-timer-box .time-countdown-two {
    display: flex;
    justify-content: center;
    align-items: center;
    flex-wrap: nowrap;
    gap: 6px;
    padding: 33px;
    margin: 0 auto;
  }

  .banner-one__countdown-timer-box .time-countdown-two li {
    list-style: none;
    margin: 0;
    padding: 0;
    flex: 0 0 auto;
  }

  .banner-one__countdown-timer-box .time-countdown-two .box {
    width: 52px;
    height: 52px;
    border-radius: 6px;
    padding: 4px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    position: relative;
  }

  .banner-one__countdown-timer-box .time-countdown-two .box:before {
    content: "";
    position: absolute;
    inset: 0;
    border-radius: 6px;
    border: 2px solid transparent;
    background: linear-gradient(180deg, #FFE986, rgba(140, 30, 13, .33)) border-box;
    -webkit-mask: linear-gradient(#fff 0 0) padding-box, linear-gradient(#fff 0 0);
    -webkit-mask-composite: xor;
    mask-composite: exclude;
    transition: all 500ms ease;
    z-index: -1;
  }

  .banner-one__countdown-timer-box .time-countdown-two .box span {
    font-size: 12px;
    font-weight: 700;
    line-height: 1.2;
    color: white;
    text-align: center;
    text-shadow: 0 1px 2px rgba(0,0,0,0.3);
  }

  .banner-one__countdown-timer-box .time-countdown-two .box .timeRef {
    font-size: 9px;
    margin-top: 2px;
    line-height: 1;
    color: white;
    text-shadow: 0 1px 2px rgba(0,0,0,0.3);
  }
}


@media only screen and (max-width: 767px) {
    .banner-one {
        padding: 120px 0 220px;
    }
}
</style>
