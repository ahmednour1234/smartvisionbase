@extends('layouts.layoutMaster')
@section('title', __('seo_edit'))

@section('content')
<div class="card">
  <div class="card-header bg-warning text-white">
    <h4>{{ __('seo_edit') }}</h4>
  </div>

  <div class="card-body">
    <form action="{{ route('dashboard.seos.update', $seo->id) }}" method="POST" enctype="multipart/form-data">
      @csrf
      @method('PUT')
      @include('content.seo._form')
      <div class="text-end mt-3">
        <button type="submit" class="btn btn-success">{{ __('global.update') }}</button>
        <a href="{{ route('dashboard.seos.index') }}" class="btn btn-secondary">{{ __('global.back') }}</a>
      </div>
    </form>
  </div>
</div>
@endsection
