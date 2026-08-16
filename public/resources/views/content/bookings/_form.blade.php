@php
  $isEdit = isset($booking) && $booking;
@endphp

<div class="row g-3">
  <div class="col-md-4">
    <label class="form-label">Speaker (type=1)</label>
    <select name="speaker_id" class="form-select" required>
      <option value="">Choose…</option>
      @foreach($speakers as $sp)
        <option value="{{ $sp->id }}" @selected(old('speaker_id', $booking->speaker_id ?? request('speaker_id'))==$sp->id)>
          {{ app()->getLocale()==='ar' ? ($sp->name_ar ?? $sp->id) : ($sp->name_en ?? $sp->id) }}
        </option>
      @endforeach
    </select>
  </div>

  <div class="col-md-4">
    <label class="form-label">Client</label>
    <select name="client_id" class="form-select" required>
      <option value="">Choose…</option>
      @foreach($clients as $c)
        <option value="{{ $c->id }}" @selected(old('client_id', $booking->client_id ?? null)==$c->id)>{{ $c->name ?? $c->id }}</option>
      @endforeach
    </select>
  </div>

  <div class="col-md-4">
    <label class="form-label">Speaker Time (optional)</label>
    <select name="speaker_time_id" class="form-select">
      <option value="">—</option>
      @foreach($speakerTimes as $st)
        <option value="{{ $st->id }}" @selected(old('speaker_time_id', $booking->speaker_time_id ?? null)==$st->id)>
          #{{ $st->id }} — {{ optional($st->date)->format('Y-m-d') ?? $st->date }} {{ $st->time_from }}→{{ $st->time_to }}
        </option>
      @endforeach
    </select>
    @if(!count($speakerTimes))
      <small class="text-muted">اختر متحدثًا أولًا من الأعلى ثم اضغط Enter لإعادة تحميل القائمة.</small>
    @endif
  </div>

  <div class="col-md-3">
    <label class="form-label">From (datetime)</label>
    <input type="datetime-local" name="time_from" class="form-control" required
           value="{{ old('time_from', isset($booking->time_from)?$booking->time_from->format('Y-m-d\TH:i'):'' ) }}">
  </div>

  <div class="col-md-3">
    <label class="form-label">To (datetime)</label>
    <input type="datetime-local" name="time_to" class="form-control" required
           value="{{ old('time_to', isset($booking->time_to)?$booking->time_to->format('Y-m-d\TH:i'):'' ) }}">
  </div>

  <div class="col-md-3">
    <label class="form-label">Status</label>
    <select name="status" class="form-select" required>
      @foreach(['pending','confirmed','cancelled'] as $st)
        <option value="{{ $st }}" @selected(old('status', $booking->status ?? 'pending')==$st)>{{ ucfirst($st) }}</option>
      @endforeach
    </select>
  </div>

  <div class="col-md-3 d-flex align-items-end">
    <div class="form-check form-switch">
      <input class="form-check-input" type="checkbox" name="active" value="1" id="activeSwitch" {{ old('active', $booking->active ?? true)?'checked':'' }}>
      <label class="form-check-label" for="activeSwitch">Active</label>
    </div>
  </div>
</div>
