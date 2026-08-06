@extends('layouts.layoutMaster')

@section('content')
<div class="card">
  <div class="card-header">
    <h5 class="mb-0">{{ __('sponsor_category.edit') }}</h5>
  </div>
  <div class="card-body">
    <form action="{{ route('admin.sponsor_categories.update', $sponsorCategory->id) }}" method="POST" enctype="multipart/form-data">
      @csrf
      @method('PUT')
      @include('content.sponsor_categories._form', ['sponsorCategory' => $sponsorCategory])
      <button type="submit" class="btn btn-primary mt-3">
        <i class="fas fa-save me-1"></i> {{ __('general.update') }}
      </button>
    </form>
  </div>
</div>
@endsection
