@extends('layouts.layoutMaster')

@section('title', __('package.title'))

@section('content')
<div class="card shadow-sm">
  <div class="card-header bg-primary text-white">
    <h5 class="mb-0">
      <i class="bi bi-info-circle me-2"></i> {{ __('package.title') }}
    </h5>
  </div>

  <div class="card-body">
    <div class="row gy-4">

      <div class="col-md-6">
        <label class="fw-bold text-muted">{{ __('package.name_ar') }}</label>
        <div class="border rounded p-2">{{ $package->name_ar }}</div>
      </div>

      <div class="col-md-6">
        <label class="fw-bold text-muted">{{ __('package.name_en') }}</label>
        <div class="border rounded p-2">{{ $package->name_en }}</div>
      </div>

      <div class="col-md-6">
        <label class="fw-bold text-muted">{{ __('package.title_ar') }}</label>
        <div class="border rounded p-2">{{ $package->title_ar }}</div>
      </div>

      <div class="col-md-6">
        <label class="fw-bold text-muted">{{ __('package.title_en') }}</label>
        <div class="border rounded p-2">{{ $package->title_en }}</div>
      </div>

      <div class="col-md-6">
        <label class="fw-bold text-muted">{{ __('package.price') }}</label>
        <div class="border rounded p-2">{{ number_format($package->price, 2) }}</div>
      </div>

      <div class="col-md-6">
        <label class="fw-bold text-muted">{{ __('package.price_discount') }}</label>
        <div class="border rounded p-2">{{ number_format($package->price_discount, 2) }}</div>
      </div>

      <div class="col-md-6">
        <label class="fw-bold text-muted">{{ __('package.sort') }}</label>
        <div class="border rounded p-2">{{ $package->sort }}</div>
      </div>

      <div class="col-md-6">
        <label class="fw-bold text-muted">{{ __('package.active') }}</label>
        <div>
          <span class="badge bg-{{ $package->active ? 'success' : 'secondary' }} px-3 py-2">
            {{ $package->active ? __('package.status_active') : __('package.status_inactive') }}
          </span>
        </div>
      </div>

      <div class="col-md-6">
        <label class="fw-bold text-muted">{{ __('package.description_ar') }}</label>
        <div class="border rounded p-2">{{ $package->description_ar }}</div>
      </div>

      <div class="col-md-6">
        <label class="fw-bold text-muted">{{ __('package.description_en') }}</label>
        <div class="border rounded p-2">{{ $package->description_en }}</div>
      </div>

    </div>

    <div class="mt-4 text-end">
      <a href="{{ route('dashboard.packages.index') }}" class="btn btn-outline-secondary px-4">
        <i class="bi bi-arrow-left me-1"></i> {{ __('package.back') }}
      </a>
    </div>
  </div>
</div>
@endsection
