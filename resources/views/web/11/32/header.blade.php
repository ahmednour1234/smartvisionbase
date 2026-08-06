     <!-- Section Header Default-->
     <header class="section page-header">
         <!--RD Navbar-->
         <div class="rd-navbar-wrap">
             <nav class="rd-navbar rd-navbar-classic" data-layout="rd-navbar-fixed" data-sm-layout="rd-navbar-fixed"
                 data-md-layout="rd-navbar-fixed" data-md-device-layout="rd-navbar-fixed" data-lg-layout="rd-navbar-static"
                 data-lg-device-layout="rd-navbar-static" data-xl-layout="rd-navbar-static"
                 data-xl-device-layout="rd-navbar-static" data-xxl-layout="rd-navbar-static"
                 data-xxl-device-layout="rd-navbar-static" data-lg-stick-up-offset="46px" data-xl-stick-up-offset="46px"
                 data-xxl-stick-up-offset="76px" data-lg-stick-up="true" data-xl-stick-up="true"
                 data-xxl-stick-up="true">
                 <div class="rd-navbar-collapse-toggle rd-navbar-fixed-element-1"
                     data-rd-navbar-toggle=".rd-navbar-collapse"><span></span></div>
                 <div class="rd-navbar-main-outer">
                     <div class="rd-navbar-main">
                         <!--RD Navbar Panel-->
                         <div class="rd-navbar-panel ">
                             <!--RD Navbar Toggle-->
                             <button class="rd-navbar-toggle"
                                 data-rd-navbar-toggle=".rd-navbar-nav-wrap"><span></span></button>
                             <!--RD Navbar Brand-->
                             <div class="rd-navbar-brand">
                                 @php
                                     $settings = \App\Models\Setting::first();
                                 @endphp

                                 <!--Brand-->
                                 <a class="brand" href="{{ route('web.home') }}">
                                     @if ($settings && $settings->img)
                                         <img class="brand-logo-dark" src="{{ asset($settings->img) }}"
                                             srcset="{{ asset($settings->img) }} 2x" alt="Logo" />

                                         <img class="brand-logo-light" src="{{ asset($settings->img) }}"
                                             srcset="{{ asset($settings->img) }} 2x" alt="Logo" />
                                     @else
                                         <img class="brand-logo-dark" src="{{ asset('images/logo-default.png') }}"
                                             srcset="{{ asset('images/logo-default@2x.png') }} 2x" alt="Logo" />
                                         <img class="brand-logo-light" src="{{ asset('images/logo-inverse.png') }}"
                                             srcset="{{ asset('images/logo-inverse@2x.png') }} 2x" alt="Logo" />
                                     @endif
                                 </a>
                             </div>
                         </div>
                         <!-- Rd Navbar Navigation-->
                     <div class="rd-navbar-main-element">

                     <!-- This is the haeder modification. -->
                      <div class="rd-navbar-nav-wrap">
                          <ul class="rd-navbar-nav">
                              <li class="rd-nav-item {{ request()->routeIs('web.home') ? 'active' : '' }}">
                                  <a class="rd-nav-link" href="{{ route('web.home') }}">{{ __('Home') }}</a>
                              </li>

                              <li class="rd-nav-item {{ request()->routeIs('web.about') ? 'active' : '' }}">
                                  <a class="rd-nav-link" href="{{ route('web.about') }}">{{ __('About') }}</a>
                              </li>

                              <li class="rd-nav-item {{ request()->routeIs('web.speaker') ? 'active' : '' }}">
                                  <a class="rd-nav-link" href="{{ route('web.speaker') }}">{{ __('Speakers') }}</a>
                              </li>

                              <li class="rd-nav-item {{ request()->routeIs('web.sponsors') ? 'active' : '' }}">
                                  <a class="rd-nav-link" href="{{ route('web.sponsors') }}">{{ __('Sponsors') }}</a>
                              </li>

                              <li class="rd-nav-item {{ request()->routeIs('web.packages') ? 'active' : '' }}">
                                  <a class="rd-nav-link" href="{{ route('web.packages') }}">{{ __('Packages') }}</a>
                              </li>

                              {{-- الروابط اللي كانت في الـ More --}}
                              <li class="rd-nav-item {{ request()->routeIs('web.schdule') ? 'active' : '' }}">
                                  <a class="rd-nav-link" href="{{ route('web.schdule') }}">{{ __('Schedule Event') }}</a>
                              </li>
                              <li class="rd-nav-item {{ request()->routeIs('web.blogs') ? 'active' : '' }}">
                                  <a class="rd-nav-link" href="{{ route('web.blogs') }}">{{ __('Blogs') }}</a>
                              </li>
                              <li class="rd-nav-item {{ request()->routeIs('web.multi_media') ? 'active' : '' }}">
                                  <a class="rd-nav-link" href="{{ route('web.multi_media') }}">{{ __('Multi Media') }}</a>
                              </li>
                          </ul>
                      </div>

    <!--<div class="rd-navbar-collapse toggle-original-elements d-block d-md-none">-->
<!--  <a href="{{ route('web.register') }}" class="custom-button-white mb-2 text-center">-->
<!--    {{ __('register_now') }}-->
<!--  </a>-->
<!--  <a href="{{ route('web.becomesponsor') }}" class="custom-button-red text-center">-->
<!--    {{ __('become_sponsor') }}-->
<!--  </a>-->
<!--</div>-->
</div>

                         <!-- RD Navbar Collapse-->

 <div class="action-buttons d-none d-md-flex">
  <a href="{{ route('web.register') }}" class="custom-button-white">
    {{ __('register_now') }}
  </a>
  <a href="{{ route('web.becomesponsor') }}" class="custom-button-red">
    {{ __('become_sponsor') }}
  </a>
</div>

                     </div>
             </nav>
         </div>
     </header>
<style>
    .btn-same-size {
        min-width: 160px;
        text-align: center;
        padding: 10px 15px;
        font-weight: 500;
        white-space: nowrap;
    }

    /* This is the haeder modification. */
    .rd-navbar-nav {
      margin-bottom: 0;
    }

    .navbar-buttons {
        gap: 10px;
        flex-wrap: nowrap !important;
    }

    @media (max-width: 768px) {
        .navbar-buttons {
            flex-wrap: wrap !important;
            justify-content: center;
            margin-top: 10px;
        }
    }
    .rd-navbar-classic.rd-navbar-static .rd-navbar-aside-outer, .rd-navbar-classic.rd-navbar-static .rd-navbar-main-outer {
    padding-left: 0px;
    padding-right: 0px;
}
.rd-navbar-classic.rd-navbar-static .rd-navbar-aside, .rd-navbar-classic.rd-navbar-static .rd-navbar-main {
    margin-left: 10;
    margin-right: 10;
}
</style>
