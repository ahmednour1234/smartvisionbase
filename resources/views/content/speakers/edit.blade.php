@extends('layouts.layoutMaster')

@section('title', __('speaker.edit'))

@section('content')
<div class="card">
  <div class="card-header">
    <h5 class="mb-0">{{ __('speaker.edit') }}</h5>
  </div>
  <div class="card-body">
    <form action="{{ route('admin.speakers.update', $speaker->id) }}" method="POST" enctype="multipart/form-data">
      @csrf
      @method('PUT')
      @include('content.speakers._form', ['speaker' => $speaker])
      <button type="submit" class="btn btn-primary mt-3">
        <i class="fas fa-save me-1"></i> {{ __('general.update') }}
      </button>
    </form>
  </div>
</div>
@endsection
