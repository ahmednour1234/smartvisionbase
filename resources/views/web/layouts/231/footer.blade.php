@php
  use App\Models\Setting;
  use App\Models\Event;

  $event = Event::first();
  $locale = app()->getLocale();
  $setting = Setting::first();
@endphp

<style>
  .section-sm, .section-md, .section-lg, .section-xl {
    padding: 20px 0;
  }

  .logo-size {
    max-width: 300px;
    max-height: 200px;
    height: auto;
    width: auto;
  }

  #map {
    width: 100%;
    height: 250px;
    border-radius: 10px;
  }
  .icon-rect {
    position: relative;
    overflow: hidden;
    display: inline-flex
;
    justify-content: center;
    align-items: center;
    width: 40px;
    height: 40px;
    line-height: 40px;
}
.icon-black {
  color: #000 !important;
}
.icon-rect.icon-white, .icon-rect.icon-white:focus, .icon-rect.icon-white:active {
    color: #000000;
    background-color: #ffffff;
}

</style>

<!-- Section Pre Footer -->
<section class="section section-lg bg-gray-900">
  <div class="container">
    <div class="row row-30">

      <!-- Contact Us Column -->
      <div class="col-sm-6 col-lg-6">
        <h5><span class="big font-weight-sbold" style="font-size:50px;">Contact Us</span></h5>
             <p class="big text-white">
            {{ $setting->address }}
          </p>
        <div class="event-detail">
     

          <a class="brand d-block mb-3" href="{{ url('/') }}">
            @if($setting && $setting->img)
              <img class="brand-logo-light logo-size"
                   src="{{ asset('web/assets/images/x-01-o (1).webp') }}"
                   srcset="{{ asset($setting->img) }} 2x"
                   alt="Site Logo">
            @else
              <img class="brand-logo-light logo-size"
                   src="{{ asset('web/assets/images/logo-inverse-big.png') }}"
                   srcset="{{ asset('web/assets/images/logo-inverse-big@2x.png') }} 2x"
                   alt="Default Logo">
            @endif
          </a>

@php
    $socials = [
        'facebook' => 'facebook',
        'twitter' => 'twitter',
        'linkedin' => 'linkedin',
        'youtube' => 'youtube-play',
        'instagram' => 'instagram',
        'x' => 'twitter',
    ];
    $settings = \App\Models\Setting::first();
@endphp

@if($settings)
          <ul class="list-inline list-inline-xs">
        @foreach ($socials as $field => $icon)
            @if (!empty($settings->$field))
                <li>
                    <a class="icon icon-rect icon-xs icon-white fa-{{ $icon }}" href="{{ $settings->$field }}" target="_blank">
                        <div class="icon-rect-overlay"></div>
                    </a>
                </li>
            @endif
        @endforeach
    </ul>
@endif



        </div>
      </div>

      <!-- Map Column -->
      <div class="col-sm-6 col-lg-6">
        <div id="map"></div>
      </div>

    </div>
  </div>
</section>

<!-- Divider -->
<div class="divider divider-gray-900 text-center"></div>

<!-- Google Maps API -->
<script>
  function initMap() {
    const location = { lat: {{ $setting->lat ?? '24.7136' }}, lng: {{ $setting->lang ?? '46.6753' }} };
    const map = new google.maps.Map(document.getElementById("map"), {
      zoom: 14,
      center: location,
      styles: [ /* optional: custom dark theme */ ]
    });

    new google.maps.Marker({
      position: location,
      map: map,
      title: "Our Location"
    });
  }
</script>

<script async defer
  src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAQgTQ30_TriFBdJPKKOK4zZQ8rfHCUk6c&callback=initMap">
</script>
<script>
  document.addEventListener('DOMContentLoaded', function () {
    const navbar = document.querySelector('.rd-navbar-panel');

    function toggleNavbarBackground() {
      if (window.scrollY === 0) {
        navbar.classList.add('transparent');
        navbar.classList.remove('scrolled');
      } else {
        navbar.classList.remove('transparent');
        navbar.classList.add('scrolled');
      }
    }

    // أول تحميل
    toggleNavbarBackground();

    // عند التمرير
    window.addEventListener('scroll', toggleNavbarBackground);
  });
</script>
