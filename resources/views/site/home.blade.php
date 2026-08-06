@extends('layouts.site')
@section('title', __('Home'))

@section('content')
    <div class="space-y-16">
        @php
            $defaultHomeSections = ['hero', 'videos', 'upcoming', 'about', 'testimonials', 'cta', 'sponsors', 'faq', 'gallery'];
            $homeSectionsRaw = \App\Models\Setting::getValue('home_sections');
            $configuredSections = collect(
                $homeSectionsRaw
                    ? preg_split('/[\r\n,]+/', $homeSectionsRaw)
                    : $defaultHomeSections
            )
                ->map(fn($section) => trim((string) $section))
                ->filter()
                ->unique()
                ->values();

            $sections = $configuredSections
                ->concat(collect($defaultHomeSections)->diff($configuredSections))
                ->filter(fn($section) => view()->exists('site.home_sections.' . $section))
                ->values();
        @endphp
        @foreach($sections as $sec)
            @includeIf('site.home_sections.' . $sec)
        @endforeach

    </div>
    <script>
        // Animate-in on scroll (from left/right/up)
        (function () {
            const els = document.querySelectorAll('.animate-in');
            const isVisibleOnLoad = (el) => {
                const rect = el.getBoundingClientRect();
                const viewportHeight = window.innerHeight || document.documentElement.clientHeight;
                return rect.top < viewportHeight * 0.9 && rect.bottom > 0;
            };
            const setInitialState = (el) => {
                const dir = el.getAttribute('data-direction') || 'up';
                el.style.opacity = '0';
                if (dir === 'left') {
                    el.style.transform = 'translateX(-24px)';
                } else if (dir === 'right') {
                    el.style.transform = 'translateX(24px)';
                } else {
                    el.style.transform = 'translateY(16px)';
                }
                el.style.transition = 'opacity .6s ease, transform .6s ease';
            };
            const reveal = (target) => {
                target.style.opacity = '1';
                target.style.transform = 'translateX(0) translateY(0)';
            };

            els.forEach((el) => {
                if (isVisibleOnLoad(el)) {
                    el.style.transition = 'opacity .6s ease, transform .6s ease';
                    reveal(el);
                } else {
                    setInitialState(el);
                }
            });
            if ('IntersectionObserver' in window) {
                const obs = new IntersectionObserver((entries) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            reveal(entry.target);
                            obs.unobserve(entry.target);
                        }
                    });
                }, { threshold: 0.2 });
                els.forEach(el => {
                    if (!isVisibleOnLoad(el)) {
                        obs.observe(el);
                    }
                });
            } else {
                els.forEach(reveal);
            }
        })();
        document.addEventListener('DOMContentLoaded', function () {
            const counters = document.querySelectorAll('.counter');
            const animateCounter = (el) => {
                const target = parseInt(el.getAttribute('data-target') || '0', 10);
                const duration = 1200;
                const steps = 60;
                const increment = Math.max(1, Math.ceil(target / steps));
                let current = 0;
                const interval = setInterval(() => {
                    current += increment;
                    if (current >= target) {
                        current = target;
                        clearInterval(interval);
                    }
                    el.textContent = current.toLocaleString();
                }, duration / steps);
            };
            if ('IntersectionObserver' in window) {
                const observer = new IntersectionObserver((entries, obs) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            animateCounter(entry.target);
                            obs.unobserve(entry.target);
                        }
                    });
                }, { threshold: 0.4 });
                counters.forEach(el => observer.observe(el));
            } else {
                counters.forEach(animateCounter);
            }

            // Testimonials slider
            const slider = document.getElementById('testimonials-slider');
            if (slider) {
                const track = slider.querySelector('.slider-track');
                const slides = Array.from(track.children);
                const dots = Array.from(slider.querySelectorAll('.dot'));
                let index = 0;
                const itemsPerView = () => window.matchMedia('(min-width: 768px)').matches ? 3 : 1;
                const update = () => {
                    const perView = itemsPerView();
                    const cardWidth = slider.clientWidth / perView;
                    // ensure left-most index so that center is highlighted on md+
                    let leftIndex = index - (perView === 3 ? 1 : 0);
                    leftIndex = Math.max(0, Math.min(leftIndex, Math.max(0, slides.length - perView)));
                    const offset = -leftIndex * cardWidth;
                    track.style.transform = `translateX(${offset}px)`;
                    // highlight center and dim sides
                    slides.forEach((slide, i) => {
                        slide.querySelector('.tcard')?.classList.remove('is-center');
                    });
                    const centerIdx = Math.max(0, Math.min(index, slides.length - 1));
                    const centerSlide = slides[centerIdx];
                    if (centerSlide) centerSlide.querySelector('.tcard')?.classList.add('is-center');
                    // dots
                    dots.forEach((d, i) => {
                        d.style.backgroundColor = i === centerIdx ? 'rgba(255,255,255,.9)' : 'rgba(255,255,255,.3)';
                    });
                };
                const next = () => {
                    index = (index + 1) % slides.length;
                    update();
                };
                let timer = setInterval(next, 4000);
                window.addEventListener('resize', update);
                update();
                // Pause on hover
                slider.addEventListener('mouseenter', () => clearInterval(timer));
                slider.addEventListener('mouseleave', () => { timer = setInterval(next, 4000); });
                // Touch swipe
                let startX = 0;
                slider.addEventListener('touchstart', (e) => { startX = e.touches[0].clientX; clearInterval(timer); }, { passive: true });
                slider.addEventListener('touchend', (e) => {
                    const dx = e.changedTouches[0].clientX - startX;
                    if (Math.abs(dx) > 30) {
                        if (dx < 0) next(); else { index = (index - 1 + slides.length) % slides.length; update(); }
                    }
                    timer = setInterval(next, 4000);
                });
            }
        });
    </script>
@endsection


