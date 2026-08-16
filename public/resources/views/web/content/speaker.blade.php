@extends('web.layouts.app')

@section('content')
@php $locale = app()->getLocale(); @endphp

<style>
    /* Breadcrumbs */
    @media (max-width: 991.98px) {
        .breadcrumbs-custom {
            height: 350px !important;
            background-size: cover;
            background-position: center;
        }
        .breadcrumbs-custom-title {
            font-size: 28px;
            padding-top: 150px;
        }
    }
    .breadcrumbs-custom {
        background-size: cover;
        background-position: center;
    }

    /* Speaker Card */
    .speaker {
        position: relative;
        border-radius: 12px;
        overflow: hidden; /* عشان التأثير يبقى داخل الكارد */
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        display: flex;
        flex-direction: column;
        cursor: pointer;
        height: 100%;
        background: #fff;
        transition: box-shadow 0.3s ease, transform 0.3s ease;
        border: 1px solid #e0e0e0;
        z-index: 0;
    }
    .speaker::before {
        content: "";
        position: absolute;
        top: 8px;
        left: 8px;
        right: 8px;
        bottom: 8px;
        border-radius: 10px;
        border: 2px solid rgba(204, 37, 46, 0.4); /* ظل خفيف */
        opacity: 0;
        transition: opacity 0.3s ease;
        pointer-events: none;
        z-index: 1;
    }
    .speaker:hover {
        transform: translateY(-6px);
        box-shadow: 0 20px 40px rgba(204, 37, 46, 0.2);
    }
    .speaker:hover::before {
        opacity: 1;
    }

    /* الجزء العلوي - صورة */
    .speaker-img {
        background: #fff;
        padding: 15px;
        display: flex;
        justify-content: center;
        align-items: center;
        overflow: hidden;
    }
    .speaker-img img {
        width: 100%;
        height: auto;
        object-fit: contain;
        border-radius: 10px;
        transition: transform 0.3s ease;
        display: block;
    }
    .speaker:hover .speaker-img img {
        transform: scale(1.05);
    }

    /* الجزء السفلي - النصوص والخلفية */
    .speaker-info {
        background: #f7f7f7;
        padding: 20px 15px;
        flex-grow: 1;
        display: flex;
        flex-direction: column;
        justify-content: center;
        text-align: center;
    }
    .speaker-title {
        font-weight: 700;
        font-size: 1.5rem;
        color: #222;
        margin-bottom: 8px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .speaker-position {
        color: #666;
        font-size: 1rem;
        margin-bottom: 12px;
        word-wrap: break-word;
    }

    .speaker-social-list {
        list-style: none;
        padding: 0;
        margin: 0 auto;
        display: flex;
        justify-content: center;
        gap: 15px;
        margin-top: auto;
    }
    .speaker-social-list li a {
        color: #cc252e;
        font-size: 18px;
        transition: color 0.3s ease;
    }
    .speaker-social-list li a:hover {
        color: #e84b3a;
    }

    /* Responsive Adjustments */
    @media (max-width: 991.98px) {
        .speaker-img {
            padding: 10px;
        }
        .speaker-info {
            padding: 0;
        }
        .speaker-title {
            font-size: 1.2rem;
            white-space: normal;
        }
        .speaker-position {
            font-size: 0.9rem;
        }
    }
    @media (max-width: 576px) {
        .speaker-img {
            padding: 8px;
        }
        .speaker-title {
            font-size: 1rem;
        }
        .speaker-position {
            font-size: 0.85rem;
        }
    }
</style>

<!-- Breadcrumb -->
<section class="breadcrumbs-custom bg-image context-dark"
    style="background-image: url({{ asset('public/web/assets/images/bg-breadcrumbs-01-1894x424.jpg') }});">
    <div class="container">
        <h3 class="breadcrumbs-custom-title">Our Speakers</h3>
    </div>
</section>

<!-- Speakers Section -->
<section class="section section-lg bg-default text-center" style="background: #f5f5f5 ">
    <div class="container">
        <h4 class="font-weight-bold mb-4">{{ $speaker_section->title[$locale] ?? '' }}</h4>
        <h3 class="font-weight-bold gre-title" style="color: #cc252e;">Our Speakers</h3>

        <div class="row">
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

            @foreach ($speakers as $speaker)
                <div class="col-6 col-md-6 col-lg-4 mb-4" id="speaker-{{ $speaker->id }}">
                    <div class="speaker">
                        <div class="speaker-img">
                            <a href="#">
                                <img src="{{ asset('public/'.$speaker->image) }}"
                                    alt="{{ $locale == 'ar' ? $speaker->name_ar : $speaker->name_en }}" />
                            </a>
                        </div>
                        <div class="speaker-info">
                            <h5 class="speaker-title">
                                <a href="#">{{ $locale == 'ar' ? $speaker->name_ar : $speaker->name_en }}</a>
                            </h5>
                            <p class="speaker-position">
                                {{ $locale == 'ar' ? $speaker->title_ar : $speaker->title_en }}
                            </p>

                            <ul class="speaker-social-list">
                                @foreach ($socialPlatforms as $field => $icon)
                                    @if (!empty($speaker->$field))
                                        <li>
                                            <a class="icon fa fa-{{ $icon }}" href="{{ $speaker->$field }}" target="_blank"
                                                rel="noopener noreferrer"></a>
                                        </li>
                                    @endif
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Manual Pagination -->
        @if($totalPages > 1)
            <div class="custom-pagination mt-5 d-flex justify-content-center">
                <ul class="list-unstyled d-flex gap-2 p-0">
                    @for ($i = 1; $i <= $totalPages; $i++)
                        <li>
                            <a href="{{ url()->current() }}?page={{ $i }}"
                                class="btn btn-sm {{ $i == $page ? 'btn-primary text-white' : 'btn-light text-dark' }}">
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
