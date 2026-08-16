@if ($section && $section->media_type === 'image')
<section class="section bg-default mt-5 pt-5 parallax-container section">
    <div class="parallax-content section-lg context-dark text-center" style="background-color: #F4F3F2;">
        <div class="container">
<<<<<<< HEAD
            <h6 class="text-center" style="color:#cc252e;">
=======
            <h6 class="text-center" style="color:#E73701;">
>>>>>>> origin/affaliate
                {{ $section->title[$locale] ?? '' }}
            </h6>
            <h3 class="text-center" style="color:black;">
                {{ $section->description[$locale] ?? '' }}
            </h3>

            {{-- Slider لجميع الشاشات --}}
            <div class="position-relative">
                <div class="swiper gallerySwiper pb-5 pt-5">
                    <div class="swiper-wrapper">
                        @foreach ($gallieries as $gallery)
                            <div class="swiper-slide">
                                <a href="{{ asset('public/'.$gallery->image) }}" data-lightgallery="item">
                                    <img src="{{ asset('public/'.$gallery->image) }}" alt="gallery">
                                </a>
                            </div>
                        @endforeach
                    </div>

                    {{-- أزرار التنقل --}}
                    <div class="swiper-button-prev custom-swiper-btn"></div>
                    <div class="swiper-button-next custom-swiper-btn"></div>
                </div>
            </div>

            {{-- زر عرض الكل --}}
            <div class="text-center">
                <a class="button button-secondary box-with-triangle-right wow fadeScale mt-2"
                   href="{{ route('web.gallery') }}" data-triangle=".button-overlay">
                    <span>More Gallery</span>
                    <span class="button-overlay"></span>
                </a>
            </div>
        </div>
    </div>
</section>

{{-- Swiper CSS & JS --}}
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.css" />
<script src="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.js"></script>

{{-- Swiper init --}}
<script>
    const swiper = new Swiper('.gallerySwiper', {
        loop: true,
        spaceBetween: 24,
        effect: 'slide', // ممكن تغيّره لـ "fade" لو تحب
        speed: 800,
        grabCursor: true,
        navigation: {
            nextEl: '.swiper-button-next',
            prevEl: '.swiper-button-prev',
        },
          breakpoints: {
            0: {
              slidesPerView: 1,
              },
          768: {
              slidesPerView: 2,
          },
          1024: {
              slidesPerView: 4,
          },
          },
    });
</script>

{{-- Internal CSS --}}
<style>
    .swiper-slide {
        height: 350px;
        overflow: hidden;
        transition: transform 0.5s ease-in-out;
        border-radius: 10px;
    }

    .swiper-slide img {
        width: 100%;
        height: 100%;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
        transition: transform 0.5s ease, box-shadow 0.3s ease;
    }

    .swiper-slide img:hover {
        transform: scale(1.03);
        box-shadow: 0 12px 28px rgba(0, 0, 0, 0.25);
    }

    .custom-swiper-btn {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        width: 40px;
        height: 40px;
<<<<<<< HEAD
        background: #cc252e;
=======
        background: rgba(231, 55, 1, 0.95);
>>>>>>> origin/affaliate
        color: #fff;
        border-radius: 50%;
        z-index: 10;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: background 0.3s ease;
    }

    .custom-swiper-btn:hover {
<<<<<<< HEAD
        background: #cc252e
=======
        background: rgba(200, 30, 0, 1);
>>>>>>> origin/affaliate
    }

    .swiper-button-prev::after,
    .swiper-button-next::after {
        font-size: 16px;
        font-weight: bold;
    }

    .swiper-button-prev {
        left: 0px;
    }

    .swiper-button-next {
        right: 0px;
    }

    @media (max-width: 768px) {

        .custom-swiper-btn {
            width: 30px;
            height: 30px;
        }

        .swiper-button-prev::after,
        .swiper-button-next::after {
            font-size: 12px;
        }

        .swiper-button-prev {
            left: 0;
        }

        .swiper-button-next {
            right: 0;
        }
    }
</style>
@endif
