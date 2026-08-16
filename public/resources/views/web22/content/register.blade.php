@extends('web.layouts.app')

@section('content')
@php $locale = app()->getLocale(); @endphp

<style>
    .form-section {
        padding: 60px 20px;
        background: #f2f2f2;
    }

    .form-title {
        font-size: 34px;
        font-weight: 800;
        text-align: center;
        margin-bottom: 45px;
        background: linear-gradient(90deg, #000, #E73701);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    .form-wrap {
        position: relative;
        margin-bottom: 28px;
    }

    .form-input,
    .form-select {
        width: 100%;
        padding: 18px 20px;
        border: 2px solid transparent;
        border-radius: 14px;
        background: #fff;
        font-size: 16px;
        font-weight: 600;
        color: #000;
        box-shadow: 0 6px 18px rgba(0, 0, 0, 0.06);
        transition: 0.3s ease all;
    }

    .form-input:focus,
    .form-select:focus {
        outline: none;
<<<<<<< HEAD
        border-color: #cc252e;
=======
        border-color: #E73701;
>>>>>>> origin/affaliate
        box-shadow: 0 0 0 3px rgba(231, 55, 1, 0.15);
    }

    .form-label {
        position: absolute;
        top: 13px;
        left: 22px;
        font-size: 14px;
        color: #777;
        font-weight: 600;
        pointer-events: none;
        transition: all 0.2s ease;
    }

    .form-input:focus + .form-label,
    .form-input:not(:placeholder-shown) + .form-label,
    .form-select:focus + .form-label {
        top: -9px;
        left: 15px;
        font-size: 12px;
        background: #f2f2f2;
        padding: 0 5px;
<<<<<<< HEAD
        color: #cc252e;
=======
        color: #E73701;
>>>>>>> origin/affaliate
    }

    .btn-square {
        width: 100%;
        padding: 16px;
<<<<<<< HEAD
        background: linear-gradient(90deg, #cc252e, #000);
=======
        background: linear-gradient(90deg, #E73701, #000);
>>>>>>> origin/affaliate
        border: none;
        border-radius: 50px;
        color: #fff;
        font-size: 17px;
        font-weight: 700;
        cursor: pointer;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        transition: transform 0.2s ease, opacity 0.3s ease;
    }

    .btn-square:hover {
        transform: scale(1.03);
        opacity: 0.95;
    }

    .alert-danger {
        font-size: 14px;
        border-radius: 8px;
        padding: 12px;
        background-color: #ff3b3b;
        color: #fff;
    }

    .phone-flex {
    display: flex;
    gap: 10px;
    align-items: center;
    flex-wrap: wrap;
}

    .phone-flex select {
        flex: 0 0 30%;
        height: 60px;
        border-radius: 14px;
        background: #fff;
        border: 2px solid transparent;
        font-weight: 600;
        box-shadow: 0 6px 18px rgba(0, 0, 0, 0.06);
        text-align: center;
    }

    .phone-flex input {
        flex: 1 1 65%;
        height: 60px;
        border-radius: 14px;
        border: 2px solid transparent;
        box-shadow: 0 6px 18px rgba(0, 0, 0, 0.06);
    }

    @media (max-width: 767px) {
        .phone-flex {
            flex-direction: column;
            align-items: stretch;
        }

        .phone-flex select,
        .phone-flex input {
            width: 100%;
            flex: unset;
        }
    }

</style>

<section class="breadcrumbs-custom bg-image context-dark"
         style="background-image: url({{ asset('public/web/assets/images/bg-breadcrumbs-01-1894x424.jpg') }});">
    <div class="container text-center py-4">
        <h3 class="breadcrumbs-custom-title">Register Now</h3>
    </div>
</section>

<section class="form-section">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10 col-lg-8 col-xl-6">
                <h4 class="form-title">Register Now</h4>

                @if ($errors->any())
                    <div class="alert alert-danger text-start">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('web.register.store') }}" method="post" class="rd-form rd-form-centered">
                    @csrf

                    <div class="form-wrap">
                        <input class="form-input" id="full_name_en" type="text" name="name" placeholder="Full Name" required>
                    </div>

                    <div class="form-wrap">
                        <input class="form-input" id="email" type="email" placeholder="Email" name="email" required>
                    </div>

                    <div class="form-wrap">
                        <label class="form-label"></label>
                        <div class="phone-flex">
                            <select class="form-select" name="country_code" id="country_code" required>
                                <option value="+971">UAE (+971)</option>
                                <option value="+20">Egypt (+20)</option>
                                <option value="+966">Saudi Arabia (+966)</option>
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
                            <input class="form-input" id="phone" type="tel" name="phone" placeholder="Phone Number" required>
                        </div>
                        <small id="phone-error" class="text-danger d-none">Invalid phone number for selected country.</small>
                    </div>

                    <div class="form-wrap">
                        <input class="form-input" id="job_title" type="text" name="job" placeholder="Job Title" required>
                    </div>

                    <input type="hidden" name="type" value="1">

                    <div class="text-center">
                        <button class="btn-square" type="submit">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

<script>
    const phonePatterns = {
        '+20': [/^10/, /^11/, /^12/, /^15/],
        '+966': [/^5/],
        '+971': [/^50/, /^52/, /^54/, /^55/, /^56/],
        '+965': [/^5/, /^6/, /^9/],
        '+964': [/^7/],
        '+963': [/^9/],
        '+962': [/^7/],
        '+968': [/^9/],
        '+973': [/^3/],
        '+974': [/^3/],
        '+212': [/^6/, /^7/],
        '+1': [/^\d{10}$/],
        '+44': [/^7/],
    };

    document.querySelector('form').addEventListener('submit', function (e) {
        const code = document.getElementById("country_code").value;
        const phoneInput = document.getElementById("phone");
        const errorText = document.getElementById("phone-error");

        const rawPhone = phoneInput.value.trim().replace(/\D/g, '');
        const numberWithoutCode = rawPhone.startsWith(code.replace('+', '')) ?
            rawPhone.slice(code.length - 1) : rawPhone;

        const patterns = phonePatterns[code] || [];
        const isValid = patterns.some(p => p.test(numberWithoutCode));

        if (!isValid) {
            e.preventDefault();
            errorText.classList.remove('d-none');
            phoneInput.classList.add('is-invalid');
        } else {
            errorText.classList.add('d-none');
            phoneInput.classList.remove('is-invalid');
        }
    });
</script>
@endsection
