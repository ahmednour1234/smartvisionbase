
// 1. create.blade.php

@extends('layouts.layoutMaster')

@section('title', __('speaker.add'))

@section('content')
<div class="card">
  <div class="card-header">
    <h5 class="mb-0">{{ __('speaker.add') }}</h5>
  </div>
  <div class="card-body">
    <form action="{{ route('admin.speakers.store') }}" method="POST" enctype="multipart/form-data">
      @csrf
      @include('content.speakers._form')
      <button type="submit" class="btn btn-primary mt-3">
        <i class="fas fa-save me-1"></i> {{ __('general.save') }}
      </button>
    </form>
  </div>
</div>
@endsection
