@extends('layouts.layoutMaster')

@section('title', __('blog.edit'))

@section('content')
<div class="card">
  <div class="card-header bg-warning text-white">
    <h5 class="mb-0"><i class="bi bi-pencil-square me-1"></i> {{ __('blog.edit') }}</h5>
  </div>
  <div class="card-body">
    <form action="{{ route('dashboard.blogs.update', $blog->id) }}" method="POST" enctype="multipart/form-data">
      @csrf
      @method('PUT')
      @include('content.blogs._form', ['button' => __('general.update')])
    </form>
  </div>
</div>
@endsection
