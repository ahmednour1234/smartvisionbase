@extends('layouts.layoutMaster')
@section('title', __('seo_create'))

@section('content')
<div class="card">
  <div class="card-header bg-primary text-white">
    <h4>{{ __('seo_create') }}</h4>
  </div>

  <div class="card-body">
    <form action="{{ route('dashboard.seos.store') }}" method="POST" enctype="multipart/form-data">
      @csrf
      @include('content.seo._form')
      <div class="text-end mt-3">
        <button type="submit" class="btn btn-success">{{ __('global.save') }}</button>
        <a href="{{ route('dashboard.seos.index') }}" class="btn btn-secondary">{{ __('global.back') }}</a>
      </div>
    </form>
  </div>
</div>
@endsection
