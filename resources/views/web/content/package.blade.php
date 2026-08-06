@extends('web.layouts.app')

@section('content')
@php $locale = app()->getLocale(); @endphp

<style>
    @import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@700&display=swap');

    .package-description p {
        max-width: 350px;
        word-wrap: break-word;
        line-height: 1.6;
        margin-bottom: 10px;
    }

    .pe-4 {
        padding-right: 7rem !important;
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

    /* Unified Package Cards Styles */
    .packages-row {
        display: flex;
        flex-wrap: wrap;
        gap: 20px;
    }

    .package-col {
        flex: 1;
        min-width: 300px;
        display: flex;
    }

    .ticket-one__single {
        display: flex;
        flex-direction: column;
        height: 100%;
        width: 100%;
        min-height: 500px; /* Set minimum height for consistency */
    }

    .ticket-one__title-box {
        flex-shrink: 0; /* Don't allow this to shrink */
    }

    .ticket-one__price-box {
        flex-shrink: 0; /* Don't allow this to shrink */
    }

    .package-description {
        flex: 1; /* This will take up available space */
        display: flex;
        flex-direction: column;
        justify-content: flex-start;
        min-height: 200px; /* Minimum height for content area */
    }

    .ticket-one__btn-box {
        margin-top: auto; /* Push button to bottom */
        flex-shrink: 0; /* Don't allow this to shrink */
        padding-top: 20px;
    }

    /* Ensure equal button heights */
    .ticket-one__btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 50px;
        width: 100%;
        text-align: center;
    }

    @media (max-width: 767.98px) {
        #typed-title {
            font-size: 32px;
        }

        .package-col {
            min-width: 100%;
            margin-bottom: 20px;
        }

        .packages-row {
            flex-direction: column;
        }
    }

    @media (min-width: 768px) and (max-width: 991.98px) {
        .package-col {
            flex: 0 0 calc(50% - 10px);
            min-width: calc(50% - 10px);
        }
    }

    @media (min-width: 992px) {
        .package-col {
            flex: 0 0 calc(33.333% - 14px);
            min-width: calc(33.333% - 14px);
        }
    }
</style>

<section class="page-header">
    <div class="container">
        <div class="page-header__inner">
            <h2 id="typed-title"></h2>
        </div>
    </div>
</section>

<section class="ticket-one">
    <div class="container">
        <div class="packages-row">
            @foreach ($packages as $index => $package)
                <div class="package-col wow fadeIn{{ $index % 3 == 0 ? 'Left' : ($index % 3 == 1 ? 'Up' : 'Right') }}" data-wow-delay="{{ 100 + ($index * 100) }}ms">
                    <div class="ticket-one__single">
                        <div class="ticket-one__title-box">
                            <div class="ticket-one__title-icon">
                                <i class="fas fa-ticket-alt"></i>
                            </div>
                            <h5 class="ticket-one__title">{{ $package->name_en }}</h5>
                        </div>
                        <div class="ticket-one__price-box">
                            <h3>{{ $package->title_en }}</h3>
                        </div>

                        @if($package->description_en)
                            <ul class="list-unstyled ticket-one__points package-description">
                                @foreach (explode("\n", $package->description_en) as $point)
                                    @if (trim($point))
                                        <li>
                                            <div class="icon">
                                                <i class="fas fa-check"></i>
                                            </div>
                                            <div class="text">
                                                <p>{{ trim($point) }}</p>
                                            </div>
                                        </li>
                                    @endif
                                @endforeach
                            </ul>
                        @endif

                        <div class="ticket-one__btn-box">
                            <a href="{{ route('web.becomesponsor') }}" class="ticket-one__btn thm-btn">
                                Become Sponsor <span class="icon-arrow-right"></span>
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const text = "Choose A Package";
        const el = document.getElementById("typed-title");
        let index = 0;
        let hasStarted = false;

        function typeWriter() {
            if (index < text.length) {
                el.innerHTML += text.charAt(index);
                index++;
                setTimeout(typeWriter, 100);
            } else {
                el.classList.add('finished');
            }
        }

        const observer = new IntersectionObserver(function (entries, observer) {
            entries.forEach(entry => {
                if (entry.isIntersecting && !hasStarted) {
                    hasStarted = true;
                    typeWriter();
                    observer.unobserve(el);
                }
            });
        }, { threshold: 0.5 });

        observer.observe(el);
    });
</script>
@endsection
