@extends('layouts.layoutMaster')

@section('title', __('event_schedule.details'))

@section('content')
<div class="card">
  <div class="card-header bg-primary text-white">
    <h5 class="mb-0"><i class="bi bi-calendar-event me-2"></i> {{ __('event_schedule.details') }}</h5>
  </div>

  <div class="card-body">
    <div class="row mb-3">
      <div class="col-md-6">
        <strong>{{ __('event_schedule.title_ar') }}:</strong>
        <p>{{ $eventSchedule->title_ar }}</p>
      </div>
      <div class="col-md-6">
        <strong>{{ __('event_schedule.title_en') }}:</strong>
        <p>{{ $eventSchedule->title_en }}</p>
      </div>
    </div>

    <div class="row mb-3">
      <div class="col-md-6">
        <strong>{{ __('event_schedule.description_ar') }}:</strong>
        <p>{{ $eventSchedule->description_ar ?: '---' }}</p>
      </div>
      <div class="col-md-6">
        <strong>{{ __('event_schedule.description_en') }}:</strong>
        <p>{{ $eventSchedule->description_en ?: '---' }}</p>
      </div>
    </div>

    <div class="row mb-3">
      <div class="col-md-6">
        <strong>{{ __('event_schedule.location_ar') }}:</strong>
        <p>{{ $eventSchedule->location_ar }}</p>
      </div>
      <div class="col-md-6">
        <strong>{{ __('event_schedule.location_en') }}:</strong>
        <p>{{ $eventSchedule->location_en }}</p>
      </div>
    </div>

    <div class="row mb-3">
      <div class="col-md-4">
        <strong>{{ __('event_schedule.start_datetime') }}:</strong>
        <p>{{ $eventSchedule->start_datetime }}</p>
      </div>
      <div class="col-md-4">
        <strong>{{ __('event_schedule.end_datetime') }}:</strong>
        <p>{{ $eventSchedule->end_datetime }}</p>
      </div>
      <div class="col-md-4">
        <strong>{{ __('event_schedule.max_attendees') }}:</strong>
        <p>{{ $eventSchedule->max_attendees }}</p>
      </div>
    </div>

    <div class="row mb-3">
      <div class="col-md-6">
        <strong>{{ __('event_schedule.status') }}:</strong>
        <p>
          <span class="badge bg-{{ $eventSchedule->status ? 'success' : 'secondary' }}">
            {{ $eventSchedule->status ? __('event_schedule.active') : __('event_schedule.inactive') }}
          </span>
        </p>
      </div>
      <div class="col-md-6">
        <strong>{{ __('event_schedule.event') }}:</strong>
        <p>{{ app()->getLocale() == 'ar' ? $eventSchedule->event?->title_ar : $eventSchedule->event?->title_en }}</p>
      </div>
    </div>

    @if($eventSchedule->logo)
    <div class="mb-3">
      <strong>{{ __('event_schedule.logo') }}:</strong><br>
      <img src="{{ asset( $eventSchedule->logo) }}" width="120" height="120" class="mt-2 rounded border">
    </div>
    @endif

    @if($eventSchedule->speakers && $eventSchedule->speakers->count())
    <div class="mb-3">
      <strong>{{ __('event_schedule.speakers') }}:</strong>
      <ul class="list-unstyled ms-3 mt-2">
        @foreach($eventSchedule->speakers as $speaker)
          <li>🎤 {{ $speaker->name }}</li>
        @endforeach
      </ul>
    </div>
    @endif
  </div>
</div>
@endsection
