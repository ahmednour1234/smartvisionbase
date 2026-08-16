@extends('web.layouts.app')

@section('content')
@php
    $locale = app()->getLocale();
@endphp
<style>
     @media (max-width: 991.98px) {
        .breadcrumbs-custom {
            height: 350px !important; /* صورة أطول في الأجهزة الصغيرة */
            background-size: cover;
            background-position: center;
        }

        .breadcrumbs-custom-title {
            font-size: 28px;
            padding-top:150px;
        }
    }

    .breadcrumbs-custom {
        background-size: cover;
        background-position: center;
    }
</style>
<!-- Breadcrumbs -->
<section class="breadcrumbs-custom bg-image context-dark"
         style="background-image: url({{ asset('public/web/assets/images/bg-breadcrumbs-01-1894x424.jpg') }}); background-size: cover;">
    <div class="container">

        <h3 class="breadcrumbs-custom-title text-white fw-bold">{{ __('Multi Media') }}</h3>
    </div>
</section>

<!-- Multi Media Categories -->
<section class="section multimedia-categories section-lg bg-light text-center">
    <div class="container">
        <div class="row row-30 justify-content-center">

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
                            <h5 class="fw-bold text-primary text-center mb-2">
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

<!-- Styles -->
<style>
    .image-container-short {
        height: 200px;
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
        border-color: #cc252;
    }
    .multimedia-categories {
      min-height: calc(100vh - 424px); /* Adjust based on your header/footer height */
      display: flex;
      align-items: center;
      justify-content: center;
    }
    .multimedia-categories .container {
      width: 100%;
    }
    .multimedia-categories .container .col {
      width: 100%;
    }

</style>
@endsection
