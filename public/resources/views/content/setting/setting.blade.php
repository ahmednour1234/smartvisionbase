@extends('layouts.layoutMaster')

@section('title', 'Application Settings')

@php
  $locale     = app()->getLocale();
  $GMAPS_KEY  = env('GOOGLE_MAPS_KEY', 'AIzaSyAQgTQ30_TriFBdJPKKOK4zZQ8rfHCUk6c'); // عدّل من .env
  $settings   = $settings ?? (object)[];
  $socials    = ['facebook','instagram','linkedin','x','youtube','flickr'];
@endphp

@section('vendor-style')
  <link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/vendor/libs/select2/select2.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/vendor/libs/@form-validation/umd/styles/index.min.css') }}">
  <style>
    .settings-card { border: 1px solid #eef0f3; border-radius: 14px; box-shadow: 0 6px 20px rgba(18, 38, 63, 0.05); }
    .settings-header { padding: 1.25rem 1.5rem; border-bottom: 1px solid #eef0f3; background: linear-gradient(135deg, #f7f9fc, #ffffff); border-radius: 14px 14px 0 0; }
    .settings-title { display:flex; align-items:center; gap:.6rem; font-weight:700; margin:0; }
    .settings-title i { color:#556ee6; }
    .nav-pills .nav-link { border-radius: 999px; }
    .nav-pills .nav-link.active { background: #556ee6; }
    .section-hint { font-size:.875rem; color:#6c757d; }
    .form-label .req { color:#e74c3c; }
    .thumb { width: 120px; height: 120px; object-fit: cover; border-radius: 12px; border:1px solid #e9ecef; }
    #map { height: 340px; width: 100%; border-radius: 12px; border: 1px solid #e9ecef; }
    .input-icon { position:relative; }
    .input-icon i { position:absolute; left: .75rem; top: 50%; transform: translateY(-50%); color:#9aa0a6; }
    .input-icon input { padding-left: 2.25rem; }
    .small-muted { font-size: .8rem; color:#8a94a6; }
    .divider { height:1px; background:#eef0f3; margin: 1rem 0; }
  </style>
@endsection

@section('content')

@if ($errors->any())
  <div class="alert alert-danger alert-dismissible" role="alert">
    <strong>@lang('There are some errors')</strong>
    <ul class="mb-0 ps-3">
      @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
      @endforeach
    </ul>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="@lang('Close')"></button>
  </div>
@endif

<div class="card settings-card mb-4">
  <div class="settings-header">
    <h5 class="settings-title">
      <i class="ti ti-settings"></i> @lang('Application Settings')
    </h5>
    <p class="mb-0 section-hint">@lang('Update app info, branding, social links, features toggles, and location.')</p>
  </div>

  <div class="card-body pt-2">
    <ul class="nav nav-pills mb-4 flex-wrap" id="settingsTabs" role="tablist">
      <li class="nav-item me-2 mb-2">
        <button class="nav-link active" id="tab-general" data-bs-toggle="pill" data-bs-target="#p-general" type="button" role="tab">
          <i class="ti ti-info-circle me-1"></i>@lang('General')
        </button>
      </li>
      <li class="nav-item me-2 mb-2">
        <button class="nav-link" id="tab-brand" data-bs-toggle="pill" data-bs-target="#p-brand" type="button" role="tab">
          <i class="ti ti-photo me-1"></i>@lang('Branding')
        </button>
      </li>
      <li class="nav-item me-2 mb-2">
        <button class="nav-link" id="tab-social" data-bs-toggle="pill" data-bs-target="#p-social" type="button" role="tab">
          <i class="ti ti-share me-1"></i>@lang('Social Links')
        </button>
      </li>
      <li class="nav-item me-2 mb-2">
        <button class="nav-link" id="tab-features" data-bs-toggle="pill" data-bs-target="#p-features" type="button" role="tab">
          <i class="ti ti-toggle-left me-1"></i>@lang('Features')
        </button>
      </li>
      <li class="nav-item mb-2">
        <button class="nav-link" id="tab-location" data-bs-toggle="pill" data-bs-target="#p-location" type="button" role="tab">
          <i class="ti ti-map-pin me-1"></i>@lang('Location')
        </button>
      </li>
    </ul>

    <form method="POST" action="{{ route('dashboard-setting-store') }}" enctype="multipart/form-data">
      @csrf

      <div class="tab-content" id="settingsTabsContent">

        {{-- ========== GENERAL ========== --}}
        <div class="tab-pane fade show active" id="p-general" role="tabpanel" aria-labelledby="tab-general">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">
                @lang('Application Name') <span class="req">*</span>
              </label>
              <div class="input-icon">
                <i class="ti ti-apps"></i>
                <input type="text" class="form-control" name="name" required
                       placeholder="@lang('Application Name')"
                       value="{{ old('name', $settings->name ?? '') }}">
              </div>
            </div>

            <div class="col-md-6">
              <label class="form-label">@lang('Phone')</label>
              <div class="input-icon">
                <i class="ti ti-phone"></i>
                <input type="text" class="form-control" name="phone"
                       placeholder="@lang('Phone')"
                       value="{{ old('phone', $settings->phone ?? '') }}">
              </div>
            </div>

            <div class="col-md-6">
              <label class="form-label">@lang('Email')</label>
              <div class="input-icon">
                <i class="ti ti-mail"></i>
                <input type="email" class="form-control" name="email"
                       placeholder="email@domain.com"
                       value="{{ old('email', $settings->email ?? '') }}">
              </div>
            </div>

            <div class="col-md-6">
              <label class="form-label">@lang('Address')</label>
              <div class="input-icon">
                <i class="ti ti-map"></i>
                <input type="text" class="form-control" name="address"
                       placeholder="@lang('Address')"
                       value="{{ old('address', $settings->address ?? '') }}">
              </div>
            </div>

            <div class="divider"></div>

            <div class="col-md-4">
              <label class="form-label">@lang('Privacy')</label>
              <input type="text" class="form-control" name="privacy"
                     placeholder="@lang('Privacy')"
                     value="{{ old('privacy', $settings->privacy ?? '') }}">
              <small class="small-muted">@lang('You can put a short text or a URL.')</small>
            </div>

            <div class="col-md-4">
              <label class="form-label">@lang('About')</label>
              <input type="text" class="form-control" name="about"
                     placeholder="@lang('About')"
                     value="{{ old('about', $settings->about ?? '') }}">
            </div>

            <div class="col-md-4">
              <label class="form-label">@lang('Terms')</label>
              <input type="text" class="form-control" name="terms"
                     placeholder="@lang('Terms')"
                     value="{{ old('terms', $settings->terms ?? '') }}">
            </div>
          </div>
        </div>

        {{-- ========== BRANDING ========== --}}
        <div class="tab-pane fade" id="p-brand" role="tabpanel" aria-labelledby="tab-brand">
          <div class="row g-4 align-items-start">
            <div class="col-md-6">
              <label class="form-label">@lang('Logo Image')</label>
              <input type="file" class="form-control" name="img" accept="image/*" id="logoInput">
              <small class="small-muted d-block mt-1">@lang('Recommended: PNG, 512x512 or higher')</small>
              <div class="mt-3 d-flex align-items-center gap-3">
                <img id="logoPreview" class="thumb d-none" alt="logo preview">
                @if(!empty($settings->img))
                  <div class="d-flex flex-column">
                    <span class="small-muted">@lang('Current')</span>
                    <img src="{{ asset('public/'.$settings->img) }}" class="thumb" alt="Logo">
                  </div>
                @endif
              </div>
            </div>

            <div class="col-md-6">
              <label class="form-label">@lang('Floor Plan')</label>
              <input type="file" class="form-control" name="floor_plan" accept="image/*" id="floorInput">
              <small class="small-muted d-block mt-1">@lang('Optional image to show event/site floor map.')</small>
              <div class="mt-3 d-flex align-items-center gap-3">
                <img id="floorPreview" class="thumb d-none" alt="floor preview">
                @if(!empty($settings->floor_plan))
                  <div class="d-flex flex-column">
                    <span class="small-muted">@lang('Current')</span>
                    <img src="{{ asset('public/'.$settings->floor_plan) }}" class="thumb" alt="Floor Plan">
                  </div>
                @endif
              </div>
            </div>
                   <div class="col-md-6">
              <label class="form-label">@lang('img_mobile')</label>
              <input type="file" class="form-control" name="img_mobile" accept="image/*" id="img_mobile">
              <small class="small-muted d-block mt-1">@lang('Optional image to show event/site img_mobile map.')</small>
              <div class="mt-3 d-flex align-items-center gap-3">
                <img id="img_mobile" class="thumb d-none" alt="img_mobile">
                @if(!empty($settings->img_mobile))
                  <div class="d-flex flex-column">
                    <span class="small-muted">@lang('Current')</span>
                    <img src="{{ asset('public/'.$settings->img_mobile) }}" class="thumb" alt="Floor Plan">
                  </div>
                @endif
              </div>
            </div>
          </div>
        </div>

        {{-- ========== SOCIAL LINKS ========== --}}
        <div class="tab-pane fade" id="p-social" role="tabpanel" aria-labelledby="tab-social">
          <div class="row g-3">
            @foreach($socials as $platform)
              <div class="col-md-6">
                <label class="form-label text-capitalize">{{ ucfirst($platform) }}</label>
                <div class="input-icon">
                  <i class="ti ti-world"></i>
                  <input type="url" class="form-control" name="{{ $platform }}"
                         placeholder="https://{{ $platform }}.com/..."
                         value="{{ old($platform, $settings->$platform ?? '') }}">
                </div>
              </div>
            @endforeach
          </div>
        </div>

        {{-- ========== FEATURES ========== --}}
        <div class="tab-pane fade" id="p-features" role="tabpanel" aria-labelledby="tab-features">
          <div class="row g-3">
            <div class="col-md-6 d-flex align-items-center">
              <div class="form-check form-switch">
                <input type="hidden" name="voting" value="0">
                <input class="form-check-input" type="checkbox" id="votingSwitch" name="voting" value="1"
                       {{ old('voting', $settings->voting ?? 0) ? 'checked' : '' }}>
                <label class="form-check-label ms-2" for="votingSwitch">
                  <i class="ti ti-hand-click me-1"></i>@lang('Enable Voting')
                </label>
              </div>
            </div>
          </div>
        </div>

        {{-- ========== LOCATION ========== --}}
        <div class="tab-pane fade" id="p-location" role="tabpanel" aria-labelledby="tab-location">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">@lang('Latitude')</label>
              <div class="input-icon">
                <i class="ti ti-compass"></i>
                <input type="text" id="lat" name="lat" class="form-control"
                       value="{{ old('lat', $settings->lat ?? '') }}"
                       placeholder="e.g. 30.0444">
              </div>
            </div>

            {{-- ملاحظة مهمة: نحافظ على اسم الحقل lang (خط الطول) كما في قاعدة البيانات لديك --}}
            <div class="col-md-6">
              <label class="form-label">@lang('Longitude')</label>
              <div class="input-icon">
                <i class="ti ti-compass"></i>
                <input type="text" id="lng" name="lang" class="form-control"
                       value="{{ old('lang', $settings->lang ?? '') }}"
                       placeholder="e.g. 31.2357">
              </div>
            </div>

            <div class="col-12">
              <div id="map"></div>
              <small class="small-muted d-block mt-2">
                @lang('Drag the marker or click on the map to update coordinates.')
              </small>
            </div>
          </div>
        </div>

      </div>

      <div class="mt-4 d-flex justify-content-end gap-2">
        <a href="{{ url()->previous() }}" class="btn btn-outline-secondary">
          <i class="ti ti-arrow-left"></i> @lang('Back')
        </a>
        <button type="submit" class="btn btn-primary">
          <i class="ti ti-device-floppy"></i> @lang('Save Changes')
        </button>
      </div>
    </form>
  </div>
</div>
@endsection

<script>
  // ===== Image live preview =====
  const previewImage = (input, previewEl) => {
    const file = input?.files?.[0];
    if (!file) { previewEl.classList.add('d-none'); return; }
    const reader = new FileReader();
    reader.onload = e => {
      previewEl.src = e.target.result;
      previewEl.classList.remove('d-none');
    };
    reader.readAsDataURL(file);
  };

  document.addEventListener('DOMContentLoaded', () => {
    const logoInput   = document.getElementById('logoInput');
    const logoPreview = document.getElementById('logoPreview');
    const floorInput  = document.getElementById('floorInput');
    const floorPreview= document.getElementById('floorPreview');

    if (logoInput && logoPreview) {
      logoInput.addEventListener('change', () => previewImage(logoInput, logoPreview));
    }
    if (floorInput && floorPreview) {
      floorInput.addEventListener('change', () => previewImage(floorInput, floorPreview));
    }

    // Optional: Init Select2 if needed
    if (window.jQuery && jQuery.fn.select2) {
      jQuery('.select2').select2({ width: '100%' });
    }

    // Optional: DataTables if a table with id exists
    const table = document.getElementById('branches-table');
    if (table && window.jQuery && jQuery.fn.DataTable) {
      jQuery(table).DataTable({
        paging: false, searching: false, info: false, ordering: false
      });
    }
  });

  // ===== Google Map =====
  function initMap() {
    const mapEl = document.getElementById('map');
    const latEl = document.getElementById('lat');
    const lngEl = document.getElementById('lng'); // id=lng لكن name=lang (حسب قاعدة البيانات)

    if (!mapEl) return;

    const defaultLoc = {
      lat: parseFloat(latEl?.value) || 30.0444,  // Cairo default
      lng: parseFloat(lngEl?.value) || 31.2357
    };

    const map = new google.maps.Map(mapEl, { center: defaultLoc, zoom: 10 });
    const marker = new google.maps.Marker({ position: defaultLoc, map, draggable: true });

    const setInputs = (latLng) => {
      if (latEl) latEl.value = latLng.lat();
      if (lngEl) lngEl.value = latLng.lng();
    };

    marker.addListener('dragend', e => setInputs(e.latLng));
    map.addListener('click', e => { marker.setPosition(e.latLng); setInputs(e.latLng); });
  }
</script>

<script async defer
  src="https://maps.googleapis.com/maps/api/js?key={{ $GMAPS_KEY }}&callback=initMap">
</script>
