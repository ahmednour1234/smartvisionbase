<?php if($section && $section->media_type === 'image'): ?>

<style>
/* Past Events Gallery Styles */
.past-events-section {
  background: #f4f2ee !important;
    padding: 80px 0;
    position: relative;
    overflow: hidden;
}

.past-events-section::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="50" cy="50" r="1" fill="%23ffffff" opacity="0.03"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
    pointer-events: none;
}

.past-events-title {
    color: #cc252e;
    font-size: 48px;
    font-weight: 700;
    margin-bottom: 0;
    text-align: center;
    position: relative;
    z-index: 2;
    text-shadow: 0 2px 10px rgba(0,0,0,0.3);
        background: linear-gradient(270deg, #000000, #cc252e, #000000);
    background-size: 600% 600%;
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    animation: gradientShift 5s ease infinite;
}

.past-events-title::after {
    content: '';
    position: absolute;
    bottom: -10px;
    left: 50%;
    transform: translateX(-50%);
    width: 100px;
    height: 3px;
    background: linear-gradient(90deg, #cc252e, #FF552E);
    border-radius: 2px;
}

.past-events-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 20px;
    margin-top: 60px;
    position: relative;
    z-index: 2;
}

.past-event-item {
    position: relative;
    border-radius: 15px;
    overflow: hidden;
    background: #333;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.past-event-item:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 35px rgba(231, 55, 1, 0.3);
}

.past-event-link {
    display: block;
    position: relative;
    text-decoration: none;
}

.past-event-image-container {
    position: relative;
    width: 100%;
    height: 200px;
    overflow: hidden;
}

.past-event-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.4s ease;
}

.past-event-item:hover .past-event-image {
    transform: scale(1.1);
}

.past-event-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background:linear-gradient(to right, #cc252e, #cc252e);
    opacity: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: opacity 0.3s ease;
}

.past-event-item:hover .past-event-overlay {
    opacity: 0.6;
}

.past-event-zoom-icon {
    transform: scale(0.8);
    transition: transform 0.3s ease;
}

.past-event-item:hover .past-event-zoom-icon {
    transform: scale(1);
}

.past-events-btn {
    background: linear-gradient(135deg, #cc252e, #FF552E);
    color: white;
    padding: 15px 35px;
    border-radius: 50px;
    text-decoration: none;
    font-weight: 600;
    font-size: 16px;
    display: inline-flex;
    align-items: center;
    transition: all 0.3s ease;
    border: none;
    box-shadow: 0 5px 20px rgba(231, 55, 1, 0.3);
}

.past-events-btn:hover {
    background: linear-gradient(135deg, #d63301, #e64a2e);
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(231, 55, 1, 0.4);
    color: white;
    text-decoration: none;
}

/* Stats Section - keeping the existing styles */
.modern-stats-section {
    background: #FFF;
    padding: 60px 0;
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
    background: linear-gradient(145deg, #FFFFFF 0%, #FFFFFF 50%, #cc252e 100%);
    border: 2px solid #ffffff;
    border-radius: 20px;
    width: 280px;
    min-height: 170px;
    padding: 30px 25px;
    text-align: center;
    box-shadow: 0 8px 25px rgb(0 0 0 / 30%);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
}

.stat-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 15px 45px #cc252e;
}

.stat-icon {
    margin-bottom: 12px;
    transition: transform 0.3s ease;
}

.stat-card:hover .stat-icon {
    transform: scale(1.2);
}

.stat-number {
    font-size: 60px;
    font-weight: 900;
    line-height: 1.2;
    margin-bottom: 10px;
    background: linear-gradient(270deg, #000000, #cc252e, #000000);
    background-size: 600% 600%;
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    animation: gradientShift 5s ease infinite;
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
    color: #000;
    text-transform: uppercase;
    letter-spacing: 1.5px;
}

/* Responsive Design */
@media (max-width: 1200px) {
    .past-events-grid {
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    }
}

@media (max-width: 992px) {
    .past-events-title {
        font-size: 36px;
    }

    .past-events-grid {
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 15px;
    }

    .past-event-image-container {
        height: 180px;
    }

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

@media (max-width: 768px) {
    .past-events-section {
        padding: 60px 0;
    }

    .past-events-title {
        font-size: 28px;
    }

    .past-events-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 12px;
        margin-top: 40px;
    }

    .past-event-image-container {
        height: 150px;
    }
}

@media (max-width: 576px) {
    .past-events-grid {
        grid-template-columns: 1fr;
        gap: 15px;
    }

    .past-event-image-container {
        height: 200px;
    }

    .past-events-btn {
        padding: 12px 25px;
        font-size: 14px;
    }

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
@media (max-width: 576px) {
    .modern-stats-inner {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 15px;
        justify-items: center;
    }

    /* الصف الأول: بوكس واحد في المنتصف */
    .modern-stats-inner .stat-card:nth-child(1) {
        grid-column: span 2;
        justify-self: center;
    }

    /* المربعات بطول وعرض متساوي */
    .stat-card {
        aspect-ratio: 1 / 1;
        width: 100%;
        max-width: 160px; /* حجم أصغر للموبايل */
        padding: 12px 10px; /* تقليل المسافات الداخلية */
    }

    /* الأيقونة أصغر وتبقى جوا البوكس */
    .stat-icon svg {
        width: 28px;
        height: 28px;
    }
    .stat-icon {
        margin-bottom: 6px; /* تقليل المسافة تحت الأيقونة */
    }

    /* الرقم */
    .stat-number {
        font-size: 28px;
        margin-bottom: 4px;
    }

    /* اللاحقة مثل + */
    .stat-suffix {
        font-size: 16px;
        margin-left: 4px;
    }

    /* العنوان */
    .stat-label {
        font-size: 12px;
        margin-top: 4px;
        letter-spacing: 0.5px;
    }
}


</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Stats animation (keeping the existing functionality)
    const statCards = document.querySelectorAll('.stat-card');
    const options = { root: null, rootMargin: '0px', threshold: 0.35 };

    const animateCounter = (el) => {
        const counter = el.querySelector('.stat-number');
        const target = +counter.getAttribute('data-target');
        const duration = 1500;
        const start = performance.now();
        const startValue = 0;

        const step = (now) => {
            const progress = Math.min((now - start) / duration, 1);
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

    // Past events gallery hover effects
    const pastEventItems = document.querySelectorAll('.past-event-item');
    pastEventItems.forEach((item, index) => {
        item.addEventListener('mouseenter', function() {
            this.style.zIndex = '10';
        });

        item.addEventListener('mouseleave', function() {
            this.style.zIndex = '1';
        });
    });
});
</script>
<?php endif; ?>
<?php /**PATH /home/u552947370/domains/affiliatesummitglobal.com/public_html/resources/views/web/content/sections/gallery.blade.php ENDPATH**/ ?>