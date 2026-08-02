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
        <label class="form-label">@lang('Email')</label>
        <input type="email" class="form-control" name="email" placeholder="@lang('Application email')" value="{{ old('email', $settings->email ?? '') }}" />
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
          <input type="url" class="form-control" name="{{ $platform }}" value="{{ old($platform, $settings->$platform ?? '') }}" placeholder="https://{{ $platform }}.com/..." />
        </div>
      @endforeach

      <!-- Location -->
   <!-- Latitude -->
<div class="col-md-6">
  <label class="form-label">@lang('Latitude')</label>
  <input type="text" id="lat" name="lat" class="form-control" value="{{ old('lat', $settings->lat ?? '') }}" />
</div>

<!-- Longitude -->
<div class="col-md-6">
  <label class="form-label">@lang('Longitude')</label>
  <input type="text" id="lng" name="lang" class="form-control" value="{{ old('lang', $settings->lang ?? '') }}" />
</div>

<!-- Map -->
<div class="col-12 mb-3">
  <div id="map" style="width:100%; height:300px; border:1px solid #ddd;"></div>
</div>

    </div>

    <div class="pt-4">
      <button type="submit" class="btn btn-primary">@lang('Submit')</button>
    </div>
  </form>
</div>
@endsection
<script>
    // 1) Auto‑generate branch code

    // 2) Safe initMap: retry on window.load if map container not yet in DOM
function initMap() {
    const mapEl = document.getElementById("map");
    if (!mapEl) {
        window.addEventListener("load", initMap);
        return;
    }

    const defaultLoc = {
        lat: parseFloat(document.getElementById("lat").value) || 30.0444,
        lng: parseFloat(document.getElementById("lng").value) || 31.2357
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
        document.getElementById("lat").value = evt.latLng.lat();
        document.getElementById("lng").value = evt.latLng.lng();
    });
}

    // 3) Explicit DataTables init (only if plugin is loaded)
    document.addEventListener("DOMContentLoaded", function() {
        const table = document.getElementById("branches-table");
        if (table && window.jQuery && jQuery.fn.DataTable) {
            jQuery(table).DataTable({
                paging:   false,
                searching:false,
                info:     false,
                ordering: false
            });
        }
    });
</script>

<script async defer
    src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAQgTQ30_TriFBdJPKKOK4zZQ8rfHCUk6c&callback=initMap">
</script>

