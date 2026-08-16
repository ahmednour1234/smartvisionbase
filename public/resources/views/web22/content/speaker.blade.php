@extends('web.layouts.app')

@section('content')
    @php $locale = app()->getLocale(); @endphp
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
    <!-- Breadcrumb -->
    <section class="breadcrumbs-custom bg-image context-dark"
        style="background-image: url({{ asset('public/web/assets/images/bg-breadcrumbs-01-1894x424.jpg') }});">
        <div class="container">
         
            <h3 class="breadcrumbs-custom-title">{{ $speaker_section->title[$locale] ?? '' }}</h3>
        </div>
    </section>

    <!-- Speakers Section -->
    <section class="section section-lg bg-default text-center">
        <div class="container">
            <h4 class="font-weight-bold mb-5">{{ $speaker_section->title[$locale] ?? '' }}</h4>

            <div class="row row-30">
                @foreach ($speakers as $speaker)
                    <div class="col-md-6 col-lg-4" id="speaker-{{ $speaker->id }}">
                        <div class="speaker">
                            <div class="speaker-img" data-triangle=".speaker-overlay">
                                <div class="speaker-overlay"></div>
                                <a href="#">
                                    <img src="{{ asset('public/'.$speaker->image) }}"
                                        alt="{{ $locale == 'ar' ? $speaker->name_ar : $speaker->name_en }}"
                                        style="width: 100%; height: 350px; object-fit: cover;" />
                                </a>
                               @php
    $socialPlatforms = [
        'facebook' => 'facebook-f',
        'twitter' => 'twitter',
        'linkedin' => 'linkedin',
        'youtube' => 'youtube-play',
        'tiktok' => 'twitter',
        'instagram' => 'instagram',
    ];
@endphp

<ul class="speaker-social-list mt-2">
    @foreach($socialPlatforms as $field => $icon)
        @if(!empty($speaker->$field))
            <li>
                <a class="icon icon-xs fa fa-{{ $icon }}" href="{{ $speaker->$field }}" target="_blank" rel="noopener noreferrer"></a>
            </li>
        @endif
    @endforeach
</ul>

                            </div>
                            <h5 class="speaker-title mt-3">
                                <a href="#">{{ $locale == 'ar' ? $speaker->name_ar : $speaker->name_en }}</a>
                            </h5>
                            <p class="speaker-position">
                                {{ $locale == 'ar' ? $speaker->title_ar : $speaker->title_en }}
                            </p>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Manual Pagination -->
            @if($totalPages > 1)
                <div class="custom-pagination mt-5 d-flex justify-content-center">
                    <ul style="list-style: none; display: flex; gap: 10px; padding: 0;">
                        @for ($i = 1; $i <= $totalPages; $i++)
                            <li>
                                <a href="{{ url()->current() }}?page={{ $i }}"
                                    style="display: inline-block; padding: 10px 15px; border-radius: 6px;
                                    text-decoration: none; border: 1px solid #ddd;
                                    background-color: {{ $i == $page ? '#007bff' : '#f8f9fa' }};
                                    color: {{ $i == $page ? '#fff' : '#333' }};
                                    font-weight: bold;">
                                    {{ $i }}
                                </a>
                            </li>
                        @endfor
                    </ul>
                </div>
            @endif
        </div>
    </section>
@endsection
