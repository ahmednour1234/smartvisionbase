@extends('layouts.layoutMaster')

@section('title', __('ads_create'))

@section('content')
<div class="card">
  <div class="card-header">
    <h4>{{ __('ads_create') }}</h4>
  </div>

  <div class="card-body">
    <form action="{{ route('dashboard.ads.store') }}" method="POST" enctype="multipart/form-data">
      @csrf
      @include('content.ads._form')
      <div class="text-end mt-3">
        <button type="submit" class="btn btn-primary">
          <i class="bi bi-save me-1"></i> {{ __('ads_create') }}
        </button>
      </div>
    </form>
  </div>
</div>
@endsection
