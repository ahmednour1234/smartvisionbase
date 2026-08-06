@extends('web.layouts.app')

@section('content')
<style>
    .pe-4 {
    padding-right: 7rem !important;
}
   @import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@700&display=swap');

        .page-header__inner {
            text-align: center;
            padding: 95px 0 60px;
        }

  #typed-title {
    display: inline-block;
    overflow: hidden;
    white-space: nowrap;
    border-right: 3px solid #FFE986;
    font-family: 'Montserrat', sans-serif;
    font-size: 60px;

    background: linear-gradient(90deg, #FFE986 0%, #C48127 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    color: transparent;

    animation: blink-caret 0.75s step-end infinite;
}


        #typed-title.finished {
            border-right: none;
            animation: none;
        }

        @keyframes blink-caret {
            0%, 100% { border-color: transparent; }
            50% { border-color: #FFE986; }
        }

        @media (max-width: 767.98px) {
            #typed-title {
                font-size: 32px;
            }
        }
  #typewriter-title {
    font-size: 48px;
    font-weight: 700;
    text-transform: capitalize;
    background: linear-gradient(90deg, #FFE986 0%, #C48127 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    color: transparent;
    white-space: nowrap;
    overflow: hidden;
    border-right: 3px solid #FFE986;
    display: inline-block;
    animation: blink 0.75s step-end infinite;
}

@keyframes blink {
  50% { border-color: transparent; }
}

   @import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@700&display=swap');

    .search-container .form-control {
        background-color: rgba(255, 255, 255, 0.1);
        color: #fff;
        border: 1px solid rgba(255, 255, 255, 0.3);
    }

    .search-container .form-control::placeholder {
        color: rgba(255, 255, 255, 0.6);
    }

    .search-container .btn {
        background-color: #E73780;
        border: none;
    }

    .page-header__inner {
      text-align: center;
      padding: 95px 0 60px;
    }

