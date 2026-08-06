@php
  $isEdit = isset($event);

  // default: ON في create لو تحب (1). لو تحب default 0 غيّرها.
  $dateActiveValue = old('date_active', $isEdit ? (int)$event->date_active : 1);
@endphp

<div class="row g-3">
  <div class="col-md-6">
    <label class="form-label">{{ __('event.name_ar') }}</label>
    <input type="text" name="name_ar" class="form-control"
           value="{{ old('name_ar', $isEdit ? $event->name_ar : '') }}" required>
  </div>

  <div class="col-md-6">
    <label class="form-label">{{ __('event.name_en') }}</label>
    <input type="text" name="name_en" class="form-control"
           value="{{ old('name_en', $isEdit ? $event->name_en : '') }}" required>
  </div>

  <div class="col-md-6">
    <label class="form-label">{{ __('event.description_ar') }}</label>
    <textarea name="description_ar" class="form-control">{{ old('description_ar', $isEdit ? $event->description_ar : '') }}</textarea>
  </div>

  <div class="col-md-6">
    <label class="form-label">{{ __('event.description_en') }}</label>
    <textarea name="description_en" class="form-control">{{ old('description_en', $isEdit ? $event->description_en : '') }}</textarea>
  </div>

  <div class="col-md-6">
    <label class="form-label">{{ __('event.date') }}</label>
    <input type="datetime-local" name="event_date" class="form-control"
           value="{{ old('event_date', $isEdit ? $event->event_date : '') }}" required>
  </div>

  <div class="col-md-6">
    <label class="form-label">{{ __('event.attendees_limit') }}</label>
    <input type="number" name="attendees_limit" class="form-control"
           value="{{ old('attendees_limit', $isEdit ? $event->attendees_limit : '') }}">
  </div>

  <div class="col-md-6">
    <label class="form-label">{{ __('event.address_ar') }}</label>
    <input type="text" name="address_ar" class="form-control"
           value="{{ old('address_ar', $isEdit ? $event->address_ar : '') }}" required>
  </div>

  <div class="col-md-6">
    <label class="form-label">{{ __('event.address_en') }}</label>
    <input type="text" name="address_en" class="form-control"
           value="{{ old('address_en', $isEdit ? $event->address_en : '') }}" required>
  </div>

  <div class="col-md-6">
    <label class="form-label">{{ __('event.location') }}</label>
    <input type="text" name="location" class="form-control"
           value="{{ old('location', $isEdit ? $event->location : '') }}">
  </div>

  <div class="col-md-6">
    <label class="form-label">{{ __('event.image') }}</label>
    <input type="file" name="main_image" class="form-control">
    @if($isEdit && $event->main_image)
      <img src="{{ asset($event->main_image) }}" class="img-thumbnail mt-2" width="100" alt="event-image">
    @endif
  </div>

  <div class="col-md-6">
    <label class="form-label">{{ __('event.active') }}</label>
    <select name="active" class="form-select">
      <option value="1" {{ old('active', $isEdit ? $event->active : 1) == 1 ? 'selected' : '' }}>
        {{ __('event.active') }}
      </option>
      <option value="0" {{ old('active', $isEdit ? $event->active : 1) == 0 ? 'selected' : '' }}>
        {{ __('event.inactive') }}
      </option>
    </select>
  </div>

  {{-- ✅ date_active toggle 0/1 --}}
  <div class="col-md-6">
    <label class="form-label d-block">{{ __('event.date_active') }}</label>

    {{-- مهم: checkbox لو اتقفلت مش بيبعت قيمة، فبنضيف hidden = 0 --}}
    <input type="hidden" name="date_active" value="0">

    <div class="form-check form-switch">
      <input
        class="form-check-input"
        type="checkbox"
        role="switch"
        id="dateActiveSwitch"
        name="date_active"
        value="1"
        {{ (int)$dateActiveValue === 1 ? 'checked' : '' }}
      >
      <label class="form-check-label" for="dateActiveSwitch">
        {{ (int)$dateActiveValue === 1 ? __('event.enabled') : __('event.disabled') }}
      </label>
    </div>

    <small class="text-muted">
      {{ __('event.date_active_hint') }}
    </small>
  </div>
</div>

{{-- optional: update label text on toggle --}}
<script>
(function(){
  const sw = document.getElementById('dateActiveSwitch');
  if(!sw) return;

  const label = document.querySelector('label[for="dateActiveSwitch"]');
  const onText  = @json(__('event.enabled'));
  const offText = @json(__('event.disabled'));

  function sync(){
    if(label) label.textContent = sw.checked ? onText : offText;
  }

  sw.addEventListener('change', sync);
  sync();
})();
</script>
