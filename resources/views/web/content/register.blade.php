@extends('web.layouts.app')

@section('content')
@php $locale = app()->getLocale(); @endphp

<section class="breadcrumbs-custom bg-image context-dark"
         style="background-image: url({{ asset('web/assets/images/bg-breadcrumbs-01-1894x424.jpg') }});">
    <div class="container">
        <ul class="breadcrumbs-custom-path">
            <li><a href="{{ route('web.home') }}">Home</a></li>
            <li class="active">Register Now</li>
        </ul>
        <h3 class="breadcrumbs-custom-title">Register Now</h3>
    </div>
</section>

<section class="section section-lg bg-default text-center">
    <div class="container">
        <div class="row justify-content-md-center">
            <div class="col-md-10 col-lg-8 col-xl-6">
                <h4 class="mb-4">Register Now</h4>

                @if ($errors->any())
                    <div class="alert alert-danger text-start">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('web.register.store') }}" method="post" class="rd-form  rd-form-centered">
                    @csrf

                    <div class="form-wrap">
                        <input class="form-input" id="full_name_en" type="text" name="name" required>
                        <label class="form-label" for="full_name_en">Full Name</label>
                    </div>

                    <div class="form-wrap">
                        <input class="form-input" id="email" type="email" name="email" required>
                        <label class="form-label" for="email">Email</label>
                    </div>

                    <div class="form-wrap">
                        <div class="row g-2 align-items-center">
                            <div class="col-md-4">
                                <select class="form-select" name="country_code" id="country_code" required>
                                    <option value="">-- Code --</option>
                                    <option value="+20">Egypt (+20)</option>
                                    <option value="+966">Saudi Arabia (+966)</option>
                                    <option value="+971">UAE (+971)</option>
                                    <option value="+965">Kuwait (+965)</option>
                                    <option value="+964">Iraq (+964)</option>
                                    <option value="+963">Syria (+963)</option>
                                    <option value="+962">Jordan (+962)</option>
                                    <option value="+968">Oman (+968)</option>
                                    <option value="+973">Bahrain (+973)</option>
                                    <option value="+974">Qatar (+974)</option>
                                    <option value="+212">Morocco (+212)</option>
                                    <option value="+1">USA (+1)</option>
                                    <option value="+44">UK (+44)</option>
                                </select>
                            </div>

                            <div class="col-md-8">
                                <input class="form-input" id="phone" type="tel" name="phone" placeholder="Phone Number" required>
                            </div>
                        </div>
                    </div>

                    <div class="form-wrap">
                        <input class="form-input" id="job_title" type="text" name="job" required>
                        <label class="form-label" for="job_title">Job Title</label>
                    </div>


                    <!-- نوع التسجيل: 2 يعني راعي -->
                    <input type="hidden" name="type" value="1">

                    <button class="button button-block button-primary mt-3" type="submit">
                        <span>Submit</span>
                        <span class="button-overlay"></span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</section>
@if(session('success'))
    <script>
        $(document).ready(function () {
            toastr.success("{{ session('success') }}");
        });
    </script>
@endif
@endsection
