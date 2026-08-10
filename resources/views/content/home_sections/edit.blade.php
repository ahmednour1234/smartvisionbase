@extends('layouts.layoutMaster')

@section('title', __('home_sections.edit_title'))

@section('content')
<div class="card">
  <div class="card-header bg-warning text-white">
    <h5 class="mb-0">{{ __('home_sections.edit_title') }}</h5>
  </div>
  <div class="card-body">
    <form action="{{ route('dashboard.home_sections.update', $homeSection->id) }}" method="POST" enctype="multipart/form-data">
      @csrf
      @method('PUT')
      @include('content.home_sections._form', ['homeSection' => $homeSection])
      <button type="submit" class="btn btn-primary">{{ __('home_sections.update') }}</button>
    </form>
  </div>
</div>
@endsection
