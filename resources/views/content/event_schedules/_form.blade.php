@csrf

<div class="row mb-3">


<div class="row mb-3">
  <div class="col-md-6">
    <label for="event_id" class="form-label">{{ __('event_schedule.event') }}</label>
    <select name="event_id" id="event_id" class="form-select" required>
      <option value="">-- {{ __('event_schedule.select_event') }} --</option>
      @foreach($events as $event)
        <option value="{{ $event->id }}" {{ (old('event_id', $eventSchedule->event_id ?? '') == $event->id) ? 'selected' : '' }}>
          {{ app()->getLocale() == 'ar' ? $event->name_ar : $event->name_en }}
        </option>
      @endforeach
    </select>
  </div>
  <div class="col-md-6">
    <label for="title_ar" class="form-label">{{ __('event_schedule.name_ar') }}</label>
    <input type="text" class="form-control" name="title_ar" id="title_ar" value="{{ old('title_ar', $eventSchedule->title_ar ?? '') }}" required>
  </div>
</div>

<div class="row mb-3">
  <div class="col-md-6">
    <label for="title_en" class="form-label">{{ __('event_schedule.name_en') }}</label>
    <input type="text" class="form-control" name="title_en" id="title_en" value="{{ old('title_en', $eventSchedule->title_en ?? '') }}" required>
  </div>
  <div class="col-md-6">
    <label for="max_attendees" class="form-label">{{ __('event_schedule.max_attendees') }}</label>
    <input type="number" class="form-control" name="max_attendees" id="max_attendees" value="{{ old('max_attendees', $eventSchedule->max_attendees ?? '') }}">
  </div>
</div>

<div class="row mb-3">
  <div class="col-md-6">
    <label for="location_ar" class="form-label">{{ __('event_schedule.location_ar') }}</label>
    <input type="text" class="form-control" name="location_ar" id="location_ar" value="{{ old('location_ar', $eventSchedule->location_ar ?? '') }}">
  </div>
  <div class="col-md-6">
    <label for="location_en" class="form-label">{{ __('event_schedule.location_en') }}</label>
    <input type="text" class="form-control" name="location_en" id="location_en" value="{{ old('location_en', $eventSchedule->location_en ?? '') }}">
  </div>
</div>

<div class="row mb-3">
  <div class="col-md-6">
    <label for="description_ar" class="form-label">{{ __('event_schedule.description_ar') }}</label>
    <textarea class="form-control" name="description_ar" id="description_ar">{{ old('description_ar', $eventSchedule->description_ar ?? '') }}</textarea>
  </div>
  <div class="col-md-6">
    <label for="description_en" class="form-label">{{ __('event_schedule.description_en') }}</label>
    <textarea class="form-control" name="description_en" id="description_en">{{ old('description_en', $eventSchedule->description_en ?? '') }}</textarea>
  </div>
</div>

<div class="row mb-3">

  <div class="col-md-4">
    <label for="start_datetime" class="form-label">{{ __('event_schedule.start_time') }}</label>
    <input type="datetime-local" class="form-control" name="start_datetime" id="start_datetime" value="{{ old('start_datetime', $eventSchedule->start_datetime ?? '') }}" required>
  </div>
  <div class="col-md-4">
    <label for="end_datetime" class="form-label">{{ __('event_schedule.end_time') }}</label>
    <input type="datetime-local" class="form-control" name="end_datetime" id="end_datetime" value="{{ old('end_datetime', $eventSchedule->end_datetime ?? '') }}" required>
  </div>
</div>

<div class="row mb-3">
  <div class="col-md-6">
    <label for="logo" class="form-label">{{ __('event_schedule.logo') }}</label>
    <input type="file" class="form-control" name="logo" id="logo">
    @if(isset($eventSchedule) && $eventSchedule->logo)
      <img src="{{ asset($eventSchedule->logo) }}" class="mt-2" width="80" height="80">
    @endif
  </div>
  <div class="col-md-6">
    <label for="active" class="form-label">{{ __('event_schedule.status') }}</label>
    <select name="active" id="active" class="form-select">
      <option value="1" {{ old('active', $eventSchedule->active ?? 1) == 1 ? 'selected' : '' }}>{{ __('event_schedule.active') }}</option>
      <option value="0" {{ old('active', $eventSchedule->active ?? 1) == 0 ? 'selected' : '' }}>{{ __('event_schedule.inactive') }}</option>
    </select>
  </div>
</div>

<div class="row mb-3">
  <div class="col-md-6">
    <label for="status" class="form-label">{{ __('event_schedule.status_event') }}</label>
    <select name="status" id="status" class="form-select" required>
      <option value="upcoming" {{ old('status', $eventSchedule->status ?? 'upcoming') == 'upcoming' ? 'selected' : '' }}>{{ __('event_schedule.status_upcoming') }}</option>
      <option value="ongoing" {{ old('status', $eventSchedule->status ?? '') == 'ongoing' ? 'selected' : '' }}>{{ __('event_schedule.status_ongoing') }}</option>
      <option value="completed" {{ old('status', $eventSchedule->status ?? '') == 'completed' ? 'selected' : '' }}>{{ __('event_schedule.status_completed') }}</option>
      <option value="canceled" {{ old('status', $eventSchedule->status ?? '') == 'canceled' ? 'selected' : '' }}>{{ __('event_schedule.status_canceled') }}</option>
    </select>
  </div>
</div>
