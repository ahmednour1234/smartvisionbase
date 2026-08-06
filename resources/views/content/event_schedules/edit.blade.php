@extends('layouts.layoutMaster')

@section('title', __('event_schedule.edit'))

@section('content')
<div class="card">
  <div class="card-header">
    <h5 class="mb-0">{{ __('event_schedule.edit') }}</h5>
  </div>
  <div class="card-body">
    <form action="{{ route('admin.event_schedules.update', $eventSchedule->id) }}" method="POST" enctype="multipart/form-data">
      @csrf
      @method('PUT')

      @include('content.event_schedules._form', ['eventSchedule' => $eventSchedule])

      <div class="mt-4 text-center">
        <button type="submit" class="btn btn-primary">
          <i class="fas fa-save me-1"></i> {{ __('general.save') }}
        </button>
        <a href="{{ route('admin.event_schedules.index') }}" class="btn btn-outline-secondary ms-2">
          {{ __('general.cancel') }}
        </a>
      </div>
    </form>
  </div>
</div>
@endsection
