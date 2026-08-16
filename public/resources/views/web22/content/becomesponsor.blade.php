@extends('web.layouts.app')

@section('content')
<style>
    .form-section {
        padding: 60px 0;
        background: linear-gradient(to bottom right, #f7f7f7, #eaeaea);
    }

    .form-title {
        font-size: 34px;
        font-weight: 900;
        text-align: center;
        margin-bottom: 40px;
        background: linear-gradient(90deg, #E73701, #000000);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    .form-wrap {
        margin-bottom: 20px;
        position: relative;
    }

    .form-input,
    .form-select {
        width: 100%;
        height: 65px;
        padding: 20px;
        font-size: 16px;
        font-weight: 600;
        border: none;
        border-radius: 18px;
        background: rgba(255, 255, 255, 0.6);
        color: #111;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
        backdrop-filter: blur(6px);
        transition: 0.3s ease all;
    }

    .form-input::placeholder {
        color: #999;
        font-weight: 500;
    }

    .form-input:focus,
    .form-select:focus {
        outline: none;
        border: 2px solid #E73701;
        background-color: #fff;
    }

    .btn-square {
        width: 100%;
        padding: 18px;
        font-size: 18px;
        font-weight: 800;
        color: white;
<<<<<<< HEAD
        background: linear-gradient(to right, #000000, #cc252e);
=======
        background: linear-gradient(to right, #000000, #E73701);
>>>>>>> origin/affaliate
        border: none;
        border-radius: 50px;
        transition: all 0.3s ease-in-out;
    }

    .btn-square:hover {
        transform: scale(1.03);
        opacity: 0.95;
    }

    .alert-danger {
        background-color: #ff3333;
        padding: 12px;
        font-size: 14px;
        color: white;
        border-radius: 10px;
    }

    .phone-flex {
        display: flex;
        gap: 12px;
        align-items: center;
        flex-wrap: nowrap;
    }

    .phone-flex select {
        flex: 0 0 35%;
        text-align: center;
        height: 65px;
        border-radius: 18px;
        background: rgba(255, 255, 255, 0.6);
        color: #000;
        border: none;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
        font-weight: 600;
    }

    .phone-flex input {
        flex: 1;
        height: 65px;
        border-radius: 18px;
    }

    @media (max-width: 767px) {
        .form-title {
            font-size: 26px;
        }

        .phone-flex {
            flex-direction: column;
        }

        .phone-flex select,
        .phone-flex input {
            width: 100%;
        }

        .btn-square {
            font-size: 16px;
            padding: 14px;
        }
    }
</style>


<section class="breadcrumbs-custom bg-image context-dark"
         style="background-image: url({{ asset('public/web/assets/images/bg-breadcrumbs-01-1894x424.jpg') }});">
    <div class="container text-center py-4">
        <h3 class="breadcrumbs-custom-title">Become Sponsor</h3>
    </div>
</section>

<section class="form-section">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10 col-lg-7">
                <h4 class="form-title">Become Sponsor</h4>

                @if ($errors->any())
                    <div class="alert alert-danger text-start">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('web.register.store') }}" method="post">
                    @csrf

                    <div class="form-wrap">
                        <input class="form-input" id="name" type="text" name="name" placeholder="Full Name" required>
                    </div>

                    <div class="form-wrap">
                        <input class="form-input" id="email" type="email" name="email" placeholder="Email" required>
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
                            <input class="form-input" id="phone" type="tel" name="phone" placeholder="Phone" required>
                        </div>
                        <small id="phone-error" class="text-danger d-none">Invalid phone number for selected country.</small>
                    </div>

                    <div class="form-wrap">
                        <input class="form-input" id="job" type="text" name="job" placeholder="Job" required>
                    </div>

                    <div class="form-wrap">
                        <input class="form-input" id="company_name" type="text" name="company_name" placeholder="Company Name" required>
                    </div>

                    <input type="hidden" name="type" value="2">

                    <div class="text-center">
                        <button class="btn-square" type="submit">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>



<script>
    document.addEventListener("DOMContentLoaded", () => {
        document.querySelectorAll('.form-input').forEach(input => {
            const wrap = input.closest('.form-wrap');

            const toggleLabel = () => {
                if (input.value.trim() !== '') {
                    wrap.classList.add('filled');
                } else {
                    wrap.classList.remove('filled');
                }
            };

            input.addEventListener('input', toggleLabel);
            input.addEventListener('focus', () => wrap.classList.add('focused'));
            input.addEventListener('blur', () => wrap.classList.remove('focused'));
            toggleLabel();
        });
    });

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
        const phone = document.getElementById("phone").value.trim().replace(/\D/g, '');
        const errorText = document.getElementById("phone-error");
        const phoneInput = document.getElementById("phone");

        const numberWithoutCode = phone.startsWith(code.replace('+', '')) ?
            phone.slice(code.length - 1) : phone;

        const patterns = phonePatterns[code];
        let isValid = false;

        if (patterns) {
            for (let pattern of patterns) {
                if (pattern.test(numberWithoutCode)) {
                    isValid = true;
                    break;
                }
            }
        }

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
