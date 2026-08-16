@php $isEdit = isset($time) && $time; @endphp
<div class="row g-3">
  <div class="col-md-4">
    <label class="form-label">Speaker (type=1)</label>
    <select name="speaker_id" class="form-select" required>
      <option value="">Choose…</option>
      @foreach($speakers as $sp)
        <option value="{{ $sp->id }}" @selected(old('speaker_id', $time->speaker_id ?? null)==$sp->id)>
          {{ app()->getLocale()==='ar' ? ($sp->name_ar ?? $sp->id) : ($sp->name_en ?? $sp->id) }}
        </option>
      @endforeach
    </select>
  </div>
  <div class="col-md-3">
    <label class="form-label">Date</label>
    <input type="date" name="date" class="form-control" required
           value="{{ old('date', optional($time->date ?? null)->format('Y-m-d')) }}">
  </div>
  <div class="col-md-2">
    <label class="form-label">From</label>
    <input type="time" name="time_from" class="form-control" required
           value="{{ old('time_from', $time->time_from ?? '') }}">
  </div>
  <div class="col-md-2">
    <label class="form-label">To</label>
    <input type="time" name="time_to" class="form-control" required
           value="{{ old('time_to', $time->time_to ?? '') }}">
  </div>
  <div class="col-md-3">
    <label class="form-label">Status</label>
    <select name="status" class="form-select" required>
      @foreach(['available','booked','unavailable'] as $st)
        <option value="{{ $st }}" @selected(old('status', $time->status ?? 'available')==$st)>{{ ucfirst($st) }}</option>
      @endforeach
    </select>
  </div>
  <div class="col-md-2 d-flex align-items-end">
    <div class="form-check form-switch">
      <input class="form-check-input" type="checkbox" name="active" value="1" id="activeSwitch" {{ old('active', $time->active ?? true)?'checked':'' }}>
      <label class="form-check-label" for="activeSwitch">Active</label>
    </div>
  </div>
</div>
