@if ($section && $section->media_type === 'image')
<section class="section bg-default mt-5 pt-5">
    <div class="section-lg context-dark text-center" style="background-color: #F4F3F2;">
        <div class="container">
            <h6 class="text-center" style="color:#E73701; margin-bottom: 8px;">
                {{ $section->title[$locale] ?? '' }}
            </h6>
            <h3 class="text-center" style="color:#111; margin-bottom: 22px;">
                {{ $section->description[$locale] ?? '' }}
            </h3>

            <div class="position-relative gallery-slider-unique" style="padding: 18px 0;">
                <div class="swiper uniqueGallerySwiper">
                    <div class="swiper-wrapper">
                        @foreach ($gallieries as $gallery)
                            <div class="swiper-slide">
                                <a href="{{ asset($gallery->image) }}" data-lightgallery="item" class="slide-link">
                                    <img src="{{ asset($gallery->image) }}" alt="gallery" class="slide-img" loading="lazy">
                                </a>
                            </div>
                        @endforeach
                    </div>

                    <!-- أزرار التنقل -->
                    <div class="gallery-prev custom-swiper-btn">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                          <path d="M15 18l-6-6 6-6" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>
                    <div class="gallery-next custom-swiper-btn">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                          <path d="M9 6l6 6-6 6" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- زر عرض المزيد -->
            <div class="text-center" style="margin-top: 14px;">
                <a class="button button-secondary box-with-triangle-right wow fadeScale"
                   href="{{ route('web.gallery') }}" data-triangle=".button-overlay" style="padding: 10px 30px;">
                    <span>More Gallery</span>
                    <span class="button-overlay"></span>
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Start Stats modification. -->

@php
$stats = $stats ?? [
  ['label' => __('Attendance'), 'value' => 30000, 'suffix' => '+'],
  ['label' => __('Speakers'), 'value' => 250, 'suffix' => '+'],
  ['label' => __('Sponsors'), 'value' => 300, 'suffix' => '+'],
  ];
  @endphp

  <section class="modern-stats-section" aria-label="Statistics">
    <div class="container modern-stats-inner">
      @foreach($stats as $stat)
      <div class="stat-card">
        <div class="stat-icon" aria-hidden="true" role="img" aria-label="check icon">
          <svg width="40" height="40" viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <circle cx="12" cy="12" r="10" stroke="#E73701" stroke-width="2" />
            <path d="M6 15L10 9L14 13L18 7" stroke="#E73701" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
          </svg>
        </div>

        <h3 class="stat-number" data-target="{{ $stat['value'] }}">0</h3>
        @if(!empty($stat['suffix']))
        <span class="stat-suffix">{{ $stat['suffix'] }}</span>
        @endif
        <p class="stat-label">{{ $stat['label'] }}</p>
      </div>
      @endforeach
    </div>
  </section>

  <style>
    /* الخلفية الأساسية */
  .modern-stats-section {
    background: #e5e5e5;
    padding: 80px 0;
    color: #fff;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  }

  .modern-stats-inner {
    display: flex;
    justify-content: center;
    gap: 40px;
    flex-wrap: wrap;
    max-width: 1080px;
    margin: 0 auto;
    padding: 0 15px;
  }

  .stat-card {
    background: linear-gradient(145deg, #ad7373, #676767);
    border: 2px solid #E73701;
    border-radius: 20px;
    width: 280px;
    min-height: 170px;
    padding: 30px 25px;
    text-align: center;
    box-shadow: 0 8px 25px rgba(231, 55, 1, 0.3);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
  }

  .stat-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 15px 45px rgba(231, 55, 1, 0.6);
  }

  .stat-icon {
    margin-bottom: 12px;
    transition: transform 0.3s ease;
  }

  .stat-card:hover .stat-icon {
    transform: scale(1.2);
  }

  .stat-number {
    font-size: 54px;
    font-weight: 900;
    color: #000;
    margin: 0;
    line-height: 1;
  }

  .stat-suffix {
    font-size: 28px;
    font-weight: 700;
    color: #FF552E;
    margin-left: 6px;
    vertical-align: super;
  }

  .stat-label {
    margin-top: 12px;
    font-size: 18px;
    font-weight: 600;
    color: #ccc;
    text-transform: uppercase;
    letter-spacing: 1.5px;
  }

  /* responsive */
  @media (max-width: 992px) {
    .stat-card {
      width: 240px;
      padding: 25px 20px;
    }
    .stat-number {
      font-size: 46px;
    }
    .stat-suffix {
      font-size: 24px;
    }
  }

  @media (max-width: 576px) {
    .modern-stats-inner {
      gap: 20px;
    }
    .stat-card {
      width: 100%;
      max-width: 360px;
      padding: 20px 18px;
      border-radius: 16px;
    }
    .stat-number {
      font-size: 40px;
    }
    .stat-suffix {
      font-size: 20px;
    }
    .stat-label {
      font-size: 16px;
    }
  }
  </style>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    const statCards = document.querySelectorAll('.stat-card');
    const options = { root: null, rootMargin: '0px', threshold: 0.35 };

    const animateCounter = (el) => {
      const counter = el.querySelector('.stat-number');
      const target = +counter.getAttribute('data-target');
      const duration = 1500; // ms
      const start = performance.now();
      const startValue = 0;

      const step = (now) => {
        const progress = Math.min((now - start) / duration, 1);
        // easing (easeOutCubic)
        const eased = 1 - Math.pow(1 - progress, 3);
        const value = Math.floor(startValue + (target - startValue) * eased);
        counter.textContent = value.toLocaleString();
        if (progress < 1) {
          requestAnimationFrame(step);
        } else {
          counter.textContent = target.toLocaleString();
        }
      };

      requestAnimationFrame(step);
    };

    const observer = new IntersectionObserver((entries, obs) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          const card = entry.target;
          if (!card.classList.contains('animated')) {
            card.classList.add('animated');
            animateCounter(card);
            obs.unobserve(card);
          }
        }
      });
    }, options);

    statCards.forEach(card => observer.observe(card));
  });
