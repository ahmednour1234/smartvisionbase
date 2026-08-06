@extends('layouts.layoutMaster')

@section('title', __('package.edit'))

@section('content')
<div class="card">
  <div class="card-header bg-warning text-dark">
    <h5 class="mb-0"><i class="bi bi-pencil-square me-2"></i> {{ __('package.edit') }}</h5>
  </div>
  <div class="card-body">
    <form action="{{ route('dashboard.packages.update', $package->id) }}" method="POST">
      @csrf
      @method('PUT')
      @include('content.packages._form', ['button' => __('package.edit'), 'package' => $package])
    </form>
  </div>
</div>
@endsection
