@extends('web.layouts.app')

@section('title', 'Registration Closed')

@section('content')
<style>
  /* خط لطيف مشابه (اختياري) */
  @import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@600;700;800;900&family=Inter:wght@400;600;700&display=swap');

  :root{
    --red:#e10600;
    --black:#111;
    --muted:#555;
    --warning:#ffd54f;
  }

  html, body { height:100%; }
  body { margin:0; }

  .notice-page{
    min-height:100vh;
    display:grid;
    place-items:center;
    padding:0vh 4vw;
    background:#fff;
    position:relative;
    overflow:hidden;
  }
  /* خلفية هندسية خفيفة جداً */
  .notice-page::before{
    content:"";
    position:absolute; inset:0;
    background:
      linear-gradient(25deg, rgba(0,0,0,.04) 2px, transparent 2px) 0 0/110px 110px,
      linear-gradient(-25deg, rgba(0,0,0,.03) 1px, transparent 1px) 0 0/120px 120px;
    pointer-events:none;
  }

  .notice-wrap{
    position:relative;
    z-index:1;
    text-align:center;
    max-width:980px;
    width:100%;
    color:var(--black);
    font-family: 'Montserrat', system-ui, -apple-system, Segoe UI, Roboto, 'Inter', Arial, sans-serif;
  }

  /* شريط "IMPORTANT NOTICE" */
  .notice-pill{
    display:inline-flex; align-items:center; gap:.6rem;
    background:var(--warning);
    color:#000;
    border-radius:10px;
    padding:.55rem .9rem;
    font-weight:800;
    letter-spacing:.5px;
    text-transform:uppercase;
    font-size:clamp(.78rem, 1.7vw, .95rem);
  }
  .notice-pill svg{ width:1.05em; height:1.05em; }

  .headline{
    margin:1.1rem 0 0;
    line-height:.95;
    letter-spacing:.5px;
    text-transform:uppercase;
    font-weight:900;
    font-size:clamp(2.2rem, 7.5vw, 5.2rem);
  }
  .headline .block{ display:block; }

  .subhead{
    margin:.5rem 0 0;
    text-transform:uppercase;
    font-weight:800;
    font-size:clamp(1rem, 3.3vw, 2rem);
    letter-spacing:.6px;
  }

  .cta{
    margin:.4rem 0 0;
    color:var(--red);
    text-transform:uppercase;
    font-weight:900;
    font-size:clamp(1.05rem, 3.5vw, 2.05rem);
    letter-spacing:.7px;
  }

  .copy{
    margin:1.1rem auto 2.3rem;
    max-width:760px;
    color:var(--muted);
    font-family:'Inter', system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif;
    font-size:clamp(.9rem, 1.4vw, 1rem);
  }

  /* الهوية بالأسفل */
  .brand-row{
    display:inline-flex; align-items:center; gap:1rem;
  }
  .dubai-tag{
    writing-mode:vertical-rl;
    text-orientation:mixed;
    font-weight:800;
    letter-spacing:1px;
    background:#000; color:#fff;
    padding:.45rem .25rem;
    border-radius:6px;
    font-size:clamp(.65rem, 1.3vw, .8rem);
  }
  .brand{
    text-transform:uppercase;
    font-weight:900;
    line-height:1.05;
  }
  .brand .af{ color:var(--red); }
  .brand .rest{ color:#000; }
  .brand .sub{
    display:block;
    font-weight:800;
    font-size:clamp(.75rem, 1.8vw, 1rem);
    letter-spacing:.7px;
    color:#000;
  }
</style>
<section class="breadcrumbs-custom bg-image context-dark"
         style="background-image: url({{ asset('public/web/assets/images/bg-breadcrumbs-01-1894x424.jpg') }});">
    <div class="container text-center py-4">
        <h3 class="breadcrumbs-custom-title">CLOSED REGISTRATION</h3>
    </div>
</section>
<section class="notice-page">
  <div class="notice-wrap">

    <!-- IMPORTANT NOTICE -->
    <div class="notice-pill" aria-label="Important Notice">
      <!-- أيقونة تحذير -->
      <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 2c.5 0 .96.26 1.22.69l9.5 16.01c.54.9-.11 2.05-1.16 2.05H2.44c-1.05 0-1.7-1.15-1.16-2.05l9.5-16.01A1.4 1.4 0 0 1 12 2zm0 6.25c-.55 0-1 .45-1 1v5.5c0 .55.45 1 1 1s1-.45 1-1v-5.5c0-.55-.45-1-1-1zm0 10c-.69 0-1.25.56-1.25 1.25S11.31 20.75 12 20.75s1.25-.56 1.25-1.25S12.69 18.25 12 18.25z"/></svg>
      <span>Important Notice</span>
      <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 2c.5 0 .96.26 1.22.69l9.5 16.01c.54.9-.11 2.05-1.16 2.05H2.44c-1.05 0-1.7-1.15-1.16-2.05l9.5-16.01A1.4 1.4 0 0 1 12 2zm0 6.25c-.55 0-1 .45-1 1v5.5c0 .55.45 1 1 1s1-.45 1-1v-5.5c0-.55-.45-1-1-1zm0 10c-.69 0-1.25.56-1.25 1.25S11.31 20.75 12 20.75s1.25-.56 1.25-1.25S12.69 18.25 12 18.25z"/></svg>
    </div>

    <!-- العنوانين -->
    <h1 class="headline">REGISTRATION <span class="block">CLOSED</span></h1>
    <p class="subhead">NO ON-SITE REGISTRATION</p>
    <p class="cta">ENTRY BY INVITATION ONLY</p>

    <!-- نص صغير -->
    <p class="copy">
      We look forward to welcoming our invited guests to the Dubai Affiliate &amp; Influencers Summit.
    </p>

    <!-- الهوية -->
    <div class="brand-row" aria-label="Dubai Affiliate & Influencers Summit">

    </div>

  </div>
</section>
@endsection
