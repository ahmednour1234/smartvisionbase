@extends('layouts.layoutMaster')

@section('title', __('ads_show'))

@section('content')
<div class="card">
  <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
    <h4 class="mb-0">
      {{ __('ads_show') }}: {{ $ad->name[app()->getLocale()] ?? '' }}
    </h4>
    <a href="{{ route('dashboard.ads.index') }}" class="btn btn-light btn-sm text-dark">
      <i class="bi bi-arrow-left-circle"></i> {{ __('global.back') }}
    </a>
  </div>

  <div class="card-body">
    <div class="row gy-4">
      <div class="col-md-6">
        <strong>{{ __('ads_name') }}:</strong>
        <p class="text-muted">{{ $ad->name[app()->getLocale()] ?? '' }}</p>
      </div>

      <div class="col-md-6">
        <strong>{{ __('ads_link') }}:</strong>
        <p>
          @if($ad->link)
            <a href="{{ $ad->link }}" target="_blank" class="text-decoration-underline">{{ $ad->link }}</a>
          @else
            <span class="text-muted">—</span>
          @endif
        </p>
      </div>

      <div class="col-md-6">
        <strong>{{ __('ads_start_date') }}:</strong>
        <p class="text-muted">{{ $ad->start_date?->format('Y-m-d') }}</p>
      </div>

      <div class="col-md-6">
        <strong>{{ __('ads_end_date') }}:</strong>
        <p class="text-muted">{{ $ad->end_date?->format('Y-m-d') }}</p>
      </div>

      <div class="col-md-6">
        <strong>{{ __('ads_sort') }}:</strong>
        <p class="text-muted">{{ $ad->sort }}</p>
      </div>

      <div class="col-md-6">
        <strong>{{ __('ads_active') }}:</strong>
        <span class="badge {{ $ad->active ? 'bg-success' : 'bg-danger' }}">
          {{ $ad->active ? __('ads_active_yes') : __('ads_active_no') }}
        </span>
      </div>

      <div class="col-md-6">
        <strong>{{ __('ads_description') }}:</strong>
        <p class="text-muted">{{ $ad->description[app()->getLocale()] ?? '—' }}</p>
      </div>

      @if($ad->img)
      <div class="col-md-6">
        <strong>{{ __('ads_img') }}:</strong>
        <div class="mt-2">
          <img src="{{ asset($ad->img) }}" alt="Ad Image" class="img-thumbnail" style="max-height: 250px;">
        </div>
      </div>
      @endif
    </div>
  </div>
</div>
@endsection
