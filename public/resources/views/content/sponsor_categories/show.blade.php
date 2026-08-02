
@extends('layouts.layoutMaster')

@section('title', $sponsorCategory->name)

@section('content')
<div class="card">
  <div class="card-body">
    <div class="row g-4 align-items-center">
      <div class="col-md-4 text-center">
        @if($sponsorCategory->logo)
          <img src="{{ asset($sponsorCategory->logo) }}" alt="Logo" class="img-thumbnail" width="200">
        @else
          <img src="https://via.placeholder.com/200x200?text=No+Logo" class="img-thumbnail" alt="No Logo">
        @endif
      </div>

      <div class="col-md-8">
        <h4 class="mb-3">{{ __('sponsor_category.details') }}</h4>
        <ul class="list-group list-group-flush">
          <li class="list-group-item">
            <strong>{{ __('sponsor_category.name') }}:</strong>
            {{ $sponsorCategory->name }}
          </li>
          <li class="list-group-item">
            <strong>{{ __('sponsor_category.name_en') }}:</strong>
            {{ $sponsorCategory->name_en }}
          </li>
          <li class="list-group-item">
            <strong>{{ __('sponsor_category.status') }}:</strong>
            @if($sponsorCategory->active)
              <span class="badge bg-success">{{ __('general.active') }}</span>
            @else
              <span class="badge bg-danger">{{ __('general.inactive') }}</span>
            @endif
          </li>
          <li class="list-group-item">
            <strong>{{ __('general.created_at') }}:</strong>
            {{ $sponsorCategory->created_at->format('Y-m-d H:i') }}
          </li>
          <li class="list-group-item">
            <strong>{{ __('general.updated_at') }}:</strong>
            {{ $sponsorCategory->updated_at->format('Y-m-d H:i') }}
          </li>
        </ul>

        <a href="{{ route('admin.sponsor_categories.index') }}" class="btn btn-outline-secondary mt-4">
          <i class="fas fa-arrow-left me-1"></i> {{ __('general.back') }}
        </a>
      </div>
    </div>
  </div>
</div>
@endsection
