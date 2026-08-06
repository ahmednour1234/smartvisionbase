  @extends('web.layouts.app')

@section('content') 
@php
  $event = \App\Models\Event::where('event_date', '>', now())->orderBy('event_date')->first();
    $setting = \App\Models\Setting::first();

  $whatsappNumber = $setting->phone; // رقم الواتساب
@endphp
    <!--Contact One Start-->
        <section class="contact-one">
            <div class="container">
                <div class="contact-one__inner">
<h3 class="contact-one__title" style="
  background: linear-gradient(90deg, #FFE986, #C48127);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  font-weight: bold;
">
  write here below?
</h3>

                 <form class="contact-one__form" action="{{ route('web.contact.store') }}" method="POST">
    @csrf
                        <div class="row">
                            <div class="col-xl-6 col-lg-6">
                                <div class="contact-one__input-box">
                                    <input type="text" name="name" placeholder="Your Name" required="">
                                </div>
                            </div>
                            <div class="col-xl-6 col-lg-6">
                                <div class="contact-one__input-box">
                                    <input type="email" name="email" placeholder="Your Email" required="">
                                </div>
                            </div>
                            <div class="col-xl-6 col-lg-6">
                                <div class="contact-one__input-box">
                                    <input type="text" name="phone" placeholder="Phone Number" required="">
                                </div>
                            </div>
                            <div class="col-xl-6 col-lg-6">
                                <div class="contact-one__input-box">
                                    <input type="text" name="title" placeholder="Title" required="">
                                </div>
                            </div>
                            <div class="col-xl-12">
                                <div class="contact-one__input-box text-message-box">
                                    <textarea name="message" placeholder="Your Message"></textarea>
                                </div>
                                <div class="contact-one__btn-box">
                                    <button type="submit" class="thm-btn contact-one__btn">Submit Now<span
                                            class="icon-arrow-right"></span></button>
                                </div>
                            </div>
                        </div>
                    </form>
                    <div class="result"></div>
                </div>
            </div>
        </section>
        <!--Contact One End-->

        <!--Contact Two Start-->
        <section class="contact-two">
            <div class="container">
                <div class="row">
                    <!--Contact Two Single Start-->
                    <div class="col-xl-4 col-lg-4">
                        <div class="contact-two__single">
                            <div class="contact-two__icon">
                                <span class="icon-pin"></span>
                            </div>
                            <h3 class="contact-two__title">Location</h3>
                            <p class="contact-two__text">{{$setting->address??''}}</p>
                        </div>
                    </div>
                    <!--Contact Two Single End-->
                    <!--Contact Two Single Start-->
                    <div class="col-xl-4 col-lg-4">
                        <div class="contact-two__single">
                            <div class="contact-two__icon">
                                <span class="icon-paper-plan"></span>
                            </div>
                            <h3 class="contact-two__title">E-mail</h3>
                            <p class="contact-two__text"><a
                                    href="mailto:{{$setting->email??''}}">{{$setting->email??''}}</a></p>
      
                        </div>
                    </div>
                    <!--Contact Two Single End-->
                    <!--Contact Two Single Start-->
                    <div class="col-xl-4 col-lg-4">
                        <div class="contact-two__single">
                            <div class="contact-two__icon">
                                <span class="icon-call"></span>
                            </div>
                            <h3 class="contact-two__title">Contact</h3>
                            <p class="contact-two__text"><a href="tel:{{$setting->phone??''}}">{{$setting->phone??''}}</a> 
                        </div>
                    </div>
                    <!--Contact Two Single End-->
                </div>
            </div>
        </section>
        @endsection