@extends('web.layouts.app')

@section('content')
@php $locale = app()->getLocale(); @endphp

<style>
    .breadcrumbs-custom {
        background-size: cover;
        background-position: center;
    }

    @media (max-width: 991.98px) {
        .breadcrumbs-custom {
            height: 350px !important;
        }

        .breadcrumbs-custom-title {
            font-size: 28px;
            padding-top: 150px;
        }
    }

    .pricing-card {
        background: linear-gradient(145deg, #ffffff, #f3f3f3);
        border-radius: 20px;
        box-shadow: 0 -15px 30px rgba(0, 0, 0, 0.08);
        padding: 30px 25px;
        display: flex;
        flex-direction: column;
        gap: 20px;
        transition: all 0.3s ease;
        height: 100%;
        position: relative;
        overflow: hidden;
    }

    .pricing-card .box {
        flex-grow: 1;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    .pricing-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.12);
    }

    .pricing-card .plan-name {
        background: linear-gradient(to right, #cc252e, #000000);
        color: #fff;
        font-size: 20px;
        font-weight: 700;
        text-align: center;
        border-radius: 10px;
        padding: 10px 0;
        margin-bottom: 20px;
    }

    .pricing-features {
        list-style: none;
        padding: 0;
        margin-bottom: 25px;
        text-align: left;
        font-size: 15px;
        color: #333;
    }

    .pricing-features li {
        margin-bottom: 12px;
        position: relative;
        padding-left: 26px;
    }

    .pricing-features li::before {
        content: '✔';
        position: absolute;
        left: 0;
        top: 0;
        color: #cc252e;
        font-weight: bold;
    }

    .package-description {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        text-overflow: ellipsis;
        min-height: 42px;
    }

    .btn-primary-custom {
        background: linear-gradient(to right, #cc252e, #000000);
        color: #fff;
        border: none;
        padding: 14px 30px;
        border-radius: 50px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        transition: 0.3s all ease-in-out;
        text-decoration: none;
        display: block;
        width: 100%;
        text-align: center;
    }

    .btn-primary-custom:hover {
        transform: scale(1.03);
        opacity: 0.95;
        color: #fff;
        background: linear-gradient(to right, #ff3c00, #6b1717);
    }
</style>

<section class="breadcrumbs-custom bg-image context-dark"
         style="background-image: url({{ asset('public/web/assets/images/bg-breadcrumbs-01-1894x424.jpg') }});">
    <div class="container">
        <h3 class="breadcrumbs-custom-title">Our Packages</h3>
    </div>
</section>

<section class="section section-lg bg-default text-center">
    <div class="container">
        <h4 class="font-weight-bold mb-5 gre-title" style="font-size: 40px;font-weight: bolder;">{{ __('Packages') }}</h4>
        <div class="row row-30" id="package-container">
            @foreach ($packages as $key => $package)
                @php
                    $desc = $locale === 'ar' ? $package->description_ar : $package->description_en;
                    $listItems = array_filter(preg_split("/\r\n|\n|\r/", $desc));
                @endphp
                <div class="col-sm-6 col-md-4 mb-4 d-flex">
                    <div class="pricing-card w-100">
                        <div class="plan-name">
                            {{ $locale === 'ar' ? $package->name_ar : $package->name_en }}
                        </div>
                        <div class="box">
                        <ul class="pricing-features">
                            @foreach($listItems as $item)
                                <li class="package-description">{{ $item }}</li>
                            @endforeach
                        </ul>
                        <a class="btn-primary-custom" href="{{ route('web.becomesponsor') }}">
                            {{ __('Become Sponsor') }}
                        </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

<script>
    let page = 2;
    let loading = false;

    window.addEventListener('scroll', function () {
        if (loading) return;

        if ((window.innerHeight + window.scrollY) >= document.body.offsetHeight - 300) {
            loading = true;

            fetch(`?page=${page}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
                .then(response => {
                    if (!response.ok) throw new Error('Network error');
                    return response.json();
                })
                .then(data => {
                    if (data.html.trim() !== '') {
                        document.getElementById('package-container')
                            .insertAdjacentHTML('beforeend', data.html);
                        page++;
                        loading = false;
                    }
                })
                .catch(error => {
                    console.error('Fetch error:', error);
                    loading = false;
                });
        }
    });
</script>
@endsection
