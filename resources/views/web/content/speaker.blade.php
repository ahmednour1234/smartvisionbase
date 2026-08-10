@extends('web.layouts.app')

@section('content')
    @php $locale = app()->getLocale(); @endphp

    <!-- Breadcrumb -->
    <section class="breadcrumbs-custom bg-image context-dark"
        style="background-image: url({{ asset('web/assets/images/bg-breadcrumbs-01-1894x424.jpg') }});">
        <div class="container">
            <ul class="breadcrumbs-custom-path">
                <li><a href="{{ route('web.home') }}">Home</a></li>
                <li class="active">Speakers</li>
            </ul>
            <h3 class="breadcrumbs-custom-title">{{ $speaker_section->title[$locale] ?? '' }}</h3>
        </div>
    </section>

    <!-- Speakers Section -->
    <section class="section section-lg bg-default text-center">
        <div class="container">
            <h4 class="font-weight-bold mb-5">{{ $speaker_section->title[$locale] ?? '' }}</h4>

            <div class="row row-30">
                @foreach ($speakers as $speaker)
                    <div class="col-md-6 col-lg-4">
                        <div class="speaker">
                            <div class="speaker-img" data-triangle=".speaker-overlay">
                                <div class="speaker-overlay"></div>
                                <a href="#">
                                    <img src="{{ asset($speaker->image) }}"
                                        alt="{{ $locale == 'ar' ? $speaker->name_ar : $speaker->name_en }}"
                                        style="width: 100%; height: 350px; object-fit: cover;" />
                                </a>
                                <ul class="speaker-social-list mt-2">
                                    @foreach(['facebook-f', 'twitter', 'youtube-play', 'linkedin'] as $icon)
                                        @if($speaker->linkedin)
                                            <li><a class="icon icon-xs fa-{{ $icon }}" href="{{ $speaker->linkedin }}" target="_blank"></a></li>
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
