@extends('web.layouts.app')

@section('content')
@php
    $locale = app()->getLocale();
@endphp
<style>
    @import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@700&display=swap');

      .page-header__inner {
      text-align: center;
      padding: 95px 0 60px;
    }

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
        border-radius: 12px;
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .buy-ticket__img img {
        width: 100%;
        height: 100%;
        object-fit: contain;
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
    }
</style>

<!-- Breadcrumbs -->

<!-- Page Header -->
<section class="page-header">
    <div class="container">
        <div class="page-header__inner text-center typewriter">
            <h2 id="typed-title"></h2>
        </div>
    </div>
</section>
<!-- Multi Media Categories -->
<section class="section section-lg  text-center pb-5">
    <div class="container">
        <div class="row row-30 justify-content-center gap-3">

            @foreach($multi_media_categories as $category)
                <div class="col-md-6 col-lg-4">
                    <div class="card multimedia-card shadow-sm rounded-4 overflow-hidden border border-2 border-primary-subtle h-100">
                        <!-- Image Frame -->
                        <div class="image-container-short border-bottom">
                            <img src="{{ asset('public/'.$category->logo) }}"
                                 alt="logo"
                                 class="img-fluid w-100 h-100 object-fit-cover">
                        </div>

                        <!-- Content -->
                        <div class="card-body px-4 py-3 text-start">
                          <a href="{{ route('web.multi_media.show',[$category->id]) }}">
                           <h5 class="fw-bold text-center mb-2"
    style="background: linear-gradient(90deg, #FFE986 0%, #C48127 100%);
           -webkit-background-clip: text;
           -webkit-text-fill-color: transparent;
           background-clip: text;
           text-fill-color: transparent;">
    {{ $category->{'name_' . $locale} ?? '' }}
</h5>

                          </a>
                            <p class="text-muted small text-wrap mb-0">
                                {{ $category->{'description_' . $locale} ?? '' }}
                            </p>
                        </div>
                    </div>
                </div>
            @endforeach

            @if($multi_media_categories->isEmpty())
                <div class="col-12">
                    <p class="text-muted">{{ __('No multimedia categories available.') }}</p>
                </div>
            @endif

        </div>
    </div>
</section>
<link href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        AOS.init({ duration: 1000, once: true });

        const text = @json(__('Multi Media'));
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
<!-- Styles -->
<style>
    .image-container-short {
        height: 180px;
        overflow: hidden;
        background-color: #f8f9fa;
    }

    .image-container-short img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .multimedia-card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        background-color: #fff;
    }

    .multimedia-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 12px 24px rgba(0, 0, 0, 0.1);
        border-color: #0d6efd;
    }
</style>
@endsection
