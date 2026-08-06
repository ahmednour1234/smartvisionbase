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
        overflow: hidden;
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
        border: 2px solid rgba(204, 37, 46, 0.4);
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

    /* Loader */
    .infinite-loader {
        text-align: center;
        padding: 20px 0;
        color: #666;
        font-size: 14px;
        display: none;
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
<section class="section section-lg bg-default text-center" style="background: #f5f5f5 ">
    <div class="container">
        <h4 class="font-weight-bold mb-4">{{ $speaker_section->title[$locale] ?? '' }}</h4>
        <h3 class="font-weight-bold gre-title" style="color: #E73701;">Our Speakers</h3>

        @php
            $socialPlatforms = [
                'facebook'  => 'facebook-f',
                'twitter'   => 'twitter',
                'linkedin'  => 'linkedin',
                'youtube'   => 'youtube-play',
                'tiktok'    => 'twitter',
                'instagram' => 'instagram',
            ];
        @endphp

        <div class="row" id="speakers-container">
            {{-- أول دفعة من السبيكرز --}}
            @include('web.content.partials._speaker_cards', ['speakers' => $speakers])
        </div>

        <!-- Loader + رسالة نهاية -->
        <div id="speakers-loader" class="infinite-loader">
            <span class="loader-text">{{ $locale == 'ar' ? 'جاري تحميل المزيد...' : 'Loading more speakers...' }}</span>
        </div>
        <div id="speakers-end" class="infinite-loader" style="display:none;">
            <span class="loader-text">{{ $locale == 'ar' ? 'لا يوجد المزيد من المتحدثين.' : 'No more speakers.' }}</span>
        </div>
    </div>
</section>

{{-- Infinite Scroll Script --}}
<script>
    (function() {
        let nextPage = {{ $speakers->currentPage() < $speakers->lastPage() ? $speakers->currentPage() + 1 : 'null' }};
        let isLoading = false;
        const container = document.getElementById('speakers-container');
        const loader    = document.getElementById('speakers-loader');
        const endMsg    = document.getElementById('speakers-end');
        const baseUrl   = "{{ url()->current() }}";

        function loadMoreSpeakers() {
            if (!nextPage || isLoading) return;

            isLoading = true;
            loader.style.display = 'block';

            const url = baseUrl + '?page=' + nextPage;

            fetch(url, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
                .then(response => response.json())
                .then(data => {
                    loader.style.display = 'none';
                    isLoading = false;

                    if (data.html) {
                        const tempDiv = document.createElement('div');
                        tempDiv.innerHTML = data.html;
                        while (tempDiv.firstChild) {
                            container.appendChild(tempDiv.firstChild);
                        }
                    }

                    if (data.next_page) {
                        nextPage = data.next_page;
                    } else {
                        nextPage = null;
                        endMsg.style.display = 'block';
                    }
                })
                .catch(() => {
                    loader.style.display = 'none';
                    isLoading = false;
                });
        }

        function handleScroll() {
            if (!nextPage) return;

            const scrollPos   = window.innerHeight + window.scrollY;
            const threshold   = document.body.offsetHeight - 300;

            if (scrollPos >= threshold) {
                loadMoreSpeakers();
            }
        }

        window.addEventListener('scroll', handleScroll);
    })();
</script>
@endsection
