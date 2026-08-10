@extends('layouts.layoutMaster')

@section('title', __('sponsor_category.add'))

@section('content')
<div class="card">
  <div class="card-header">
    <h5 class="mb-0">{{ __('sponsor_category.add') }}</h5>
  </div>
  <div class="card-body">
    <form action="{{ route('admin.sponsor_categories.store') }}" method="POST" enctype="multipart/form-data">
      @csrf
      @include('content.sponsor_categories._form')
      <button type="submit" class="btn btn-primary mt-3">
        <i class="fas fa-save me-1"></i> {{ __('general.save') }}
      </button>
    </form>
  </div>
</div>
@endsection
