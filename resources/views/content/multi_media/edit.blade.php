@extends('layouts.layoutMaster')

@section('title', __('multi_media.edit'))

@section('content')
<div class="card">
  <div class="card-header">
    <h5 class="mb-0">{{ __('multi_media.edit') }}</h5>
  </div>
  <div class="card-body">
    <form action="{{ route('dashboard.multimedia-categories.update', $category->id) }}" method="POST" enctype="multipart/form-data">
      @csrf
      @method('PUT')
      @include('content.multi_media._form', ['button' => __('multi_media.update'), 'category' => $category])
    </form>
  </div>
</div>
@endsection
