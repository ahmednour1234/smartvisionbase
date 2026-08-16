@extends('web.layouts.app')

@section('title', 'Email Verification')

@section('content')
<section class="breadcrumbs-custom bg-image context-dark"
         style="background-image: url({{ asset('public/web/assets/images/bg-breadcrumbs-01-1894x424.jpg') }});">
  <div class="container">
    <h3 class="breadcrumbs-custom-title">Verify</h3>
  </div>
</section>

<style>
  :root{
    --ink:#0f172a; --muted:#6b7280; --line:#e5e7eb; --ring:rgba(231,55,1,.25);
    --grad: linear-gradient(to right, #000000, #E73701);
  }
  .verify-section{padding:64px 0}
  .verify-wrap{max-width:640px;margin:0 auto;text-align:center}
  .otp-title{font-weight:800;margin:0 0 .25rem;color:var(--ink);letter-spacing:.4px}
  .otp-sub{color:var(--muted);margin-bottom:1.75rem}

  .otp-grid{display:grid;grid-template-columns:repeat(6,1fr);gap:12px;justify-items:center;margin-bottom:22px}
  .otp-input{
    width:64px;height:72px;border-radius:14px;border:1.5px solid var(--line);
    font-weight:800;font-size:28px;text-align:center;line-height:72px;transition:.2s;background:#fff;
  }
  .otp-input:focus{outline:none;border-color:#E73701;box-shadow:0 0 0 6px var(--ring)}
  @media (max-width:480px){ .otp-input{width:52px;height:60px;font-size:22px;border-radius:12px} }

  /* Buttons row: equal width */
  .btn-row{
    display:grid;grid-template-columns:repeat(2,1fr);gap:14px;max-width:520px;margin:0 auto;
  }
  @media (max-width:480px){ .btn-row{grid-template-columns:1fr} }

  .btn-pill{
    border-radius:9999px; padding:.9rem 1.4rem; font-weight:700; width:100%;
    transition:transform .12s ease, box-shadow .12s ease, filter .12s ease;
  }
  .btn-pill:active{transform:translateY(1px)}

  .btn-grad{
    background:var(--grad); color:#fff; border:0;
    box-shadow:0 10px 24px -14px rgba(231,55,1,.65);
  }
  .btn-grad:hover{filter:brightness(1.06)}
  .btn-grad:disabled{opacity:.6; filter:grayscale(15%); cursor:not-allowed}

  .hints{color:var(--muted);font-size:.95rem;margin-top:18px}
</style>

<section class="verify-section">
  <div class="container">
    <div class="verify-wrap">

      @if(session('success'))
        <div class="alert alert-success text-center">{{ session('success') }}</div>
      @endif
      @if($errors->any())
        <div class="alert alert-danger text-start mx-auto" style="max-width:560px">
          <ul class="mb-0">
            @foreach($errors->all() as $err)
              <li>{{ $err }}</li>
            @endforeach
          </ul>
        </div>
      @endif

      <h2 class="otp-title">Verify your email</h2>
      <p class="otp-sub">Enter the 6-digit code we sent to your inbox.</p>

      <form method="POST" action="{{ route('web.register.verify') }}" id="otpForm" autocomplete="off">
        @csrf
        {{-- Hidden email (prefilled) --}}
        <input type="hidden" name="email" value="{{ $email ?? '' }}">
        {{-- Hidden concatenated code --}}
        <input type="hidden" name="code" id="code">

        <div class="otp-grid">
          @for($i=1;$i<=6;$i++)
            <input
              type="text" inputmode="numeric" pattern="\d*" maxlength="1"
              class="otp-input" data-index="{{ $i }}" aria-label="Digit {{ $i }}" required>
          @endfor
        </div>

        <div class="btn-row">
          <button type="submit" id="verifyBtn" class="btn btn-grad btn-pill" disabled>Verify</button>
          <button type="button" id="resendBtn" class="btn btn-grad btn-pill">Resend code</button>
        </div>
      </form>

      {{-- Hidden form for resend --}}
      <form id="resendForm" method="POST" action="{{ route('web.register.resend') }}" class="d-none">
        @csrf
        <input type="hidden" name="email" value="{{ $email ?? '' }}">
      </form>

      <div class="hints">
        Having trouble? Check your spam folder.<br>
        <span id="cooldownText" class="d-inline-block mt-1"></span>
      </div>
    </div>
  </div>
</section>

<script>
(function () {
  const inputs = Array.from(document.querySelectorAll('.otp-input'));
  const codeEl = document.getElementById('code');
  const form = document.getElementById('otpForm');
  const verifyBtn = document.getElementById('verifyBtn');
  const resendBtn = document.getElementById('resendBtn');
  const resendForm = document.getElementById('resendForm');
  const cooldownText = document.getElementById('cooldownText');

  const updateVerifyState = () => {
    const filled = inputs.map(x => x.value).join('');
    codeEl.value = filled;
    verifyBtn.disabled = filled.length !== inputs.length;
  };

  const focusNext = (idx) => { if (idx < inputs.length) inputs[idx].focus(); };
  const focusPrev = (idx) => { if (idx > 0) inputs[idx - 1].focus(); };

  inputs.forEach((input, i) => {
    input.addEventListener('paste', (e) => {
      const paste = (e.clipboardData || window.clipboardData).getData('text').replace(/\D/g, '');
      if (!paste) return;
      e.preventDefault();
      paste.split('').slice(0, inputs.length).forEach((ch, j) => {
        if (inputs[i + j]) inputs[i + j].value = ch;
      });
      updateVerifyState();
      if (codeEl.value.length === inputs.length) form.submit();
    });

    input.addEventListener('input', (e) => {
      const val = e.target.value.replace(/\D/g, '');
      e.target.value = val.slice(-1);
      if (e.target.value && i < inputs.length - 1) focusNext(i + 1);
      updateVerifyState();
    });

    input.addEventListener('keydown', (e) => {
      if (e.key === 'Backspace' && !e.target.value) focusPrev(i);
      if (e.key === 'ArrowLeft') focusPrev(i);
      if (e.key === 'ArrowRight') focusNext(i + 1);
    });
  });

  form.addEventListener('submit', (e) => {
    if (codeEl.value.length !== inputs.length) {
      e.preventDefault();
      alert('Please enter the 6-digit code.');
    } else {
      verifyBtn.disabled = true;
      verifyBtn.textContent = 'Verifying...';
    }
  });

  // Cooldown for resend (30s)
  const startCooldown = (sec = 30) => {
    resendBtn.disabled = true;
    resendBtn.textContent = 'Resending...';
    let t = sec;
    const tick = () => {
      cooldownText.textContent = t > 0 ? `You can resend in ${t}s` : '';
      if (t === 0) {
        resendBtn.disabled = false;
        resendBtn.textContent = 'Resend code';
        return;
      }
      t--; setTimeout(tick, 1000);
    };
    tick();
  };

  if (inputs[0]) inputs[0].focus();
  updateVerifyState();

  resendBtn.addEventListener('click', () => {
    resendForm.submit();
    startCooldown(30);
  });
})();
</script>
@endsection
