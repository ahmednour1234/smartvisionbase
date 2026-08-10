@extends('layouts.layoutMaster')

@section('title', __('sponsor.details'))

@section('content')
<div class="card">
  <div class="card-header">
    <h5 class="mb-0">{{ __('sponsor.details') }}</h5>
  </div>
  <div class="card-body row g-4">

    <div class="col-md-3">
      <label class="form-label">{{ __('sponsor.image') }}</label><br>
      @if($sponsor->image)
        <img src="{{ asset($sponsor->image) }}" class="img-thumbnail" width="150">
      @else
        <span class="text-muted">-</span>
      @endif
    </div>

    <div class="col-md-9 row g-3">
      <div class="col-md-6">
        <label class="form-label">{{ __('sponsor.name_ar') }}</label>
        <div class="form-control">{{ $sponsor->name_ar }}</div>
      </div>

      <div class="col-md-6">
        <label class="form-label">{{ __('sponsor.name_en') }}</label>
        <div class="form-control">{{ $sponsor->name_en }}</div>
      </div>

      <div class="col-md-6">
        <label class="form-label">{{ __('sponsor.title_ar') }}</label>
        <div class="form-control">{{ $sponsor->title_ar }}</div>
      </div>

      <div class="col-md-6">
        <label class="form-label">{{ __('sponsor.title_en') }}</label>
        <div class="form-control">{{ $sponsor->title_en }}</div>
      </div>

      <div class="col-md-6">
        <label class="form-label">{{ __('sponsor.company_name_ar') }}</label>
        <div class="form-control">{{ $sponsor->company_name_ar }}</div>
      </div>

      <div class="col-md-6">
        <label class="form-label">{{ __('sponsor.company_name_en') }}</label>
        <div class="form-control">{{ $sponsor->company_name_en }}</div>
      </div>

      <div class="col-md-6">
        <label class="form-label">{{ __('sponsor.phone') }}</label>
        <div class="form-control">{{ $sponsor->phone }}</div>
      </div>

      <div class="col-md-6">
        <label class="form-label">{{ __('sponsor.category') }}</label>
        <div class="form-control">
          {{ app()->getLocale() == 'ar' ? $sponsor->category->name ?? '-' : $sponsor->category->name_en ?? '-' }}
        </div>
      </div>

      <div class="col-md-6">
        <label class="form-label">{{ __('sponsor.status') }}</label>
        <div class="form-control">
          @if($sponsor->active)
            <span class="badge bg-success">{{ __('sponsor.active') }}</span>
          @else
            <span class="badge bg-danger">{{ __('sponsor.inactive') }}</span>
          @endif
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
