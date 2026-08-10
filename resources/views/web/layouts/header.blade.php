@php
    use App\Models\Setting;
    $setting = Setting::first();
@endphp

<style>
    :root {
        --gold: #D4AF37;
        --dark-gold: #B8860B;
        --black: #0A0A0A;
        --dark-gray: #1A1A1A;
        --medium-gray: #2A2A2A;
    }

    /* Header Base Styles */
    .main-header {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        z-index: 1000;
        padding: 15px 40px;
        height: 110px;

    }

    .header-container {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin: 0 auto;
        padding: 0 20px;
        height: 100%;
    }

    .logo img {
        height: 85px;
    }

    /* Navigation */
    .main-nav {
        display: flex;
        align-items: center;
        gap: 25px;
    }

    .nav-links {
        display: flex;
        gap: 20px;
    }

    .nav-links a {
        color: white;
        text-decoration: none;
        font-weight: 500;
        transition: color 0.3s;
        position: relative;
        font-size: 19px;
    }

    .nav-links a:hover {
        color: var(--gold);
    }

    .nav-links a::after {
        content: '';
        position: absolute;
        bottom: -5px;
        left: 0;
        width: 0;
        height: 2px;
        background: var(--gold);
        transition: width 0.3s;
    }

    .nav-links a:hover::after {
        width: 100%;
    }

    .thm-btn {
      text-decoration: none;
    }

    /* Mobile Menu */
    .mobile-menu-btn {
        display: none;
        background: none;
        border: none;
        color: white;
        font-size: 24px;
        cursor: pointer;
    }

    /* Sidebar */
    .sidebar {
        position: fixed;
        bottom: 0;
        left: -300px;
        width: 300px;
        height: calc(100% - 110px);
        background: var(--dark-gray);
        z-index: 1100;
        transition: left 0.3s;
        padding: 30px;
        overflow-y: auto;
    }

    .sidebar img {
      height: 80px !important;
      width: 100px;
    }

    .sidebar.open {
        left: 0;
    }

    .sidebar-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.5);
        z-index: 1099;
        display: none;
    }

    .sidebar-overlay.active {
        display: block;
    }

    .sidebar-links {
        display: flex;
        flex-direction: column;
        gap: 15px;
    }

    .sidebar-links a {
        color: white;
        text-decoration: none;
        padding: 10px 0;
        border-bottom: 1px solid var(--medium-gray);
        transition: all 0.3s;
    }

    .sidebar-links a:hover {
        color: var(--gold);
        padding-left: 10px;
    }

    /* Responsive */
    @media (max-width: 992px) {
      .nav-links {
          display: none;
      }

      .mobile-menu-btn {
          display: block;
      }

      .main-header {
          padding: 15px 0;
      }

      .main-header .thm-btn {
          display: none;
      }
    }
</style>

<!-- Header Markup -->
<header class="main-header">
    <div class="header-container">
        <div class="logo">
            <a href="{{ route('web.home') }}">
                <img src="{{ asset('public/'.$setting->img) }}" alt="Logo">
            </a>
        </div>

        <nav class="main-nav">
            <div class="nav-links">
                <a href="{{ route('web.home') }}">Home</a>
                <a href="{{ route('web.about') }}">About</a>
                <a href="{{ route('web.packages') }}">Packages</a>
                <a href="{{ route('web.voting') }}">Voting</a>
                <a href="{{ route('web.prizes') }}">Awards</a>
                <a href="{{ route('web.multi_media') }}">Media</a>
                <a href="{{ route('web.luxury') }}">Luxury</a>
                <a href="{{ route('web.contact') }}">Contact</a>
            </div>

            <button class="mobile-menu-btn" onclick="openSidebar()">
                <i class="fa fa-bars"></i>
            </button>
        </nav>

        <a href="{{route('web.becomesponsor')}}" class="buy-ticket__btn-1 thm-btn">
              Become Sponsor
          <span class="icon-arrow-right"></span>
        </a>
    </div>
</header>

<!-- Sidebar -->
<div class="sidebar-overlay" onclick="closeSidebar()"></div>
<div class="sidebar" id="sidebar">
    <div class="sidebar-links">
        <a href="{{ route('web.home') }}">Home</a>
        <a href="{{ route('web.about') }}">About</a>
        <a href="{{ route('web.packages') }}">Packages</a>
        <a href="{{ route('web.voting') }}">Voting</a>
        <a href="{{ route('web.prizes') }}">Awards</a>
        <a href="{{ route('web.contact') }}">Contact</a>
        <a href="{{ route('web.multi_media') }}">Media</a>
        <a href="{{ route('web.luxury') }}">Luxury</a>
        <a href="{{ route('web.becomesponsor') }}" style="color: var(--gold); font-weight: 600;">
          Become Sponsor
        </a>
    </div>
</div>

<script>
    function openSidebar() {
        document.getElementById('sidebar').classList.add('open');
        document.querySelector('.sidebar-overlay').classList.add('active');
    }

    function closeSidebar() {
        document.getElementById('sidebar').classList.remove('open');
        document.querySelector('.sidebar-overlay').classList.remove('active');
    }

    // Close sidebar when clicking on links
    document.querySelectorAll('.sidebar-links a').forEach(link => {
        link.addEventListener('click', closeSidebar);
    });

    window.addEventListener("scroll" , () => {
      if( window.scrollY > 40 ) {
        document.querySelector('.main-header').style.background = 'var(--black)';
      } else {
        document.querySelector('.main-header').style.background = 'transparent';
      }
    })
</script>
