@extends('web.layouts.app')

@section('content')
@php $locale = app()->getLocale(); @endphp

<style>
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

    .news {
        background: #fff;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.05);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        height: 100%;
        display: flex;
        flex-direction: column;
        animation: fadeInUp 0.5s ease both;
    }

    .news:hover {
        transform: translateY(-6px);
        box-shadow: 0 12px 24px rgba(0, 0, 0, 0.1);
    }

    .news-img img {
        width: 100%;
        height: 220px;
        object-fit: cover;
        transition: transform 0.3s ease;
    }

    .news-img:hover img {
        transform: scale(1.03);
    }

    .news-time {
        color: #888;
        font-size: 14px;
        margin-top: 12px;
    }

    .news-title {
        font-size: 18px;
        font-weight: bold;
        margin: 12px 0 8px;
    }

    .news-title a {
        color: #222;
        text-decoration: none;
        transition: color 0.3s ease;
    }

    .news-title a:hover {
        color: #cc252e;
    }

    .news-text {
        color: #555;
        font-size: 15px;
        flex-grow: 1;
    }

    .news-link {
        color: #cc252e;
        font-weight: 500;
        text-decoration: none;
        margin-top: 10px;
        position: relative;
        display: inline-block;
        transition: color 0.3s ease;
    }

    .news-link::after {
        content: '';
        position: absolute;
        bottom: -2px;
        left: 0;
        width: 0;
        height: 2px;
        background: #cc252e;
        transition: width 0.3s ease;
    }

    .news-link:hover::after {
        width: 100%;
    }

    /* Pagination Styling */
    .pagination-classic {
        display: flex;
        justify-content: center;
        gap: 8px;
        margin-top: 40px;
    }

    .pagination-classic-link {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background-color: #f1f1f1;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #333;
        font-weight: bold;
        text-decoration: none;
        transition: background-color 0.3s ease;
    }

    .pagination-classic-link:hover {
        background-color: #cc252e;
        color: #fff;
    }

    .pagination-classic-link.active {
        background-color: #cc252e;
        color: #fff;
    }

    .pagination-classic-link.disabled {
        opacity: 0.5;
        pointer-events: none;
    }

    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
</style>

<section class="breadcrumbs-custom bg-image context-dark"
         style="background-image: url({{ asset('public/web/assets/images/bg-breadcrumbs-01-1894x424.jpg') }});">
    <div class="container">
        <h3 class="breadcrumbs-custom-title">Our Blogs</h3>
    </div>
</section>

<section class="section section-lg bg-default text-center">
    <div class="container">
        <div class="row row-30 row-lg-50">
            @foreach ($blogs as $blog)
                <div class="col-sm-12 col-md-6 col-lg-4 d-flex">
                    <div class="news w-100 p-3">
                        <div class="news-img">
                            <a href="{{ route('web.blog.show',[$blog->id]) }}">
                                <img src="{{ asset('public/'.$blog->image) }}" alt="blog image">
                            </a>
                        </div>

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
            {{-- Previous --}}
            <li class="pagination-classic-item">
                <a class="pagination-classic-link pagination-classic-link-prev {{ $page <= 1 ? 'disabled' : '' }}"
                   href="{{ $page > 1 ? '?page=' . ($page - 1) : '#' }}">
                    <span class="fa fa-angle-left"></span>
                </a>
            </li>

            {{-- Pages --}}
            @for($i = 1; $i <= $totalPages; $i++)
                <li class="pagination-classic-item">
                    <a class="pagination-classic-link {{ $page == $i ? 'active' : '' }}"
                       href="?page={{ $i }}">
                        {{ $i }}
                    </a>
                </li>
            @endfor

            {{-- Next --}}
            <li class="pagination-classic-item">
                <a class="pagination-classic-link pagination-classic-link-next {{ $page >= $totalPages ? 'disabled' : '' }}"
                   href="{{ $page < $totalPages ? '?page=' . ($page + 1) : '#' }}">
                    <span class="fa fa-angle-right"></span>
                </a>
            </li>
        </ul>
    @endif
</section>
@endsection
