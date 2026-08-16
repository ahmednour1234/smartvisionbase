  @if ($section && $section->media_type === 'image')
<section class="section section-lg bg-default text-center mt-5 pb-5">
    <div class="container mt-5 pt-5">
        <h6 class="text-secondary">{{ $section->title[$locale] ?? '' }}</h6>
        <h3 class="mb-5">{{ $section->description[$locale] ?? '' }}</h3>

        <div class="row row-30">
            @php $locale = app()->getLocale(); @endphp

            @foreach ($blogs as $blog)
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card h-100 shadow-sm border-0">
                        <a href="{{ route('web.blog.show', [$blog->id]) }}">
                            <img src="{{ asset('public/'.$blog->image) }}" class="card-img-top rounded-top" alt="Blog Image" style="height: 230px; object-fit: cover;">
                        </a>
                        <div class="card-body text-start">
                            <p class="text-muted mb-2" style="font-size: 14px;">
                                <span>{{ $blog->{'name_' . $locale} ?? '' }}</span> •
                                <time datetime="{{ $blog->created_at->toDateString() }}">
                                    {{ $blog->created_at->format('d M, Y') }}
                                </time>
                            </p>

                            <h5 class="card-title"  style="color:black;">
                                <a href="{{ route('web.blog.show', [$blog->id]) }}" class="text-dark text-decoration-none">
                                    {{ $blog->{'title_' . $locale} ?? '' }}
                                </a>
                            </h5>

                            <p class="card-text"  style="color:black;">
                                {{ Str::limit(strip_tags($blog->{'description_' . $locale} ?? ''), 150) }}
                            </p>
                        </div>
                        <div class="card-footer bg-white border-0 text-start">
                            <a href="{{ route('web.blog.show', [$blog->id]) }}" class="btn btn-sm btn-outline-primary">
                                {{ __('Read More') }}
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif
