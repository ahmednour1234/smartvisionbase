@extends('layouts.layoutMaster')

@section('title', __('event_schedule.create'))

@section('content')
<div class="card">
  <div class="card-header">
    <h5>{{ __('event_schedule.create') }}</h5>
  </div>
  <div class="card-body">
    <form action="{{ route('admin.event_schedules.store') }}" method="POST" enctype="multipart/form-data">
      @csrf
      @include('content.event_schedules._form', ['eventSchedule' => null])
      <div class="mt-3 text-center">
        <button type="submit" class="btn btn-primary">
          <i class="fas fa-save"></i> {{ __('general.save') }}
        </button>
      </div>
    </form>
  </div>
</div>
@endsection
