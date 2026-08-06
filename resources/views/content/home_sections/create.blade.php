@extends('layouts.layoutMaster')

@section('title', __('home_sections.create_title'))

@section('content')
<div class="card">
  <div class="card-header bg-primary text-white">
    <h5 class="mb-0">{{ __('home_sections.create_title') }}</h5>
  </div>
  <div class="card-body">
    <form action="{{ route('dashboard.home_sections.store') }}" method="POST" enctype="multipart/form-data">
      @csrf
      @include('content.home_sections._form', ['homeSection' => null])
      <button type="submit" class="btn btn-success">{{ __('home_sections.save') }}</button>
    </form>
  </div>
</div>
@endsection
