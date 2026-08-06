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
    /* Reset and Base Styles */
    .sponsors-section * {
        box-sizing: border-box;
        margin: 0;
        padding: 0;
    }

    /* Main Container */
    .sponsors-container {
        width: 100%;
        min-height: 100vh;
        padding: 60px 20px;
        background: linear-gradient(135deg, #f5f7fa 0%, #e4e8f0 100%);
        position: relative;
        overflow: hidden;
    }

    /* Animated Background Elements */
    .sponsors-container::before {
        content: '';
        position: absolute;
        top: -50%;
        left: -50%;
        width: 200%;
        height: 200%;
        background-image:
            radial-gradient(circle at 20% 30%, rgba(231, 55, 1, 0.05) 0%, transparent 20%),
            radial-gradient(circle at 80% 70%, rgba(231, 55, 1, 0.05) 0%, transparent 20%);
        animation: float 15s ease-in-out infinite;
        pointer-events: none;
    }

    @keyframes float {
        0%, 100% { transform: translate(0, 0); }
        50% { transform: translate(50px, 50px); }
    }

    /* Header Styles */
    .sponsors-header {
        text-align: center;
        margin-bottom: 60px;
        position: relative;
        z-index: 2;
    }

    .sponsors-title {
        font-size: 3rem;
        font-weight: 800;
        color: #E73701;
        margin-bottom: 20px;
        text-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    /* Category Styles */
    .category-container {
        margin-bottom: 60px;
        position: relative;
        z-index: 2;
    }

    .category-title {
        text-align: center;
        margin-bottom: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .category-title h2 {
        font-size: 2rem;
        color: #333;
        padding: 10px 20px;
        background: white;
        position: relative;
        z-index: 2;
        border-radius: 5px;
    }

    .category-title::before,
    .category-title::after {
        content: '';
        flex: 1;
        height: 2px;
        background: linear-gradient(to right, transparent, #E73701, transparent);
    }

    /* Cards Grid */
    .cards-grid {
        display: flex;
        justify-content: center;
        align-items: center;
        flex-wrap: wrap;
        gap: 30px;
    }

    /* Card Wrapper */
    .sponsor-card-wrapper {
      height: fit-content !important;
    }

    /* Card Styles */
    .sponsor-card {
        width: 300px;
        height: 350px;
        position: relative;
        transform-style: preserve-3d;
        transition: all 0.8s ease;
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        cursor: pointer;
        margin: 0;
    }

    /* Card Faces */
    .card-face {
        position: absolute;
        width: 100%;
        height: 100%;
        backface-visibility: hidden;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        padding: 30px;
    }

    .card-front {
        background: white;
        transform: rotateY(0deg);
        transition: filter 0.3s ease;
    }

    /* Redial Circle */
    .redial {
        background:#E73701;
        width: 70px;
        height: 70px;
        border-radius: 50%;
        position: absolute;
        top: 40%;
        right: 20px;
        transition: all 0.3s ease;
        overflow: hidden;

        /* نخلي المحتوى داخلها مخفي */
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        color: white;
        font-weight: 700;
        font-size: 1.2rem;

        /* نخفي المحتوى عشان يظهر عند hover */
        opacity: 0;
        pointer-events: none;
    }

    /* محتوى الـ redial (الاسم واللينك) مخفيين في الأصل */
    .redial h3,
    .redial a {
        display: none;
        color: #FFF;
        font-size: 30px;
        margin: 5px 0;
        text-decoration: none;
    }

    .redial a {
      font-size: 14px;
      background: #FFF;
      padding: 7px;
      color: #000;
      border-radius: 5px
    }

    /* عند الهوفر على الكارد */
    .sponsor-card:hover .redial {
        width: 100%;
        height: 100%;
        top: 0;
        right: 0;
        border-radius: 0;
        opacity: 1;
        pointer-events: auto;
        flex-direction: column;
        justify-content: space-evenly;
        padding: 30px;
    }

    .sponsor-card:hover .redial h3,
    .sponsor-card:hover .redial a {
        display: block;
    }

    /* نخفف تدرج الخلفية للكارد الأساسي لما الكورة تظهر */
    .sponsor-card:hover .card-front {
        filter: brightness(0.8);
    }

    /* Front Card Content */
    .logo-container {
        width: 100%;
        height: 60%;
        display: flex;
        justify-content: center;
        align-items: center;
        margin-bottom: 20px;
    }

    .sponsor-logo {
        max-width: 100%;
        max-height: 100%;
        object-fit: contain;
        filter: grayscale(30%);
        transition: all 0.3s ease;
        margin-top: 110px;
    }

    .sponsor-card:hover .sponsor-logo {
        filter: grayscale(0%);
    }

    .sponsor-info {
        text-align: center;
    }

    .sponsor-name {
        font-size: 1.4rem;
        font-weight: 700;
        color: #333;
        margin-bottom: 5px;
    }

    .sponsor-category {
        font-size: 1rem;
        color: #E73701;
    }
        .sponsors-cta {
        background: linear-gradient(135deg, #000000 0%, #E73701 100%);; /* لون برتقالي جذاب */
        padding: 40px 30px;
        border-radius: 20px;
        margin-top: 60px;
        text-align: center;
        color: #fff;
        box-shadow: 0 10px 30px rgba(231, 55, 1, 0.3);
        max-width: 700px;
        margin-left: auto;
        margin-right: auto;
    }

    .sponsors-cta .cta-title {
        font-size: 2.5rem;
        font-weight: 800;
        margin-bottom: 15px;
        text-shadow: 1px 1px 3px rgba(0,0,0,0.2);
        color: #FFF;
      }

      .sponsors-cta .cta-subtitle {
        font-size: 1.2rem;
        margin-bottom: 30px;
        opacity: 0.9;
        color: #FFF;
    }

    .sponsors-cta .cta-btn {
        display: inline-block;
        background: #fff;
        color: #E73701;
        font-weight: 700;
        padding: 15px 40px;
        border-radius: 50px;
        text-decoration: none;
        font-size: 1.2rem;
        box-shadow: 0 5px 15px rgba(255, 255, 255, 0.6);
        transition: all 0.3s ease;
        cursor: pointer !important;
    }

    .sponsors-cta .cta-btn:hover {
        background: #ff7043;
        color: #fff;
        box-shadow: 0 8px 25px rgba(255, 112, 67, 0.6);
    }

    /* Responsive adjustments */
    @media (max-width: 576px) {
        .sponsors-cta {
            padding: 30px 20px;
            border-radius: 15px;
        }

        .sponsors-cta .cta-title {
            font-size: 1.8rem;
        }

        .sponsors-cta .cta-subtitle {
            font-size: 1rem;
        }

        .sponsors-cta .cta-btn {
            font-size: 1rem;
            padding: 12px 30px;
        }

      .redial h3,
      .redial a {
          font-size: 20px;
          margin: 2px 0;
      }

    .redial a {
      font-size: 10px;
      padding: 7px;
      border-radius: 5px
    }
    }

    /* Responsive Adjustments */
    @media (max-width: 992px) {
        .cards-grid {
            grid-template-columns: repeat(3, 1fr);
            gap: 25px;
        }

        .sponsor-card-wrapper {
            height: 320px;
        }

        .sponsors-title {
            font-size: 2.5rem;
        }
    }

    @media (max-width: 509px) {
      .sponsor-card {
        width: 140px;
        height: 185px;
      }
      .sponsor-category {
        font-size: 11px
      }
      .sponsor-info {
        padding: 10px 0;
      }
      .sponsor-logo {
        margin-top: 40px;
      }
    }
</style>

<section class="breadcrumbs-custom bg-image context-dark" style="background-image: url({{ asset('public/web/assets/images/bg-breadcrumbs-01-1894x424.jpg') }});">
  <div class="container">
    <h3 class="breadcrumbs-custom-title" style="font-size: 2.2rem;">{{ $sponsor_section->title[$locale] ?? '' }}</h3>
  </div>
</section>

<div class="sponsors-container">
    <div class="container">
        <div class="sponsors-header">
            <h1 class="sponsors-title gre-title" style="font-size: 40px;font-weight: bolder;">Our Sponsors</h1>
        </div>

        @foreach($categories as $category)
            <div class="category-container">
                <div class="category-title">
                    <h2>{{ $locale == 'ar' ? $category->name : $category->name_en }}</h2>
                </div>

                @if($category->sponsors->count() > 0)
                    <div class="cards-grid">
                        @foreach($category->sponsors as $sponsor)
                            <div class="sponsor-card-wrapper">
                                <div class="sponsor-card">
                                    <!-- Front Side -->
                                    <div class="card-face card-front">
                                        <div class="logo-container">
                                            <img src="{{ asset('public/'.$sponsor->image) }}" alt="{{ $sponsor->name }}" class="sponsor-logo">
                                        </div>
                                        <div class="sponsor-info">
                                            <h3 class="sponsor-name">{{ $sponsor->name_en }}</h3>
                                            <p class="sponsor-category">{{ $locale == 'ar' ? $category->name : $category->name_en }}</p>
                                        </div>

                                        <div class="redial">
                                            <h3>{{ $sponsor->name_en }}</h3>
                                            <a href="#">View Profile</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div style="text-align: center; padding: 40px 20px; background: white; border-radius: 15px; box-shadow: 0 5px 15px rgba(0,0,0,0.05);">
                        <svg width="60" height="60" viewBox="0 0 24 24" fill="none" stroke="#E73701" stroke-width="1.5" style="opacity: 0.7; margin-bottom: 20px;">
                            <circle cx="12" cy="12" r="10"></circle>
                            <line x1="12" y1="8" x2="12" y2="12"></line>
                            <line x1="12" y1="16" x2="12.01" y2="16"></line>
                        </svg>
                        <h4 style="color: #666; font-weight: 500;">{{ __('No sponsors available in this category') }}</h4>
                    </div>
                @endif
            </div>
        @endforeach

        <div class="sponsors-cta">
            <h3 class="cta-title">{{ __('Become a Sponsor') }}</h3>
            <p class="cta-subtitle">{{ __('Join our prestigious list of partners and get exclusive benefits') }}</p>
            <a href="{{ route('web.becomesponsor') }}" class="cta-btn">{{ __('Get Started') }}</a>
        </div>
    </div>
</div>
@endsection
