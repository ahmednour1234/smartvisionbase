@extends('layouts.layoutMaster')

@section('title', __('speaker.edit'))

@section('content')
<div class="card">
  <div class="card-header d-flex justify-content-between align-items-center">
    <h5 class="mb-0">{{ __('speaker.edit') }}</h5>
    @if(session('success'))
      <span class="badge bg-success">{{ session('success') }}</span>
    @endif
  </div>

  <div class="card-body">
    <form action="{{ route('updatenew', [$type, $speaker->id]) }}" method="POST" enctype="multipart/form-data">
      @csrf
      {{-- لاحظ: لا نستخدم @method('PUT') لتجنّب 403 على بعض الاستضافات --}}
      @include('content.speakers._form', ['speaker' => $speaker, 'type' => $type])

      <button type="submit" class="btn btn-primary mt-3">
        <i class="fas fa-save me-1"></i> {{ __('general.update') }}
      </button>
    </form>
  </div>
</div>
@endsection
