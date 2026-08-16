@extends('web.layouts.app')

@section('content')
@php $locale = app()->getLocale(); @endphp
<style>
.section-title-line {
  display: flex;
  align-items: end;
  justify-content: center;
  gap: 20px;
  flex-wrap: wrap;
}
.section-title-line .line {
  flex: 1;
  height: 2px;
  background: linear-gradient(to right, #000000, #E73701);
  max-width: 150px;
}
.section-title-line h4 {
  margin: 0;
  white-space: nowrap;
  font-weight: bold;
}
.sponsor-ritmo {
  background: #fff;
  border-radius: 12px;
  padding: 20px;
  box-shadow: 0 4px 10px rgba(0,0,0,0.1);
  text-align: center;
  height: 100%;
  position: relative;
  transition: all 0.3s ease;
}
.sponsor-ritmo-header {
  position: relative;
  display: inline-block;
  margin-bottom: 15px;
}
.sponsor-ritmo-overlay {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: linear-gradient(135deg, #000000, #E73701);
  opacity: 0.1;
  z-index: 1;
  border-radius: 50%;
}
.sponsor-ritmo-img {
  position: relative;
  z-index: 2;
}
.sponsor-ritmo-img-default,
.sponsor-ritmo-img-active {
  display: block;
}
.sponsor-ritmo-img img {
  width: 120px;
  height: 120px;
  object-fit: contain;
}
.sponsor-ritmo-title {
  font-size: 18px;
  font-weight: bold;
  color: #333;
}
.sponsor-ritmo-text {
  font-size: 14px;
  color: #666;
}
     @media (max-width: 991.98px) {
        .breadcrumbs-custom {
            height: 350px !important; /* صورة أطول في الأجهزة الصغيرة */
            background-size: cover;
            background-position: center;
        }

        .breadcrumbs-custom-title {
            font-size: 28px;
            padding-top:150px;
        }
    }

    .breadcrumbs-custom {
        background-size: cover;
        background-position: center;
    }
</style>

<section class="breadcrumbs-custom bg-image context-dark" style="background-image: url({{ asset('public/web/assets/images/bg-breadcrumbs-01-1894x424.jpg') }});">
  <div class="container">
    <h3 class="breadcrumbs-custom-title">{{ $sponsor_section->title[$locale] ?? '' }}</h3>
  </div>
</section>

<section class="section section-lg bg-default text-center">
  <div class="container">
    <h4 class="font-weight-bold mb-5">{{ $sponsor_section->title[$locale] ?? '' }}</h4>

    @foreach($categories as $category)
      <div class="section-title-line my-4">
        <div class="line"></div>
        <h4 class="text-primary">
          {{ $locale == 'ar' ? $category->name : $category->name_en }}
        </h4>
        <div class="line"></div>
      </div>

      <div class="row row-30 justify-content-center mb-5">
        @forelse($category->sponsors as $sponsor)
          <div class="col-md-6 col-lg-4 mb-4">
            <div class="sponsor-ritmo">
              <a class="sponsor-ritmo-header" href="#" data-triangle=".sponsor-ritmo-overlay">
                <div class="sponsor-ritmo-overlay"></div>
                <div class="sponsor-ritmo-img">
                  <div class="sponsor-ritmo-img-default">
                    <img src="{{ asset('public/'.$sponsor->image) }}" alt="{{ $sponsor->name }}">
                  </div>
                  <div class="sponsor-ritmo-img-active">
                    <img src="{{ asset('public/'.$sponsor->image) }}" alt="{{ $sponsor->name }}">
                  </div>
                </div>
              </a>
              <div class="sponsor-ritmo-body">
                <h5 class="sponsor-ritmo-title"><span class="big">{{ $sponsor->name_en }}</span></h5>
                <p class="sponsor-ritmo-text">{{ $sponsor->title_en }}</p>
              </div>
            </div>
          </div>
        @empty
          <div class="col-12">
            <p class="text-muted">{{ __('No sponsors available in this category.') }}</p>
          </div>
        @endforelse
      </div>
    @endforeach
  </div>
</section>

<section class="section section-lg bg-default text-center">
  <div class="container">
    <div class="block-lg block-center">
      <h6>Become a sponsor.</h6>
      <h2>Let’s become a part of our conference</h2>
      <a class="button button-lg button-primary button-offset-xl" href="{{ route('web.becomesponsor') }}" data-triangle=".button-overlay">
        <span>Become a sponsor</span><span class="button-overlay"></span>
      </a>
    </div>
  </div>
</section>
@endsection
