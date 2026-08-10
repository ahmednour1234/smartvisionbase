@extends('layouts.layoutMaster')

@section('title', 'Application Settings')

@section('vendor-style')
  <link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/vendor/libs/select2/select2.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/vendor/libs/@form-validation/umd/styles/index.min.css') }}">
  <style>
    #map {
      height: 300px;
      width: 100%;
      border-radius: 8px;
      margin-top: 10px;
      border: 1px solid #ddd;
    }
  </style>
@endsection

@section('content')
@php $locale = app()->getLocale(); @endphp

<div class="card mb-4">
  <h5 class="card-header">@lang('Update Application Data')</h5>

  <form class="card-body" method="POST" action="{{ route('dashboard-setting-store') }}" enctype="multipart/form-data">
    @csrf

    <div class="row g-3">

      <!-- Application Info -->
      <div class="col-md-6">
        <label class="form-label">@lang('Application Name')</label>
        <input type="text" class="form-control" name="name" placeholder="@lang('Application Name')" value="{{ old('name', $settings->name ?? '') }}" />
      </div>

      <div class="col-md-6">
        <label class="form-label">@lang('Phone')</label>
        <input type="text" class="form-control" name="phone" placeholder="@lang('Phone')" value="{{ old('phone', $settings->phone ?? '') }}" />
      </div>

      <div class="col-md-6">
        <label class="form-label">@lang('Address')</label>
        <input type="text" class="form-control" name="address" placeholder="@lang('Address')" value="{{ old('address', $settings->address ?? '') }}" />
      </div>

      <div class="col-md-6">
        <label class="form-label">@lang('email')</label>
        <input type="email" class="form-control" name="email" placeholder="@lang('email')" value="{{ old('email', $settings->email ?? '') }}" />
      </div>

      <!-- Logo -->
      <div class="col-md-6">
        <label class="form-label">@lang('Logo Image')</label>
        <input type="file" class="form-control" name="img" accept="image/*" />
        @if(isset($settings->img))
          <img src="{{ asset($settings->img) }}" alt="Logo" class="mt-2" style="width: 100px;">
        @endif
      </div>

      <!-- Floor Plan -->
      <div class="col-md-6">
        <label class="form-label">@lang('Floor Plan')</label>
        <input type="file" class="form-control" name="floor_plan" accept="image/*" />
        @if(isset($settings->floor_plan))
          <img src="{{ asset($settings->floor_plan) }}" alt="Floor Plan" class="mt-2" style="width: 100px;">
        @endif
      </div>

      <!-- Social Links -->
      @foreach(['facebook', 'instagram', 'linkedin', 'x', 'youtube', 'flickr'] as $platform)
        <div class="col-md-6">
          <label class="form-label">{{ ucfirst($platform) }}</label>
          <input
            type="url"
            class="form-control"
            name="{{ $platform }}"
            value="{{ old($platform, $settings->$platform ?? '') }}"
            placeholder="https://{{ $platform }}.com/..." />
        </div>
      @endforeach

      <!-- Voting Toggle (0/1) -->
      <div class="col-md-6 d-flex align-items-center">
        <div class="form-check form-switch">
          <input type="hidden" name="voting" value="0">
          <input
            class="form-check-input"
            type="checkbox"
            id="votingSwitch"
            name="voting"
            value="1"
            {{ old('voting', $settings->voting ?? 0) ? 'checked' : '' }}>
          <label class="form-check-label ms-2" for="votingSwitch">@lang('Enable Voting')</label>
        </div>
      </div>

      <!-- Location -->
      <div class="col-md-6">
        <label class="form-label">@lang('Latitude')</label>
        <input type="text" id="lat" name="lat" class="form-control" value="{{ old('lat', $settings->lat ?? '') }}" />
      </div>

      <!-- ملاحظة: نحافظ على الاسم كما هو (lang) للتوافق مع قاعدة البيانات لديك -->
      <div class="col-md-6">
        <label class="form-label">@lang('Longitude')</label>
        <input type="text" id="lng" name="lang" class="form-control" value="{{ old('lang', $settings->lang ?? '') }}" />
      </div>

      <div class="col-12 mb-3">
        <div id="map"></div>
      </div>

    </div>

    <div class="pt-4">
      <button type="submit" class="btn btn-primary">@lang('Submit')</button>
    </div>
  </form>
</div>
@endsection

@push('scripts')
<script>
  // 1) Safe initMap: re-run on window.load if needed
  function initMap() {
    const mapEl = document.getElementById("map");
    if (!mapEl) {
      window.addEventListener("load", initMap);
      return;
    }

    const latEl = document.getElementById("lat");
    const lngEl = document.getElementById("lng");

    const defaultLoc = {
      lat: parseFloat(latEl?.value) || 30.0444,   // Cairo default
      lng: parseFloat(lngEl?.value) || 31.2357
    };

    const map = new google.maps.Map(mapEl, {
      center: defaultLoc,
      zoom: 10
    });

    const marker = new google.maps.Marker({
      position: defaultLoc,
      map: map,
      draggable: true
    });

    marker.addListener("dragend", evt => {
      if (latEl) latEl.value = evt.latLng.lat();
      if (lngEl) lngEl.value = evt.latLng.lng();
    });

    // Click to move marker
    map.addListener("click", (e) => {
      marker.setPosition(e.latLng);
      if (latEl) latEl.value = e.latLng.lat();
      if (lngEl) lngEl.value = e.latLng.lng();
    });
  }

  // 2) DataTables (if present)
  document.addEventListener("DOMContentLoaded", function () {
    const table = document.getElementById("branches-table");
    if (table && window.jQuery && jQuery.fn.DataTable) {
      jQuery(table).DataTable({
        paging:    false,
        searching: false,
        info:      false,
        ordering:  false
      });
    }
  });
</script>

<script async defer
  src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAQgTQ30_TriFBdJPKKOK4zZQ8rfHCUk6c&callback=initMap">
</script>
@endpush
