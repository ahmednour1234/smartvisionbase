@extends('layouts.layoutMaster')

@section('title', __('package.create'))

@section('content')
<div class="card">
  <div class="card-header bg-primary text-white">
    <h5 class="mb-0"><i class="bi bi-plus-lg me-2"></i> {{ __('package.create') }}</h5>
  </div>
  <div class="card-body">
    <form action="{{ route('dashboard.packages.store') }}" method="POST">
      @csrf
      @include('content.packages._form', ['button' => __('package.create')])
    </form>
  </div>
</div>
@endsection
