@extends('web.layouts.app')

@section('content')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@700&display=swap');

    .prize-row {
        row-gap: 60px;
    }

    .prize-card {
        transition: transform 0.3s ease;
    }

    .prize-card:hover {
        transform: translateY(-5px);
    }

    .buy-ticket__img {
        width: 100%;
        height: 400px;
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .buy-ticket__img img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }

    .buy-ticket__title {
        font-size: 28px;
        font-weight: bold;
        margin-bottom: 15px;
        background: linear-gradient(90deg, #FFE986, #C48127);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        color: transparent;
        font-family: 'Montserrat', sans-serif;
    }

    .buy-ticket__text {
        font-size: 17px;
        line-height: 1.8;
        color: #e2e2e2;
        word-break: break-word;
    }

    /* Typewriter Title */
    .typewriter h2 {
        display: inline-block;
        overflow: hidden;
        white-space: nowrap;
        border-right: 3px solid #FFE986;
        animation: blink-caret 0.75s step-end infinite;
        font-family: 'Montserrat', sans-serif;
        font-size: 50px;
background: linear-gradient(90deg, #FFE986 0%, #C48127 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    }

    .typewriter h2.finished {
        border-right: none;
        animation: none;
    }
          .page-header {
        padding: 200px 0 85px;
    }

    @keyframes blink-caret {
        0%, 100% { border-color: transparent; }
        50% { border-color: #FFE986; }
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
    }
    .buy-ticketw {
    position: relative;
    display: block;
    padding: 85px 0 85px;
    z-index: 1;
}

   
</style>

<!-- Page Header -->
<section class="page-header">
    <div class="container">
        <div class="page-header__inner text-center typewriter">
            <h2 id="typed-title"></h2>
        </div>
    </div>
</section>

@php $locale = app()->getLocale(); @endphp

<!-- Prizes Section -->
<section class="buy-ticketw">
    <div class="container">
        <div class="row prize-row">
            @foreach($prizes as $prize)
                @php $isEven = $loop->iteration % 2 == 0; @endphp

                <div class="row align-items-center mb-5">
                    @if($isEven)
                        <!-- صورة يسار -->
                        <div class="col-lg-6" data-aos="fade-right">
                            <div class="prize-card">
                                <div class="buy-ticket__img">
                                    <img src="{{ asset('public/'.$prize->img) }}" alt="{{ $prize->name_en }}">
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <h3 class="buy-ticket__title" data-aos="fade-left" data-aos-delay="100">
                                {{ $prize->name_en ?? 'Prize Title' }}
                            </h3>
                            <p class="buy-ticket__text" data-aos="fade" data-aos-delay="400">
                                {!! nl2br(e($prize->description_en ?? 'Prize description')) !!}
                            </p>
                        </div>
                    @else
                        <!-- صورة يمين -->
                        <div class="col-lg-6 order-lg-2" data-aos="fade-left">
                            <div class="prize-card">
                                <div class="buy-ticket__img">
                                    <img src="{{ asset('public/'.$prize->img) }}" alt="{{ $prize->name_en }}">
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6 order-lg-1">
                            <h3 class="buy-ticket__title" data-aos="fade-left" data-aos-delay="100">
                                {{ $prize->name_en ?? 'Prize Title' }}
                            </h3>
                            <p class="buy-ticket__text" data-aos="fade" data-aos-delay="400">
                                {!! nl2br(e($prize->description_en ?? 'Prize description')) !!}
                            </p>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
</section>

<!-- Scripts -->
<link href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        AOS.init({ duration: 1000, once: true });

        const text = @json(__('Ceremony Awards'));
        const target = document.getElementById("typed-title");
        let index = 0;

        function typeWriter() {
            if (index < text.length) {
                target.innerHTML += text.charAt(index);
                index++;
                setTimeout(typeWriter, 120);
            } else {
                target.classList.add('finished');
            }
        }

        typeWriter();
    });
</script>
@endsection
