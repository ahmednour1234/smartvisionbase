@extends('layouts.layoutMaster')

@section('title', __('multi_media.create'))

@section('content')
<div class="card">
  <div class="card-header">
    <h5 class="mb-0">{{ __('multi_media.create') }}</h5>
  </div>
  <div class="card-body">
    <form action="{{ route('dashboard.multimedia-categories.store') }}" method="POST" enctype="multipart/form-data">
      @csrf
      @include('content.multi_media._form', ['button' => __('multi_media.save')])
    </form>
  </div>
</div>
@endsection
