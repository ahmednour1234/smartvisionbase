@php
  use App\Models\Setting;
  use App\Models\Event;

  $event = Event::first();
  $locale = app()->getLocale();
  $setting = Setting::first();
@endphp

<!-- Google Font: Barlow Condensed -->
<link href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@400;600;700&display=swap" rel="stylesheet">

<!-- Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" integrity="sha512-..." crossorigin="anonymous" referrerpolicy="no-referrer" />

<style>
  body, section, .contact-section, h1, h2, h3, h4, h5, h6, p, a, span, div {
    font-family: 'Barlow Condensed', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif !important;
  }

  .logo-size {
    max-width: 250px;
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
    display: flex;
    justify-content: center;
    align-items: center;
    background-color: #ffffff;
    width: 40px;
    height: 40px;
    border-radius: 4px;
    font-size: 18px;
    margin-right: 10px;
    transition: all 0.3s ease-in-out;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  }

  .icon-rect i {
    color: #000000 !important;
  }

  .icon-rect:hover {
    transform: translateY(-2px);
    background-color: #ffffff;
  }

  .social-icons {
    display: flex;
    flex-wrap: wrap;
    margin-top: 20px;
    
        margin-bottom: 20px;

  }

  .contact-section {
    background: linear-gradient(90deg, #FFE986 0%, #C48127 100%);
    color: white;
    padding: 40px 0;
  }
 .scroll-to-top {
    display: none !important;
}


  .contact-section h5,
  .contact-section p,
  .contact-section a {
    color: black;
  }

  .divider {
    background: #C48127;
    height: 4px;
  }
</style>

<section class="contact-section">
  <div class="container">
    <div class="row row-30">

      <!-- Contact Column -->
      <div class="col-sm-6 col-lg-6">
        <h5><span class="big font-weight-sbold" style="font-size:50px;">Contact Us</span></h5>

        <p class="big">
          {{ $setting->address }}
        </p>

        <a class="brand d-block mb-3" href="{{ url('/') }}">
          @if($setting && $setting->img)
            <img class="brand-logo-light logo-size"
                 src="https://toptrustedfxbrokers.com/public/2W-01.png"
                 alt="Site Logo">
          @else
            <img class="brand-logo-light logo-size"
                 src="https://toptrustedfxbrokers.iqbrandx.com/public/2W-01.png"
                 srcset="{{ asset('public/web/assets/images/logo-inverse-big@2x.png') }} 2x"
                 alt="Default Logo">
          @endif
        </a>

        @php
          $socials = [
              'facebook' => 'fab fa-facebook-f',
              'twitter' => 'fab fa-twitter',
              'linkedin' => 'fab fa-linkedin-in',
              'youtube' => 'fab fa-youtube',
              'instagram' => 'fab fa-instagram',
              'x' => 'fab fa-twitter',
          ];
        @endphp

        @if($setting)
          <div class="social-icons">
            @foreach ($socials as $field => $icon)
              @if (!empty($setting->$field))
                <a href="{{ $setting->$field }}" style="text-decoration:none;" target="_blank" class="icon-rect">
                  <i class="{{ $icon }}"></i>
                </a>
              @endif
            @endforeach
          </div>
        @endif
      </div>

      <!-- Map Column -->
      <div class="col-sm-6 col-lg-6">
        <div id="map"></div>
      </div>

    </div>
  </div>
</section>

<!-- Divider -->
<div class="divider text-center"></div>

<!-- Google Maps -->
<script>
  function initMap() {
    const location = { lat: {{ $setting->lat ?? '24.7136' }}, lng: {{ $setting->lang ?? '46.6753' }} };
    const map = new google.maps.Map(document.getElementById("map"), {
      zoom: 14,
      center: location,
      styles: []
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