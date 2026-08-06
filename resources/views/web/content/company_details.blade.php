@extends('web.layouts.app')

@section('content')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@700&display=swap');

    .page-header__inner {
      text-align: center;
      padding: 95px 0 60px;
    }

    #typed-title {
        display: inline-block;
        overflow: hidden;
        white-space: nowrap;
        border-right: 3px solid #FFE986;
        font-family: 'Montserrat', sans-serif;
        font-size: 60px;
        background: linear-gradient(90deg, #FFE986 0%, #C48127 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        animation: blink-caret 0.75s step-end infinite;
    }

    #typed-title.finished {
        border-right: none;
        animation: none;
    }

    @keyframes blink-caret {
        0%, 100% { border-color: transparent; }
        50% { border-color: #FFE986; }
    }

    @media (max-width: 767.98px) {
        #typed-title {
            font-size: 32px;
        }
    }

    /* ✅ Popup Styles */
    .vote-popup-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.8);
        z-index: 9999;
        display: none;
        justify-content: center;
        align-items: center;
    }

    .vote-popup-content {
        background-color: #111;
        padding: 40px 50px;
        border-radius: 20px;
        text-align: center;
        box-shadow: 0 10px 30px rgba(255, 255, 255, 0.1);
        position: relative;
        max-width: 500px;
        width: 90%;
    }

    .vote-popup-content h4 {
        background: linear-gradient(90deg, #FFE986 0%, #C48127 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        font-size: 22px;
        font-weight: bold;
        margin-bottom: 20px;
    }

    .vote-popup-content .close-btn {
        position: absolute;
        top: 10px;
        right: 15px;
        background: transparent;
        border: none;
        color: white;
        font-size: 24px;
        cursor: pointer;
    }

    .vote-popup-content .back-btn {
        background: linear-gradient(90deg, #FFE986, #C48127);
        color: white;
        padding: 10px 25px;
        font-weight: bold;
        font-size: 16px;
        border-radius: 8px;
        border: none;
        margin-top: 20px;
        display: inline-block;
        text-decoration: none;
        transition: background 0.3s ease;
    }

    .vote-popup-content .back-btn:hover {
        background: linear-gradient(90deg, #C48127, #FFE986);
        color: #000;
    }

    #voteBtn {
      margin-top: 20px;
      padding: 5px 35px !important;
    }
</style>

<section class="page-header">
    <div class="container">
        <div class="page-header__inner">
            <h2 id="typed-title"></h2>
        </div>
    </div>
</section>

{{-- ✅ Popup --}}
<div class="vote-popup-overlay" id="votePopup">
    <div class="vote-popup-content">
        <button class="close-btn" onclick="document.getElementById('votePopup').style.display='none'">&times;</button>
        <h4 id="voteMessage"></h4>
        <a href="{{ route('web.home') }}" class="back-btn">{{ __('Back to Home') }}</a>
    </div>
</div>

<section class="team-details">
    <div class="container">
        <div class="row">
            <div class="col-xl-4 col-lg-5">
                <div class="team-details__left" style="background: linear-gradient(90deg, #3c2a1e 0%, #1a120b 70%); border-radius: 16px; padding: 30px; text-align: center; color: white;">
                  <div style="margin: 0 auto; width: 160px; height: 160px; border-radius: 50%; overflow: hidden; border: 3px solid #FFE986; box-shadow: 0 4px 12px rgba(0,0,0,0.4);">
                        <img src="{{ asset('public/'.$company->image??'') }}" alt="{{ $company->name_en }}" style="width: 100%; height: 100%; object-fit: contain;" />
                    </div>

                    <div class="mt-4">
                        <h3>{{ $company->name_en }}</h3>
                       @php
  // تحديد الـ category المعروضة وتقييم النجوم حسب القواعد
  $displayCategory = $company->category ?? null;

  if (isset($company->orders) && (int)$company->orders > 100) {
      $rating = 3;
      $displayCategory = 'Average';
  } else {
      $forceFive = isset($company->category) && strcasecmp(trim($company->category), 'Top 100 Member') === 0;
      $rating    = $forceFive ? 5 : (float)($company->stars ?? 0);
  }
@endphp

@if($displayCategory)
  <p style="color: #ccc;">{{ $displayCategory }}</p>
@endif

<div class="mb-2">
  @for ($i = 1; $i <= 5; $i++)
    @php
      $class = $rating >= $i
        ? 'fas fa-star'
        : ($rating >= ($i - 0.5) ? 'fas fa-star-half-alt' : 'far fa-star');
    @endphp
    <i class="{{ $class }} text-warning"></i>
  @endfor
</div>


                        @if ($company->link)
                            <a href="{{ $company->link }}" target="_blank" style="color: #FFE986; font-weight: bold; text-decoration: underline;">
                                <i class="fas fa-globe"></i> {{ __('Visit Website') }}
                            </a>
                        @endif
                    </div>
                       <button id="voteBtn"
                            class="btn"
                            style="background: linear-gradient(90deg, #FFE986 0%, #C48127 100%); color: white; font-size: 22px; padding: 10px 45px; border: none;"
                            onclick="submitVote({{ $company->id }})">
                            {{ __('Vote') }}
                        </button>
                </div>

            </div>

            <div class="col-xl-8 col-lg-7">
                <div class="team-details__right">
                    @if($company->title_en)
                        <h3>{{ $company->title_en }}</h3>
                    @endif

                    @if($company->description_en)
                        <p>{{ $company->description_en }}</p>
                    @endif

                    @if($company->country)
                        <div class="mt-3">
                            <h5 class="fw-bold">{{ __('Country') }}</h5>
                            <p>{{ $company->country }}</p>
                        </div>
                    @endif

                    @if($company->regulation)
                        <div class="mt-4 d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="fw-bold">{{ __('Regulation') }}</h5>
                                <p>{{ $company->regulation }}</p>
                            </div>

                            @endif
                        </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const text = @json(__('Voting'));
        const target = document.getElementById("typed-title");
        let index = 0;

        function typeWriter() {
            if (index < text.length) {
                target.innerHTML += text.charAt(index);
                index++;
                setTimeout(typeWriter, 100);
            } else {
                target.classList.add('finished');
            }
        }

        typeWriter();
    });

    function submitVote(companyId) {
        const voteBtn = document.getElementById('voteBtn');
        voteBtn.disabled = true;
        voteBtn.innerText = 'Voting...';

        fetch(`{{ url('/vote') }}/${companyId}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(async res => {
            const data = await res.json();
            document.getElementById('voteMessage').innerText = data.message;
            document.getElementById('votePopup').style.display = 'flex';

            if (res.ok) {
                voteBtn.innerText = 'Voted';
            } else {
                voteBtn.innerText = 'Vote';
                voteBtn.disabled = false;
            }
        })
        .catch(() => {
            document.getElementById('voteMessage').innerText = 'Something went wrong!';
            document.getElementById('votePopup').style.display = 'flex';
            voteBtn.innerText = 'Vote';
            voteBtn.disabled = false;
        });
    }
</script>
@endsection
