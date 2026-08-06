@extends('web.layouts.app')

@section('content')
@php $locale = app()->getLocale(); @endphp

<section class="breadcrumbs-custom bg-image context-dark"
         style="background-image: url({{ asset('web/assets/images/bg-breadcrumbs-01-1894x424.jpg') }});">
    <div class="container">
        <ul class="breadcrumbs-custom-path">
            <li><a href="{{ route('web.home') }}">{{ __('Home') }}</a></li>
            <li><a href="{{ route('web.blogs') }}">{{ __('Blogs') }}</a></li>
            <li class="active">{{ $blog->{'name_' . $locale} ?? '' }}</li>
        </ul>
        <h3 class="breadcrumbs-custom-title">{{ $blog->{'name_' . $locale} ?? '' }}</h3>
    </div>
</section>

<section class="section section-lg bg-default">
    <div class="container">
        <div class="row row-50">
            <!-- Blog Content -->
            <div class="col-lg-7 col-xl-8">
                <article class="post-single">
                    <div class="post-single-img mb-4">
                        <img src="{{ asset($blog->image) }}" alt="Blog image"
                             width="770" height="472" class="img-fluid rounded">
                    </div>

                    <ul class="post-single-meta list-unstyled d-flex gap-3 mb-3">
                        <li class="post-single-meta-author text-muted">
                            <i class="fa fa-user me-1"></i>
                            {{ $blog->{'name_' . $locale} ?? '' }}
                        </li>
                        <li class="post-single-meta-date text-muted">
                            <i class="fa fa-calendar me-1"></i>
                            <time datetime="{{ $blog->created_at->toDateString() }}">
                                {{ $blog->created_at->format('d M, Y') }}
                            </time>
                        </li>
                           <li class="post-single-meta-date text-muted">
                            <i class="fa fa-globe me-1"></i>
                            <a href="{{ $blog->link ??'#'}}">
                            {{ $blog->{'name_' . $locale} ?? '' }}
</a>
                        </li>
                    </ul>

                    <h3 class="post-single-title text-primary mb-3">
                        {{ $blog->{'title_' . $locale} ?? '' }}
                    </h3>

                    <div class="post-single-text text-start">
                        {!! $blog->{'description_' . $locale} ?? '' !!}
                    </div>
                           <!-- Sponsor Button -->
                    @if($blog->link)
                        <div class="text-center mt-5">
                            <a href="{{ $blog->link }}" target="_blank" class="btn btn-lg btn-primary px-5 shadow">
                                {{ __('Become This Event Sponsor') }}
                            </a>
                        </div>
                    @endif
                </article>
            </div>

            <!-- Sidebar: Latest Blogs -->
            <div class="col-lg-5 col-xl-4">
                <div class="post-aside">
                    <h5 class="post-aside-title mb-3">{{ __('Latest Posts') }}</h5>
                    <div class="post-aside-content">
                        @foreach($latestblogs as $latest)
                            <div class="post-mini mb-3">
                                <div class="unit d-flex">
                                    <div class="unit-left me-3">
                                        <a href="{{ route('web.blog.show', $latest->id) }}">
                                            <img src="{{ asset($latest->image) }}" alt="thumb"
                                                 width="59" height="59" class="rounded">
                                        </a>
                                    </div>
                                    <div class="unit-body">
                                        <p class="post-mini-author small mb-1 text-muted">
                                            {{ $latest->{'name_' . $locale} ?? '' }}
                                        </p>
                                        <p class="post-mini-title mb-0">
                                            <a href="{{ route('web.blog.show', $latest->id) }}">
                                                {{ Str::limit($latest->{'title_' . $locale} ?? '', 50) }}
                                            </a>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>
@endsection
