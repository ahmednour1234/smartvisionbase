@extends('web.layouts.app')

@section('content')
@php $locale = app()->getLocale(); @endphp

<section class="breadcrumbs-custom bg-image context-dark"
         style="background-image: url({{ asset('web/assets/images/bg-breadcrumbs-01-1894x424.jpg') }});">
    <div class="container">
        <ul class="breadcrumbs-custom-path">
            <li><a href="{{ route('web.home') }}">Home</a></li>
            <li class="active">Blogs</li>
        </ul>
        <h3 class="breadcrumbs-custom-title">Our Blogs</h3>
    </div>
</section>

<section class="section section-lg bg-default text-center">
    <div class="container">
        <div class="row row-30 row-lg-50">
            @foreach ($blogs as $blog)
                <div class="col-md-4 mb-4">
                    <div class="news" style="border: 1px solid #ddd; border-radius: 8px; padding: 15px; height: 100%;">
                        <div class="news-img">
                            <a href="{{ route('web.blog.show',[$blog->id]) }}" data-triangle=".news-img-overlay">
                                <span class="news-img-overlay"></span>
                                <img src="{{ asset($blog->image) }}" alt="blog image" width="370" height="284" />
                            </a>
                        </div>

                        <p class="news-time mt-2">
                            <span>{{ $blog->{'name_' . $locale} ?? '' }}</span> -
                            <time datetime="{{ $blog->created_at->toDateString() }}">
                                {{ $blog->created_at->format('d M, Y') }}
                            </time>
                        </p>

                        <h4 class="news-title">
                            <a href="{{ route('web.blog.show',[$blog->id]) }}">
                                {{ $blog->{'title_' . $locale} ?? '' }}
                            </a>
                        </h4>

                        <p class="news-text">
                            {{ Str::limit(strip_tags($blog->{'description_' . $locale} ?? ''), 100) }}
                        </p>

                        <a class="news-link" href="{{ route('web.blog.show',[$blog->id]) }}">{{ __('Read More') }}</a>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    @if($totalPages > 1)
        <div class="divider divider-gray-100 mt-5"></div>
        <ul class="pagination-classic">
            {{-- السابق --}}
            <li class="pagination-classic-item">
                <a class="pagination-classic-link-prev {{ $page <= 1 ? 'disabled' : '' }}"
                   href="{{ $page > 1 ? '?page=' . ($page - 1) : '#' }}"
                   data-triangle=".pagination-classic-overlay">
                    <div class="pagination-classic-overlay"></div>
                    <span class="fa-angle-left"></span>
                </a>
            </li>

            {{-- أرقام الصفحات --}}
            @for($i = 1; $i <= $totalPages; $i++)
                <li class="pagination-classic-item">
                    <a class="pagination-classic-link {{ $page == $i ? 'active' : '' }}"
                       href="?page={{ $i }}"
                       data-triangle=".pagination-classic-overlay">
                        <div class="pagination-classic-overlay"></div>
                        <span>{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}</span>
                    </a>
                </li>
            @endfor

            {{-- التالي --}}
            <li class="pagination-classic-item">
                <a class="pagination-classic-link-next {{ $page >= $totalPages ? 'disabled' : '' }}"
                   href="{{ $page < $totalPages ? '?page=' . ($page + 1) : '#' }}"
                   data-triangle=".pagination-classic-overlay">
                    <div class="pagination-classic-overlay"></div>
                    <span class="fa-angle-right"></span>
                </a>
            </li>
        </ul>
    @endif
</section>
@endsection
