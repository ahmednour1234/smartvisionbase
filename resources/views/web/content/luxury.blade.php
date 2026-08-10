@extends('web.layouts.app')

@section('content')
@php $locale = app()->getLocale(); @endphp

<style>
    @import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@700&display=swap');

    .page-header {
        position: relative;
        padding: 100px 0 40px;
        text-align: center;
        overflow: hidden;
    }

    #typed-title {
        font-family: 'Montserrat', sans-serif;
        font-size: 60px;
        font-weight: bold;
        background: linear-gradient(90deg, #FFE986 0%, #C48127 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        border-right: 3px solid #FFE986;
        display: inline-block;
        overflow: hidden;
        white-space: nowrap;
        animation: blink-caret 0.75s step-end infinite;
        position: relative;
        z-index: 2;
    }

    .Title-Overlay {
        position: absolute;
        top: 55%;
        left: 50%;
        transform: translate(-50%, -50%);
        font-family: "Open Sans", sans-serif;
        font-size: 70px;
        font-weight: 700;
        color: transparent;
        -webkit-text-stroke-width: 1px;
        -webkit-text-stroke-color: #4E5156;
        opacity: 0;
        transition: opacity 1s ease;
        white-space: nowrap;
        z-index: 1;
        pointer-events: none;
        user-select: none;
    }

    .Title-Overlay.show {
        opacity: 0.7;
    }

    @keyframes blink-caret {
        0%, 100% { border-color: transparent; }
        50% { border-color: #FFE986; }
    }

    @media (max-width: 768px) {
        #typed-title {
            font-size: 32px;
        }

        .Title-Overlay {
            font-size: 48px;
        }
    }

    .about-section {
        font-size: 20px;
        line-height: 36px;
        text-align: center;
        font-family: "Inter Tight", sans-serif;
        color: #fff;
        max-width: 900px;
        margin: 0 auto 60px auto;
        padding: 0px;
    }

    .about-section p {
        margin-bottom: 20px;
        color: #fff;
        font-weight: 400;
    }

    .speaker-card {
        background: #000;
        border-radius: 16px;
        overflow: hidden;
        transition: transform 0.3s ease;
        height: 100%;
        box-shadow: 0 4px 20px rgba(255, 233, 134, 0.2), 0 6px 30px rgba(196, 129, 39, 0.3);
    }

    .speaker-card:hover {
        transform: translateY(-5px);
    }

    .speaker-inner {
        display: flex;
        flex-direction: column;
        height: 100%;
    }

    .speaker-img-wrapper {
        width: 100%;
        height: 350px;
        overflow: hidden;
        background-color: #111;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .speaker-img-wrapper img {
        width: 100%;
        height: 100%;
        object-fit: contain;
        transition: transform 0.5s ease;
    }

    .speaker-card:hover .speaker-img-wrapper img {
        transform: scale(0.95);
    }

    .speaker-info {
        padding: 0px;
        padding-top:0px ;
        animation: fadeInUp 0.8s ease both;
        text-align: center;
    }

    .speaker-title {
        font-size: 22px;
        font-weight: bold;
        margin-bottom: 5px;
        color: #fff;
        text-transform: capitalize;
    }

    .speaker-position {
        font-size: 15px;
        color: #ccc;
    }

    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    .speaker-info {
    padding: 20px;
    animation: fadeInUp 0.8s ease both;
    text-align: center;
    display: flex;
    flex-direction: column;
    gap: 8px; /* المسافة بين العناصر */
}

.speaker-title {
    font-size: 22px;
    font-weight: bold;
    margin: 0;
    color: #fff;
    text-transform: capitalize;
}

.speaker-meta p {
    margin: 0;
    font-size: 15px;
    color: #bbb;
    line-height: 1.4;
}

.speaker-company {
    font-weight: 500;
    color: #FFD700; /* لون ذهبي مميز */
}

.speaker-position {
    color: #ccc;
}
       .page-header {
        padding: 200px 0 85px;
    }
      @media (max-width: 767.98px) {
        .buy-ticket__img {
            height: 240px;
        }

        .buy-ticket__title {
            font-size: 22px;
        }

        .buy-ticket__text {
            font-size: 14px;
        }

        .typewriter h2 {
            font-size: 32px;
        }
         .page-header {
        padding: 250px 0 0px;
    }
    .Title-Overlay{
        font-size:30px;
    }
    }

</style>

{{-- ✅ العنوان مع الظل --}}
<section class="page-header">
    <div class="container position-relative">
        <div class="Title-Overlay" id="title-overlay">Luxury FX Community Members</div>
        <h2 id="typed-title"></h2>
    </div>
</section>

{{-- 💬 وصف المجتمع --}}
<section class="section section-lg bg-default text-center pb-5">
    <div class="container">
        <div class="about-section">
            <p>
                An exclusive network that brings together the most prominent and influential figures in the world of luxury Forex and high-end financial markets.
            </p>
            <p>
                The Luxury FX Community represents a distinguished circle of elite traders, market leaders, and financial visionaries who embody sophistication, success, and global influence.
            </p>
            <p>
                Their presence at the Middle East Financial Markets Awards Ceremony is strictly by invitation, reflecting the private and prestigious nature of this landmark event.<br>
                <strong>Please note:</strong> The Luxury FX Community is not affiliated with the award nominations or recognitions during the ceremony.
            </p>
        </div>
           <div class="mt-2 text-center">
            <a href="{{ route('web.luxurystore') }}"
class="buy-ticket__btn-1 thm-btn">         Join To Community
            </a>
        </div>

        {{-- 👥 الأعضاء --}}
        <div class="row row-30">
            @foreach ($members as $speaker)
                <div class="col-md-6 col-lg-4 pt-5" id="speaker-{{ $speaker->id }}">
                    <div class="speaker-card">
                        <div class="speaker-inner">
                            <div class="speaker-img-wrapper">
                                <img src="{{ asset('public/'.$speaker->image) }}"
                                     alt="{{ $locale == 'ar' ? $speaker->name_ar : $speaker->name_en }}" />
                            </div>
                            <div class="speaker-info">
                                <h5 class="speaker-title">
                                    {{ $locale == 'ar' ? $speaker->name_ar : $speaker->name_en }}
                                </h5>
                                  <p class="speaker-position">
                                    {{ $locale == 'ar' ? $speaker->company : $speaker->company }}
                                </p>
                                <p class="speaker-position">
                                    {{ $locale == 'ar' ? $speaker->title_ar : $speaker->title_en }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        
    </div>
</section>

{{-- ✨ سكريبت الكتابة --}}
<script>
    const text = "Luxury FX Community Members";
    const el = document.getElementById("typed-title");
    const overlay = document.getElementById("title-overlay");
    let index = 0;
    let hasStarted = false;

    function typeWriter() {
        if (index < text.length) {
            el.innerHTML += text.charAt(index);
            index++;
            setTimeout(typeWriter, 100);
        } else {
            el.style.borderRight = "none";
            overlay.classList.add('show');
        }
    }

    const observer = new IntersectionObserver(function (entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting && !hasStarted) {
                hasStarted = true;
                typeWriter();
                observer.unobserve(el);
            }
        });
    }, { threshold: 0.5 });

    observer.observe(el);
</script>

@endsection