</script>


<!-- Start Stats modification. -->

<!-- Swiper CSS & JS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.css" />
<script src="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
  new Swiper('.gallery-slider-unique .uniqueGallerySwiper', {
    loop: true,
    spaceBetween: 24,
    speed: 800,
    grabCursor: true,
    centeredSlides: true,
    autoplay: {
            delay: 3000,
            disableOnInteraction: false
          },
          slidesPerView: 1, // صورة واحدة في كل الشاشات
        navigation: {
            nextEl: '.gallery-slider-unique .gallery-next',
            prevEl: '.gallery-slider-unique .gallery-prev',
        }
    });
});
</script>

<style>
.gallery-slider-unique .swiper-slide {
  height: 500px; /* ارتفاع موحد */
  border-radius: 18px;
  overflow: hidden;
  display: flex;
  align-items: center;
  justify-content: center;
  background: #000; /* خلفية لملء الفراغات */
}

.gallery-slider-unique .slide-link {
  width: 100%;
  height: 100%;
  display: flex;
}

.gallery-slider-unique .slide-img {
  width: 100%;
  height: 100%;
  object-fit: contain; /* الصورة كاملة بدون قص أو تمدد */
  border-radius: 18px;
  background: #000;
}

/* أزرار التنقل */
.gallery-slider-unique .custom-swiper-btn {
  position: absolute;
  top: 50%;
  transform: translateY(-50%);
  width: 48px;
  height: 48px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  background: rgba(231,55,1,0.95);
  z-index: 20;
  cursor: pointer;
  transition: transform .2s ease, background .3s ease;
}
.gallery-slider-unique .custom-swiper-btn:hover {
  transform: translateY(-50%) scale(1.1);
  background: rgba(200,30,0,1);
}
.gallery-slider-unique .gallery-prev { left: 8px; }
.gallery-slider-unique .gallery-next { right: 8px; }

@media (max-width: 768px) {
  .gallery-slider-unique .swiper-slide { height: 320px; }
}
</style>
@endif
