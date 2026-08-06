@extends('layouts.layoutMaster')

@section('title', __('blog.create'))

@section('content')
<div class="card">
  <div class="card-header bg-primary text-white">
    <h5 class="mb-0"><i class="bi bi-plus-circle me-1"></i> {{ __('blog.create') }}</h5>
  </div>
  <div class="card-body">
    <form action="{{ route('dashboard.blogs.store') }}" method="POST" enctype="multipart/form-data">
      @csrf
      @include('content.blogs._form', ['button' => __('general.save')])
    </form>
  </div>
</div>
@endsection
