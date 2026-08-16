
// 1. create.blade.php

@extends('layouts.layoutMaster')

@section('title', __('speaker.add'))

@section('content')
<div class="card">
  <h5 class="mb-0">
    @if(isset($type) && $type == 1)
        {{ __('speaker.add') }}
    @elseif(isset($type) && $type == 2)
        {{ __('speaker.special_guests_title') }}
    @else
        {{ __('speaker.add') }}
    @endif
</h5>

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
