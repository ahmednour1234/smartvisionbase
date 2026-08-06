@extends('web.layouts.app')

@section('content')
@php
    $locale = app()->getLocale();
@endphp

<style>
    /* Breadcrumbs */
    .breadcrumbs-custom {
        background-size: cover;
        background-position: center;
    }

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

    /* Gallery cards */
    .gallery-thumb {
        height: 250px;
        object-fit: cover;
        width: 100%;
        transition: transform 0.3s ease;
        display: block;
    }

    .gallery-thumb:hover {
        transform: scale(1.03);
    }

    .image-wrapper {
        height: 250px;
        background: #f9f9f9;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        border-radius: 8px;
    }

    @media (max-width: 768px) {
        .gallery-thumb,
        .image-wrapper {
            height: 180px;
        }
    }

    /* ========== CUSTOM LIGHTBOX (بديل المودال) ========== */
    .image-lightbox {
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.9);
        display: none;              /* نخليه مخفي في البداية */
        align-items: center;
        justify-content: center;
        z-index: 9999;              /* أعلى من أي حاجة في الصفحة */
    }

    .image-lightbox.active {
        display: flex;
    }

    .image-lightbox img {
        max-width: 95vw;
        max-height: 90vh;
        object-fit: contain;
        box-shadow: 0 0 25px rgba(0, 0, 0, 0.7);
        border-radius: 4px;
    }

    .image-lightbox-close {
        position: absolute;
        top: 15px;
        right: 20px;
        font-size: 32px;
        color: #fff;
        cursor: pointer;
        z-index: 10000;
    }

    .image-lightbox-close:hover {
        color: #f1f1f1;
    }
</style>

<!-- Breadcrumbs -->
<section class="breadcrumbs-custom bg-image context-dark"
         style="background-image: url({{ asset('public/web/assets/images/bg-breadcrumbs-01-1894x424.jpg') }});">
    <div class="container text-start">
        <h3 class="breadcrumbs-custom-title text-white fw-bold">
            {{ $multi_media_category->{'name_' . $locale} ?? '' }}
        </h3>
    </div>
</section>

<!-- Multimedia Section -->
<section class="section section-lg bg-light">
    <div class="container">

        {{-- Videos --}}
        <div class="mb-5">
            <h3 class="text-primary text-center mb-4">{{ __('Videos') }}</h3>
            <div class="row justify-content-center g-4">
                @foreach($multimedias as $media)
                    @php
                        $videos = is_array($media->links)
                            ? $media->links
                            : json_decode($media->links ?? '[]', true);
                    @endphp

                    @foreach($videos as $link)
                        @php
                            $videoId = null;

                            // دعم ?v= و youtu.be
                            if (preg_match('/v=([^\&]+)/', $link, $matches)) {
                                $videoId = $matches[1];
                            } elseif (preg_match('#youtu\.be/([^?]+)#', $link, $matches)) {
                                $videoId = $matches[1];
                            }

                            $embedUrl = $videoId ? 'https://www.youtube.com/embed/' . $videoId : null;
                        @endphp

                        @if($embedUrl)
                            <div class="col-md-8 col-lg-6">
                                <div class="ratio ratio-16x9 shadow-sm rounded overflow-hidden">
                                    <iframe
                                        src="{{ $embedUrl }}"
                                        title="Video"
                                        allowfullscreen
                                        frameborder="0">
                                    </iframe>
                                </div>
                            </div>
                        @endif
                    @endforeach
                @endforeach
            </div>
        </div>

        {{-- Images --}}
        <div>
            <h3 class="text-primary text-center mb-4">{{ __('Images') }}</h3>
            <div class="row g-4 justify-content-center">
                @foreach($multimedias as $media)
                    @php
                        $images = is_array($media->images)
                            ? $media->images
                            : json_decode($media->images ?? '[]', true);
                    @endphp

                    @foreach($images as $image)
                        @php
                            $imageUrl = asset('public/' . ltrim($image, '/'));
                        @endphp

                        <div class="col-6 col-md-4 col-lg-3 pt-2">
                            <a href="javascript:void(0);"
                               class="image-popup d-block"
                               data-image="{{ $imageUrl }}">
                                <div class="image-wrapper">
                                    <img src="{{ $imageUrl }}" class="gallery-thumb" alt="image">
                                </div>
                            </a>
                        </div>
                    @endforeach
                @endforeach
            </div>
        </div>

    </div>
</section>

<!-- ========== CUSTOM LIGHTBOX OVERLAY ========== -->
<div class="image-lightbox" id="imageLightbox">
    <span class="image-lightbox-close" id="lightboxClose">&times;</span>
    <img src="" alt="popup image" id="lightboxImage">
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const lightbox      = document.getElementById('imageLightbox');
        const lightboxImage = document.getElementById('lightboxImage');
        const lightboxClose = document.getElementById('lightboxClose');

        if (!lightbox || !lightboxImage || !lightboxClose) return;

        // فتح الـ popup عند الضغط على الصورة
        document.querySelectorAll('.image-popup').forEach(function (trigger) {
            trigger.addEventListener('click', function (e) {
                e.preventDefault();

                const imageUrl = this.getAttribute('data-image');
                if (!imageUrl) return;

                lightboxImage.src = imageUrl;
                lightbox.classList.add('active');
            });
        });

        // إغلاق بالـ X
        lightboxClose.addEventListener('click', function () {
            lightbox.classList.remove('active');
            lightboxImage.src = '';
        });

        // إغلاق عند الضغط على الخلفية السوداء
        lightbox.addEventListener('click', function (e) {
            if (e.target === lightbox) {
                lightbox.classList.remove('active');
                lightboxImage.src = '';
            }
        });

        // إغلاق بـ ESC من الكيبورد
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && lightbox.classList.contains('active')) {
                lightbox.classList.remove('active');
                lightboxImage.src = '';
            }
        });
    });
</script>
@endsection
