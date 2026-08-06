@extends('layouts.layoutMaster')

@section('title', __('event.add'))

@section('content')
<div class="card">
  <div class="card-header">
    <h5 class="mb-0">{{ __('event.add') }}</h5>
  </div>
  <div class="card-body">
    <form action="{{ route('admin.events.store') }}" method="POST" enctype="multipart/form-data">
      @csrf
      @include('content.events.form')
      <button type="submit" class="btn btn-primary mt-3">
        <i class="fas fa-save me-1"></i> {{ __('general.save') }}
      </button>
    </form>
  </div>
</div>
@endsection
