@extends('web.layouts.app')

@section('content')
    <style>
        .gallery-auto-scroll {
            overflow: hidden;
            width: 100%;
            position: relative;
        }

        .gallery-track {
            display: flex;
            width: max-content;
            animation: scroll-horizontal 60s linear infinite;
            gap: 24px;
            /* المسافة بين الصور */
            padding-block: 10px;
        }

        .thumb-wrapper {
            flex: 0 0 auto;
            width: 300px;
            /* العرض أكبر */
            height: 400px;
            /* الطول أطول */
            border-radius: 12px;
            overflow: hidden;
            position: relative;
        }

        .thumb-wrapper img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
        }

        @keyframes scroll-horizontal {
            0% {
                transform: translateX(0);
            }

            100% {
                transform: translateX(-50%);
            }
        }

        .sponsor-auto-scroll {
            overflow: hidden;
            width: 100%;
            position: relative;
        }

        .sponsor-track {
            display: flex;
            width: max-content;
            animation: sponsor-scroll 50s linear infinite;
            gap: 40px;
            /* المسافة بين الشعارات */
            align-items: center;
            justify-content: center;
        }

        .sponsor-item {
            flex: 0 0 auto;
            width: 160px;
            height: 100px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            padding: 10px;
        }

        .sponsor-item img {
            max-width: 100%;
            max-height: 80px;
            object-fit: contain;
        }

        @keyframes sponsor-scroll {
            0% {
                transform: translateX(0);
            }

            100% {
                transform: translateX(-50%);
            }
        }
        .gallery-wrapper {
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
    scroll-behavior: smooth;
}

.gallery-scroll-container {
    display: flex;
    gap: 20px;
    width: max-content;
}

.gallery-thumb {
    flex: 0 0 auto;
    width: calc(25% - 15px); /* يعرض 4 صور في الصف */
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 2px 6px rgba(0,0,0,0.1);
    transition: transform 0.3s ease-in-out;
}

.gallery-thumb img {
    width: 100%;
    height: auto;
    display: block;
}

.gallery-thumb:hover {
    transform: scale(1.05);
}
.swiper-slide {
    height: 300px; /* أو أي ارتفاع تريده */
    display: flex;
    align-items: center;
    justify-content: center;
}

.swiper-slide img {
    width:400px;
    height: 400px;
    object-fit: cover;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    transition: transform 0.3s ease;
}

.swiper-slide img:hover {
    transform: scale(1.03);
}
.gallery-grid-mobile {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 15px;
  padding-bottom: 30px;
}

.gallery-grid-mobile .gallery-item img {
 width:400px;
    height: 250px;
  border-radius: 8px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}
@media (max-width: 767px) {
  .speaker-card {
    display: none;
  }

  .speaker-card:nth-child(-n+4) {
    display: block;
  }
}
.custom-swiper-btn {
  position: absolute;
  top: 50%;
  transform: translateY(-50%);
  width: 34px;
  height: 34px;
  background-color: rgba(0, 0, 0, 0.6);
  border-radius: 50%;
  z-index: 10;
  color: #fff;
}

.swiper-button-prev.custom-swiper-btn {
  left: -15px; /* ← أو 0px لو حابب يكون ملتصق بالحافة */
}

.swiper-button-next.custom-swiper-btn {
  right: -15px;
}

.swiper-button-next::after,
.swiper-button-prev::after {
  font-size: 16px;
}
.swiper-button-next.custom-swiper-btn {
    right: -35px;
}
.custom-swiper-btn {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    width: 34px;
    height: 34px;
    background-color: rgba(0, 0, 0, 0);
    border-radius: 50%;
    z-index: 10;
    color: #fff;
}
@media (max-width: 768px) {
  .custom-swiper-btn {
    width: 28px;
    height: 28px;
  }

  .swiper-button-next::after,
  .swiper-button-prev::after {
    font-size: 12px;
  }

  .swiper-button-prev.custom-swiper-btn {
    left: 0px;
  }

  .swiper-button-next.custom-swiper-btn {
    right: 0px;
  }
.gallery-grid-mobile .gallery-item img {
    width: 200px;
    height: 200px;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}
}

.btn-sm, .btn-group-sm > .btn {
    padding: 0.25rem 0.5rem;
    font-size: 18px;
    line-height: 1.5;
    border-radius: 0.2rem;
}
  @media (max-width: 576px) {
    .promo-title {
      font-size: 36px !important;
    }
  }

.swiper-button-next, .swiper-container-rtl .swiper-button-prev {
    background-image: url(https://forextraderssummit.com/public/web/assets/images/arrow left-01.svg);
    right: 10px;
    left: auto;
}
.swiper-button-prev, .swiper-container-rtl .swiper-button-next {
    background-image: url(https://forextraderssummit.com/public/web/assets/images/arrow-01.svg);

    left: 10px;
    right: auto;
}

    </style>
@php
    $locale = app()->getLocale(); // "en" أو "ar"
@endphp
    @foreach($home_sections as $section)
    @php
        $type = $section->id;
    @endphp

    @if ($type == 1) {{-- Home Slider --}}
        @include('web.content.sections.slider', ['section' => $section, 'event' => $event])
        @include('web.content.sections.note')
        @elseif ($type == 2) {{-- Promo Section --}}
        @include('web.content.sections.promo', ['section' => $section])
    @elseif ($type == 3) {{-- About --}}
        @include('web.content.sections.about', ['section' => $section, 'event' => $event])
    @elseif ($type == 9) {{-- Speakers --}}
        @include('web.content.sections.voting', ['section' => $section])
@elseif ($type == 6) {{-- Gallery --}}
        @include('web.content.sections.gallery', ['section' => $section, 'gallieries' => $gallieries])
       @elseif ($type == 10) {{-- Gallery --}}
        @include('web.content.sections.promo2', ['section' => $section])

    @endif
@endforeach
<!-- Swiper JS -->
<script src="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.js"></script>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const swiper = new Swiper(".gallerySwiper", {
            slidesPerView: 4,
            spaceBetween: 20,
            loop: true,
            navigation: {
                nextEl: ".button-next",
                prevEl: ".button-prev",
            },
            breakpoints: {
                320: { slidesPerView: 1 },
                576: { slidesPerView: 2 },
                768: { slidesPerView: 3 },
                992: { slidesPerView: 4 }
            }
        });

        // تفعيل lightgallery على الصور
        lightGallery(document.querySelector('.gallerySwiper .swiper-wrapper'), {
            selector: 'a[data-lightgallery="item"]',
            plugins: [lgZoom],
            speed: 500,
        });
    });
</script>
    <!-- Section Newsletter Sign Up-->
@endsection
