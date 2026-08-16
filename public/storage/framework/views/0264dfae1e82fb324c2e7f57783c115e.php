

<?php $__env->startSection('content'); ?>
<?php $locale = app()->getLocale(); ?>

<style>
    .form-section {
        padding: 100px 20px;
        background: linear-gradient(135deg, #f8f9fa 0%, #f1f3f5 100%);
    }

    .form-title {
        font-size: 3rem;
        font-weight: 900;
        text-align: center;
        margin-bottom: 4rem;
        background: linear-gradient(90deg, #000 0%, #cc252e 50%, #000 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        position: relative;
        padding-bottom: 1.5rem;
        letter-spacing: -0.5px;
    }

    .form-title::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 50%;
        transform: translateX(-50%);
        width: 150px;
        height: 5px;
        background: linear-gradient(90deg, #cc252e, #000);
        border-radius: 5px;
    }

    /* Cards Container */
    .cards-container {
        display: flex;
        flex-wrap: wrap;
        gap: 40px;
        justify-content: center;
        margin-bottom: 50px;
        max-width: 1400px;
        margin-left: auto;
        margin-right: auto;
        align-items: stretch; /* Ensure cards stretch to same height */
    }

    /* Card Styles */
    .card {
        flex: 1;
        min-width: 350px;
        max-width: 420px;
        background: white;
        border-radius: 25px;
        overflow: hidden;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.15);
        transition: all 0.5s cubic-bezier(0.22, 1, 0.36, 1);
        border: none;
        display: flex;
        flex-direction: column;
        position: relative;
        z-index: 1;
        height: auto; /* Let content determine height */
    }

    .card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 10px;
        z-index: 2;
    }

    .card::after {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: linear-gradient(135deg, rgba(255,255,255,0.9) 0%, rgba(255,255,255,0.7) 100%);
        z-index: -1;
        opacity: 0;
        transition: opacity 0.4s ease;
    }

    .card:hover {
        transform: translateY(-15px) scale(1.03);
        box-shadow: 0 30px 60px -10px rgba(0, 0, 0, 0.2);
    }

    .card:hover::after {
        opacity: 1;
    }

    /* Card Header */
    .card-header {
        padding: 30px;
        display: flex;
        align-items: center;
        background-color: transparent;
        border-bottom: 1px solid rgba(0, 0, 0, 0.08);
    }

    .card-icon {
        width: 60px;
        height: 60px;
        border-radius: 16px;
        margin-right: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 28px;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        transition: all 0.3s ease;
    }

    .card:hover .card-icon {
        transform: scale(1.1);
    }

    .card-title {
        font-size: 1.8rem;
        font-weight: 800;
        margin-bottom: 8px;
        color: #222;
        letter-spacing: -0.5px;
    }

    .card-subtitle {
        font-size: 1.1rem;
        color: #555;
        line-height: 1.5;
        font-weight: 600;
    }

    /* Card Body */
    .card-body {
        padding: 30px;
        flex-grow: 1;
        display: flex;
        flex-direction: column;
    }

    .card-description {
        font-size: 1.1rem;
        color: #666;
        margin-bottom: 30px;
        line-height: 1.7;
        flex-grow: 1;
    }

    .card-select {
        width: 100%;
        padding: 16px 20px;
        border: 2px solid #eee;
        border-radius: 14px;
        font-size: 1.1rem;
        font-weight: 600;
        background-color: white;
        appearance: none;
        background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
        background-repeat: no-repeat;
        background-position: right 20px center;
        background-size: 20px;
        transition: all 0.3s ease;
        cursor: pointer;
        margin-bottom: 25px;
    }

    /* Card-specific select styles */
    .card-green .card-select {
        border-color: #8BC34A;
    }
    .card-green .card-select:focus {
        border-color: #4CAF50;
        box-shadow: 0 0 0 4px rgba(76, 175, 80, 0.2);
    }

    .card-blue .card-select {
        border-color: #03A9F4;
    }
    .card-blue .card-select:focus {
        border-color: #2196F3;
        box-shadow: 0 0 0 4px rgba(33, 150, 243, 0.2);
    }

    .card-orange .card-select {
        border-color: #FF9800;
    }
    .card-orange .card-select:focus {
        border-color: #FF5722;
        box-shadow: 0 0 0 4px rgba(255, 87, 34, 0.2);
    }

    .action-container {
        margin-top: auto; /* Push to bottom */
        padding-top: 20px;
    }

    .register-btn {
        display: block;
        width: 100%;
        padding: 18px 30px;
        color: white;
        font-size: 1.1rem;
        font-weight: 700;
        border-radius: 12px;
        text-align: center;
        text-decoration: none;
        transition: all 0.3s ease;
        border: none;
        cursor: pointer;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        margin-top: 10px;
    }

    .register-btn:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
    }

    /* Card Colors */
    .card-green::before {
        background: linear-gradient(90deg, #4CAF50, #8BC34A);
    }
    .card-green .card-icon {
        background: linear-gradient(135deg, #4CAF50, #8BC34A);
    }
    .card-green .register-btn {
        background: linear-gradient(135deg, #4CAF50, #8BC34A);
    }
    .card-green .register-btn:hover {
        background: linear-gradient(135deg, #8BC34A, #4CAF50);
    }

    .card-blue::before {
        background: linear-gradient(90deg, #2196F3, #03A9F4);
    }
    .card-blue .card-icon {
        background: linear-gradient(135deg, #2196F3, #03A9F4);
    }
    .card-blue .register-btn {
        background: linear-gradient(135deg, #2196F3, #03A9F4);
    }
    .card-blue .register-btn:hover {
        background: linear-gradient(135deg, #03A9F4, #2196F3);
    }

    .card-orange::before {
        background: linear-gradient(90deg, #FF9800, #FF5722);
    }
    .card-orange .card-icon {
        background: linear-gradient(135deg, #FF9800, #FF5722);
    }
    .card-orange .register-btn {
        background: linear-gradient(135deg, #FF9800, #FF5722);
    }
    .card-orange .register-btn:hover {
        background: linear-gradient(135deg, #FF5722, #FF9800);
    }

    /* Animation */
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .card {
        animation: fadeInUp 0.8s ease forwards;
        opacity: 0;
    }

    .card:nth-child(1) {
        animation-delay: 0.1s;
    }
    .card:nth-child(2) {
        animation-delay: 0.2s;
    }
    .card:nth-child(3) {
        animation-delay: 0.3s;
    }

    @media (max-width: 1200px) {
        .cards-container {
            gap: 30px;
        }
        .card {
            min-width: 300px;
        }
    }

    @media (max-width: 992px) {
        .form-title {
            font-size: 2.5rem;
        }
        .card {
            min-width: 280px;
        }
    }

    @media (max-width: 768px) {
        .form-section {
            padding: 80px 20px;
        }
        .form-title {
            font-size: 2.2rem;
        }
        .cards-container {
            gap: 25px;
        }
        .card {
            min-width: 100%;
            max-width: 450px;
        }
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
    padding-top: 30px;
    padding-bottom: 30px;
    padding-left: 36px;
    font-size: 18px;
    line-height: 1;
    color: #fff;
    padding-right: 36px;
}
.select2-container--default .select2-selection--single {
    background-color: #000000;
}
.select2-dropdown {
    position: absolute;
    left: -100000px;
    z-index: 1051;
    width: 100%;
    display: block;
    box-sizing: border-box;
    background-color: #000;
    color: #fff;
}
.select2-container--default .select2-results__option.select2-results__option--highlighted, .select2-container--default .select2-results__option[aria-selected=true] {
    background-color: #cc252e;
    color: #ffffff;
}
.select2-container--default .select2-results__option[aria-disabled=true] {
    background-color: #000;
    color: #fff;
}
.card-select {
  position: relative;
  display: inline-block;
}

.card-select option {
  padding: 10px;
}

/* لما يضاف له الكلاس dropup */
.dropup select {
  transform-origin: bottom;
}
.dropup select {
  transform: translateY(-100%);
}
/* Default (desktop/tablet) */

/* Smaller on mobile */
@media (max-width: 767.98px){
  .form-title {
    font-size: 24px;
    line-height: 1.35;
  }
}

</style>


<section class="breadcrumbs-custom bg-image context-dark"
         style="background-image: url(<?php echo e(asset('public/web/assets/images/bg-breadcrumbs-01-1894x424.jpg')); ?>);">
    <div class="container text-center py-4">
        <h3 class="breadcrumbs-custom-title">Pass Categories</h3>
    </div>
</section>

<section class="form-section">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12 col-lg-12 col-xl-12">
                <h4 class="form-title">Please Select Which Pass Best Describes Your Business Activities</h4>

                <?php if($errors->any()): ?>
                    <div class="alert alert-danger text-start">
                        <ul class="mb-0">
                            <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <li><?php echo e($error); ?></li>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <!-- Three Cards Section -->
                <div class="cards-container">
                    <!-- Card 1 - Zone 1 -->
                    <div class="card card-green" data-section="Zone 1 – Affiliates, IBs & Influencers" data-section-key="zone1">
                        <div class="card-header">
                            <div class="card-icon">🟢</div>
                            <div>
                                <h3 class="card-title">FREE</h3>
                                <p class="card-subtitle">Zone 1 – Affiliates, IBs & Influencers</p>
                            </div>
                        </div>
                        <div class="card-body">
                            <p class="card-description">
                                For affiliate marketers, introducing brokers, and content creators in the Forex space. Join our exclusive network and grow your business.
                            </p>
                            <div class="action-container">
                                <select class="card-select" name="zone1_category" required>
                                    <option value="" disabled selected>Select your Category</option>
                                    <option value="Forex Affiliate">Forex Affiliate</option>
                                    <option value="Forex IB">Forex IB (Introducing Broker)</option>
                                    <option value="Affiliate Network Representative">Affiliate Network Representative</option>
                                    <option value="Performance Marketer">Performance Marketer</option>
                                    <option value="Referral Partner">Referral Partner</option>
                                    <option value="Social Media Influencer">Social Media Influencer</option>
                                    <option value="Financial YouTuber/Blogger">Financial YouTuber / Blogger</option>
                                    <option value="Trading Coach/Educator">Trading Coach / Educator</option>
                                    <option value="Signal Provider">Signal Provider</option>
                                </select>
                                <button class="register-btn" type="button">Register Now</button>
                            </div>
                        </div>
                    </div>

                    <!-- Card 2 - Zone 2 -->
                    <div class="card card-blue" data-section="Zone 2 – Brokers & Pro Traders" data-section-key="zone2">
                        <div class="card-header">
                            <div class="card-icon">🔵</div>
                            <div>
                                <h3 class="card-title">FREE</h3>
                                <p class="card-subtitle">Zone 2 – Brokers & Pro Traders</p>
                            </div>
                        </div>
                        <div class="card-body">
                            <p class="card-description">
                                For companies, licensed institutions, and active traders in the Forex & financial markets. Connect with industry leaders and expand your reach.
                            </p>
                            <div class="action-container">
                                <select class="card-select" name="zone2_category" required>
                                    <option value="" disabled selected>Select your Category</option>
                                    <option value="Forex Broker">Forex Broker</option>
                                    <option value="Crypto/CFD Broker">Crypto / CFD Broker</option>
                                    <option value="Bank/Financial Institution">Bank / Financial Institution</option>
                                    <option value="Proprietary Trading Firm">Proprietary Trading Firm</option>
                                    <option value="Regulated Exchange">Regulated Exchange</option>
                                    <option value="Professional Forex Trader">Professional Forex Trader</option>
                                    <option value="Retail Forex Trader">Retail Forex Trader</option>
                                    <option value="Market Analyst">Market Analyst</option>
                                </select>
                                <button class="register-btn" type="button">Register Now</button>
                            </div>
                        </div>
                    </div>

                    <!-- Card 3 - Zone 3 -->
                    <div class="card card-orange" data-section="Zone 3 – Fintech, Services & Public Access" data-section-key="zone3">
                        <div class="card-header">
                            <div class="card-icon">🟠</div>
                            <div>
                                <h3 class="card-title">FREE</h3>
                                <p class="card-subtitle">Zone 3 – Fintech, Services & Public Access</p>
                            </div>
                        </div>
                        <div class="card-body">
                            <p class="card-description">
                                For fintech companies, service providers, and general attendees interested in financial innovation. Discover cutting-edge solutions and opportunities.
                            </p>
                            <div class="action-container">
                                <select class="card-select" name="zone3_category" required>
                                    <option value="" disabled selected>Select your Category</option>
                                    <option value="Forex Liquidity Provider">Forex Liquidity Provider</option>
                                    <option value="Fintech Company">Fintech Company</option>
                                    <option value="CRM/Automation Tools Provider">CRM / Automation Tools Provider</option>
                                    <option value="Payment/PSP Solutions">Payment / PSP Solutions</option>
                                    <option value="Trading Platform/Tech Provider">Trading Platform / Tech Provider</option>
                                    <option value="Business Consultant">Business Consultant</option>
                                    <option value="General Visitor">General Visitor (Interested in Forex & Financial Markets)</option>
                                    <option value="University Student/Job Seeker">University Student / Job Seeker</option>
                                    <option value="Media/Press">Media / Press</option>
                                </select>
                                <button class="register-btn" type="button">Register Now</button>
                            </div>
                        </div>
                    </div>
                </div><!-- /.cards-container -->
            </div>
        </div>
    </div>
</section>

<script>
// — اختيار واحد فقط من أي كارد —
// لو المستخدم اختار جديد، بنشيل القديم فوراً (فرونت) + بنستبدله في السيرفر لما يضغط Register.
document.addEventListener('DOMContentLoaded', function () {
    const selects  = document.querySelectorAll('.card-select');
    const buttons  = document.querySelectorAll('.register-btn');

    // اجعل اختيار واحد فقط
    selects.forEach(sel => {
        sel.addEventListener('change', function () {
            // صَفّر كل القوائم الأخرى
            selects.forEach(other => {
                if (other !== sel) other.selectedIndex = 0;
            });
        });
    });

    // الإرسال للسيرفر مع اسم الـ Section
    buttons.forEach(btn => {
        btn.addEventListener('click', function () {
            const card    = this.closest('.card');
            const section = card?.dataset?.section || '';
            const sectionKey = card?.dataset?.sectionKey || '';
            const select  = card.querySelector('.card-select');

            if (!select.value) {
                alert('Please select a category.');
                return;
            }

            fetch("<?php echo e(route('register.selectCategory')); ?>", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
                },
                body: JSON.stringify({
                    category: select.value,
                    section: section,
                    section_key: sectionKey
                })
            })
            .then(res => res.json())
            .then(data => {
                if (data?.success) {
                    window.location.href = "<?php echo e(route('register.nextPage')); ?>";
                } else {
                    alert(data?.message || 'Something went wrong.');
                }
            })
            .catch(() => alert('Network error. Please try again.'));
        });
    });
});

// — dropdown يفتح لفوق/تحت حسب المساحة —
document.addEventListener('DOMContentLoaded', function() {
    const allSelects = document.querySelectorAll('.card-select');
    allSelects.forEach(select => {
        select.addEventListener('mousedown', function () {
            const rect = this.getBoundingClientRect();
            const spaceBelow = window.innerHeight - rect.bottom;
            const spaceAbove = rect.top;

            if (spaceBelow < 200 && spaceAbove > spaceBelow) {
                this.classList.add('dropup');
            } else {
                this.classList.remove('dropup');
            }
        });
    });
});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('web.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/u552947370/domains/affiliatesummitglobal.com/public_html/resources/views/web/content/register.blade.php ENDPATH**/ ?>