@extends('layouts.layoutMaster')

@section('title', __('multi_media.edit'))

@section('content')
<div class="card">
  <div class="card-header">
    <h5 class="mb-0">{{ __('multi_media.edit') }}</h5>
  </div>
  <div class="card-body">
    <form action="{{ route('dashboard.multi-medias.update', $media->id) }}" method="POST" enctype="multipart/form-data">
      @csrf
      @method('PUT')
      @include('content.multi_mediall._form', ['button' => __('multi_media.update'), 'media' => $media])
    </form>
  </div>
</div>
@endsection
