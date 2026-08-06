@extends('web.layouts.app')

@section('content')
@php
    $event    = \App\Models\Event::where('event_date', '>', now())->orderBy('event_date')->first();
    $setting  = \App\Models\Setting::first();
    $whatsappNumber = $setting->phone ?? '';
@endphp

<style>
  :root{
    --grad-start:#000000;      /* للـ Gradient */
    --grad-end:#E73701;
    --card:#f5f5f5;            /* خلفية بيضاء */
    --line:#e5e7eb;            /* رصاصي فاتح */
    --text:#111827;            /* نص غامق بسيط */
    --muted:#6b7280;           /* نص ثانوي */
    --accent:#E73701;
  }


  /* ====== Breadcrumbs ====== */
  .breadcrumbs-custom{
    padding: 64px 0;
    background-size: cover;
    background-position: center;
    position: relative;
    color:#fff;
  }
  .breadcrumbs-custom::after{
    content:"";
    position:absolute; inset:0;
    /* تظليل خفيف للصورة بدون سواد كامل */
    background: linear-gradient(to right, rgba(0,0,0,.25), rgba(231,55,1,.15));
  }
  .breadcrumbs-custom { position:relative; z-index:2; }
  .breadcrumbs-custom-title{
    margin:0; font-weight:800; font-size:42px; letter-spacing:.5px;
  }

  /* ====== Contact One ====== */
  .contact-one{ padding: 120px 0; }
  .contact-one .container{ max-width:1030px; }
  .contact-one__inner{
    background-color: var(--card);
    text-align:center;
    padding: 58px 60px 70px;
    border-radius: 16px;
    border:1px solid #E73701;
  }

  .contact-one__title{
    font-weight:900; font-size:36px; margin:0 0 10px;
    background: linear-gradient(to right, var(--grad-start), var(--grad-end));
    -webkit-background-clip:text; background-clip:text;
    -webkit-text-fill-color: transparent; text-fill-color: transparent;
    letter-spacing:.6px;
  }

  .contact-one__text{
    color: var(--muted);
    line-height: 1.6;
    margin: 11px 0 40px;
  }

  .contact-one__form{ text-align: left; }

  .contact-one__input-box{ margin-bottom: 22px; }
  .contact-one__input-box input[type="text"],
  .contact-one__input-box input[type="email"]{
    height:54px; width:100%;
    background-color: #fff;
    border: 1.5px solid var(--line);
    padding: 0 18px;
    outline: none;
    font-size:16px; color: var(--text);
    border-radius: 10px;
    transition: all .15s ease;
  }
  .contact-one__input-box textarea{
    height:140px; width:100%;
    background-color: #fff;
    border: 1.5px solid var(--line);
    padding: 12px 18px;
    outline: none;
    font-size:16px; color: var(--text);
    border-radius: 10px;
    transition: all .15s ease;
    resize: vertical;
  }
  .contact-one__input-box input:focus,
  .contact-one__input-box textarea:focus{
    border-color: var(--accent);
    box-shadow: 0 0 0 3px rgba(231,55,1,.12);
  }

  /* ====== Button (centered) ====== */
  .contact-one__btn-box{ text-align:center; }
  .thm-btn{
    display:inline-flex; align-items:center; gap:10px;
    padding: 12px 28px;
    border:0; border-radius: 999px;
    font-weight:700; font-size:16px; letter-spacing:.4px;
    color:#fff; cursor:pointer; text-decoration:none;
    background: linear-gradient(to right, var(--grad-start), var(--grad-end));
    transition: transform .12s ease, box-shadow .2s ease, filter .2s ease;
  }
  .thm-btn:hover{ transform: translateY(-1px); filter: brightness(1.05); box-shadow: 0 10px 20px rgba(231,55,1,.2); }
  .thm-btn:active{ transform: translateY(0); }
  .icon-arrow-right{
    display:inline-block; width:0; height:0;
    border-top:6px solid transparent; border-bottom:6px solid transparent; border-left:8px solid #fff;
  }

  /* ====== Grid ====== */
  .row{ --bs-gutter-x:1.5rem; display:flex; flex-wrap:wrap; margin-right: calc(var(--bs-gutter-x)/-2); margin-left: calc(var(--bs-gutter-x)/-2); }
  .row > *{ padding-right: calc(var(--bs-gutter-x)/2); padding-left: calc(var(--bs-gutter-x)/2); }

  /* ====== Contact Two ====== */
  .contact-two{ padding: 0 0 90px; }
  .contact-two__single{
    border: 1px solid #E73701;
    border-radius: 14px;
    text-align: center;
    padding: 30px 24px 38px;
    margin-bottom: 30px;
    background: #f5f5f5;          /* أبيض */
  }
  .contact-two__icon{
    width: 78px; height:78px; margin: 0 auto; border-radius: 50%;
    display:flex; align-items:center; justify-content:center;
    background: #fff;          /* أبيض */
    border:1px solid var(--line);
  }
  .contact-two__icon span{
    font-size: 30px;
    background: linear-gradient(to right, var(--grad-start), var(--grad-end));
    -webkit-background-clip:text; background-clip:text;
    -webkit-text-fill-color: transparent; text-fill-color: transparent;
  }
  .contact-two__title{
    font-size: 22px; font-weight: 800; line-height: 1.3;
    margin: 16px 0 6px; color: var(--text);
  }
  .contact-two__text{
    line-height: 1.6; color:#374151;
  }
  .contact-two__text a{ color:#374151; text-decoration:none; }
  .contact-two__text a:hover{ text-decoration: underline; }

  /* أيقونات افتراضية بسيطة */
  .icon-pin::before{ content:"📍"; }
  .icon-call::before{ content:"📞"; }
  .icon-paper-plan::before{ content:"✉️"; }

  /* Containers */
  .container{ width:100%; padding-right: var(--bs-gutter-x, .75rem); padding-left: var(--bs-gutter-x, .75rem); margin-right:auto; margin-left:auto; }
  @media (min-width:576px){ .container{ max-width:540px; } }
  @media (min-width:768px){ .container{ max-width:720px; } }
  @media (min-width:992px){ .container{ max-width:960px; } }
  @media (min-width:1200px){ .container{ max-width:1030px; } }
      .contact-two .row { align-items: stretch; } /* خلّي الأعمدة تتمد لطول أطول كارد */
  .contact-two .col-xl-4,
  .contact-two .col-lg-4,
  .contact-two .col-md-6,
  .contact-two .col-12 { display:flex; }     /* العمود نفسه يبقى flex */
  .contact-two__single{
    display:flex; flex-direction:column;
    height:100%; width:100%;
    min-height: 220px;                        /* ارتفاع أدنى ثابت علشان أي كارد فاضي */
  }
  /* بدّل الكلاس هنا برضه */
.icon-paper-plane::before{
  content: "📤"; /* Outbox */
  font-size: 26px;
  line-height: 1;
}

</style>

<section class="breadcrumbs-custom bg-image context-dark"
         style="background-image: url({{ asset('public/web/assets/images/bg-breadcrumbs-01-1894x424.jpg') }});">
    <div class="container mt-5 pt-5">

        <h3 class="breadcrumbs-custom-title">Contact</h3>
    </div>
</section>
<section class="contact-one">
  <div class="container">
    <div class="contact-one__inner">
      <h3 class="contact-one__title">write here below?</h3>
   

      {{-- رسائل نجاح/أخطاء اختيارية --}}
      @if (session('success'))
        <div class="alert alert-success" role="alert" style="margin-bottom:18px">{{ session('success') }}</div>
      @endif
      @if ($errors->any())
        <div class="alert alert-danger" role="alert" style="margin-bottom:18px">
          <ul style="margin:0;padding-left:18px;">
            @foreach ($errors->all() as $error)
              <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
      @endif

      <form class="contact-one__form" action="{{ route('web.contact.store') }}" method="POST">
        @csrf
        <div class="row">
          <div class="col-xl-6 col-lg-6">
            <div class="contact-one__input-box">
              <input type="text" name="name" placeholder="Your Name" required>
            </div>
          </div>
          <div class="col-xl-6 col-lg-6">
            <div class="contact-one__input-box">
              <input type="email" name="email" placeholder="Your Email" required>
            </div>
          </div>
          <div class="col-xl-6 col-lg-6">
            <div class="contact-one__input-box">
              <input type="text" name="phone" placeholder="Phone Number" required>
            </div>
          </div>
          <div class="col-xl-6 col-lg-6">
            <div class="contact-one__input-box">
              <input type="text" name="title" placeholder="Title" required>
            </div>
          </div>
          <div class="col-xl-12">
            <div class="contact-one__input-box">
              <textarea name="message" placeholder="Your Message" rows="6"></textarea>
            </div>
            <div class="contact-one__btn-box">
              <button type="submit" class="thm-btn contact-one__btn">
                Submit Now 
              </button>
            </div>
          </div>
        </div>
      </form>

      <div class="result"></div>
    </div>
  </div>
</section>
<section class="contact-two">
  <div class="container">
    <div class="row">
      <!-- Location -->
      <div class="col-xl-4 col-lg-4">
        <div class="contact-two__single">
          <div class="contact-two__icon"><span class="icon-pin"></span></div>
          <h3 class="contact-two__title">Location</h3>
          <p class="contact-two__text">{{ $setting->address ?? '' }}</p>
        </div>
      </div>

      <!-- E-mail -->
      <div class="col-xl-4 col-lg-4">
        <div class="contact-two__single">
    <div class="contact-two__icon"><span class="icon-paper-plane"></span></div>
          <h3 class="contact-two__title">E-mail</h3>
          <p class="contact-two__text">
            @if(!empty($setting->email))
              <a href="mailto:{{ $setting->email }}">{{ $setting->email ?? 'info@affaliatesummit.com' }}</a>
            @endif
          </p>
        </div>
      </div>

      <!-- Contact -->
      <div class="col-xl-4 col-lg-4">
        <div class="contact-two__single">
          <div class="contact-two__icon"><span class="icon-call"></span></div>
          <h3 class="contact-two__title">Contact</h3>
          <p class="contact-two__text">
            @if(!empty($setting->phone))
              <a href="tel:{{ $setting->phone }}">{{ $setting->phone }}</a>
            @endif
          </p>
        </div>
      </div>
    </div>
  </div>
</section>
@endsection