#typed-title {
    display: inline-block;
    overflow: hidden;
    white-space: nowrap;
    border-right: 3px solid #FFE986;
    font-family: 'Montserrat', sans-serif;
    font-size: 60px;
    background: linear-gradient(90deg, #FFE986 0%, #C48127 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    animation: blink-caret 0.75s step-end infinite;
}

    #typed-title.finished {
        border-right: none;
        animation: none;
    }

    @keyframes blink-caret {
        0%, 100% { border-color: transparent; }
        50% { border-color: #FFE986; }
    }


    @media (max-width: 1365px) {
        #typed-title { font-size: 60px; }
        .company-card { padding: 20px; }
    }

    @media (max-width: 1023px) {
        #typed-title { font-size: 42px; }
        .company-image { width: 140px; height: 140px; }
        .company-card { padding: 15px; }
    }

    @media (max-width: 767.98px) {
        #typed-title { font-size: 32px; }
        .company-image { width: 120px; height: 120px; }
        .company-title { font-size: 20px; }
        .company-card { padding: 10px; }
    }
    @media (max-width: 768px) {
        .section-title-responsive {
            font-size: 40px !important;
        }
    }
        @import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@700&display=swap');

    .search-container .form-control {
        background-color: rgba(255, 255, 255, 0.1);
        color: #fff;
        border: 1px solid rgba(255, 255, 255, 0.3);
    }

    .search-container .form-control::placeholder {
        color: rgba(255, 255, 255, 0.6);
    }

    .search-container .btn {
        background-color: #E73780;
        border: none;
    }

    .page-header__inner {
      text-align: center;
      padding: 95px 0 60px;
    }

    #typed-title {
        display: inline-block;
        overflow: hidden;
        white-space: nowrap;
        border-right: 3px solid #FFE986;
        font-family: 'Montserrat', sans-serif;
        font-size: 60px;
        background: linear-gradient(90deg, #FFE986 0%, #C48127 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        animation: blink-caret 0.75s step-end infinite;
    }

    #typed-title.finished {
        border-right: none;
        animation: none;
    }

    @keyframes blink-caret {
        0%, 100% { border-color: transparent; }
        50% { border-color: #FFE986; }
    }

    .section-title-responsive {
        font-size: 60px;
        width: 80%;
        max-width: 800px;
        font-weight: 800;
        background: linear-gradient(90deg, #fff, #fff);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        margin: 0 auto 40px auto;
        line-height: 1.3;
    }

    @media (max-width: 1365px) {
        #typed-title { font-size: 60px; }
    }

    @media (max-width: 1023px) {
        #typed-title { font-size: 42px; }
    }

    @media (max-width: 767.98px) {
        #typed-title { font-size: 32px; }
        .section-title-responsive { font-size: 40px !important; }
    }

    .desktop-only { display: none; }
    .mobile-only-page { display: none; }

    @media (min-width: 768px) {
        .desktop-only { display: block; }
    }

    @media (max-width: 767.98px) {
        .mobile-only-page { display: block; }
    }
 #searchInput {
    display: none !important;
}

</style>

    <!--Page Header Start-->
<section class="page-header">
    {{-- الخلفية الديناميكية --}}

   <div class="container">
        <div class="page-header__inner">
            <h2 id="typed-title"></h2>
        </div>
    </div>
</section>        <!--Page Header End-->
@php
    $locale = app()->getLocale(); // "en" أو "ar"
@endphp

<section class="buy-ticket">
    <div class="container">
        <div class="row">
            {{-- معلومات الفعالية --}}
            <div class="col-xl-6">
                <div class="buy-ticket__left wow fadeInLeft" data-wow-delay="100ms">
                    <!--<ul class="buy-ticket__address list-unstyled">-->
                    <!--    <li>-->
                    <!--        <div class="icon">-->
                    <!--            <span class="icon-clock"></span>-->
                    <!--        </div>-->
                    <!--        <div class="text">-->
                    <!--            <p>{{ $event->location ?? 'Event location' }}</p>-->
                    <!--        </div>-->
                    <!--    </li>-->
                    <!--    <li>-->
                            <!--<div class="icon">-->
                            <!--    <span class="icon-pin"></span>-->
                            <!--</div>-->
                            <!--<div class="text">-->
                            <!--    <p>-->
                            <!--        {{ \Carbon\Carbon::parse($event->event_date)->format('h:i A d F Y') }}-->
                            <!--    </p>-->
                            <!--</div>-->
                    <!--    </li>-->
                    <!--</ul>-->

                    {{-- العنوان من JSON --}}
<h3 class="buy-ticket__title"
    style="background: linear-gradient(90deg, #FFE986, #C48127);
           -webkit-background-clip: text;
           -webkit-text-fill-color: transparent;
           background-clip: text;
           color: transparent;">
    {{ $aboutSection?->title[$locale] ?? 'About Event' }}
</h3>


                    {{-- الوصف من JSON --}}
                    <p class="buy-ticket__text">
                        {!! nl2br(e($aboutSection?->description[$locale] ?? 'Event description')) !!}
                    </p>

                    {{-- الأزرار --}}
                    <div class="buy-ticket__btn-box">
                        <a href="{{route('web.becomesponsor')}}" class="buy-ticket__btn-1 thm-btn">
                            {{ $locale === 'ar' ? 'احجز تذكرتك' : 'Become Sponsor' }}
                            <span class="icon-arrow-right"></span>
                        </a>
                        <a href="{{route('web.contact')}}" class="buy-ticket__btn-2 thm-btn">
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
                        <img src="{{ asset('public/'.$aboutSection->media_path) }}" alt="{{ $aboutSection?->title[$locale] ?? 'Event Image' }}">
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@include('web.content.sections.note')

@php
use App\Models\HomeSection;
use App\Models\Company;
$section=HomeSection::Where('id',9)->first();
$companies=Company::where('active',1)->orderBy('orders', 'asc')->paginate(25);
@endphp
        @include('web.content.sections.voting', ['section' => $section,'companies' => $companies])



        <!--CTA One Start-->

        <!--CTA One End-->
{{-- في ملف layout الرئيسي --}}
<link href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        AOS.init({
            duration: 1000,
            once: true
        });
    });
</script>
<script>
        document.addEventListener("DOMContentLoaded", function () {
            const text = @json(('About Us'));
            const target = document.getElementById("typed-title");
            let index = 0;

            function typeWriter() {
                if (index < text.length) {
                    target.innerHTML += text.charAt(index);
                    index++;
                    setTimeout(typeWriter, 100);
                } else {
                    target.classList.add('finished');
                }
            }

            typeWriter();
        });
    </script>
<script>
  const text = "Voting";
  const el = document.getElementById("typewriter-title");
  let index = 0;
  let hasStarted = false;

  function typeWriter() {
    if (index < text.length) {
      el.innerHTML += text.charAt(index);
      index++;
      setTimeout(typeWriter, 150);
    } else {
      el.style.borderRight = "none";
    }
  }

  const observer = new IntersectionObserver(function (entries, observer) {
    entries.forEach(entry => {
      if (entry.isIntersecting && !hasStarted) {
        hasStarted = true;
        typeWriter();
        observer.unobserve(el); // نوقف المراقبة بعد التشغيل مرة واحدة
      }
    });
  }, { threshold: 0.5 }); // يشغل التأثير عندما يكون 50% من العنصر ظاهرًا

  observer.observe(el);
</script>


@endsection
