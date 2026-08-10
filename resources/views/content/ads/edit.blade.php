@extends('layouts.layoutMaster')

@section('title', __('ads_edit'))

@section('content')
<div class="card">
  <div class="card-header">
    <h4>{{ __('ads_edit') }}</h4>
  </div>

  <div class="card-body">
    <form action="{{ route('dashboard.ads.update', $ad->id) }}" method="POST" enctype="multipart/form-data">
      @csrf
      @method('PUT')
      @include('content.ads._form', ['ad' => $ad])
      <div class="text-end mt-3">
        <button type="submit" class="btn btn-success">
          <i class="bi bi-save me-1"></i> {{ __('ads_edit') }}
        </button>
      </div>
    </form>
  </div>
</div>
@endsection
