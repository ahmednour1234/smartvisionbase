@php
  $isAr = app()->getLocale()==='ar';
  $t = fn($ar,$en)=> $isAr ? $ar : $en;
@endphp

<div class="row g-3">
  <div class="col-md-4">
    <label class="form-label">{{ $t('التاريخ','Date') }}</label>
    <input type="date" name="date" class="form-control" required
           value="{{ old('date', isset($time) && optional($time->date)->format ? $time->date->format('Y-m-d') : (isset($time->date) ? $time->date : '')) }}">
    <div class="form-help">{{ $t('اختر يوم الموعد','Pick the appointment day') }}</div>
  </div>

  <div class="col-md-4">
    <label class="form-label">{{ $t('من','From') }}</label>
    <div class="input-group">
      <span class="input-group-text"><i class="far fa-clock"></i></span>
      <input type="time" name="time_from" class="form-control" required
             value="{{ old('time_from', $time->time_from ?? '') }}">
    </div>
    <div class="form-help">{{ $t('وقت بداية الموعد (ساعة:دقيقة)','Start time (HH:MM)') }}</div>
  </div>

  <div class="col-md-4">
    <label class="form-label d-flex justify-content-between">
      <span>{{ $t('إلى','To') }}</span>
      <span class="text-muted" style="font-weight:normal">{{ $t('المدة','Duration') }}</span>
    </label>
    <div class="input-group">
      <span class="input-group-text"><i class="far fa-clock"></i></span>
      <input type="time" name="time_to" class="form-control" required
             value="{{ old('time_to', $time->time_to ?? '') }}">
      <select id="duration" class="form-select" style="max-width: 140px">
        <option value="15">15 {{ $t('دقيقة','min') }}</option>
        <option value="30" selected>30 {{ $t('دقيقة','min') }}</option>
        <option value="45">45 {{ $t('دقيقة','min') }}</option>
        <option value="60">60 {{ $t('دقيقة','min') }}</option>
      </select>
    </div>
    <div class="form-help">{{ $t('يمكنك اختيار مدة وسيتم حساب وقت الانتهاء تلقائيًا','Pick a duration to auto-calc end time') }}</div>
  </div>

  <div class="col-md-4">
    <label class="form-label">{{ $t('الحالة','Status') }}</label>
    <select name="status" class="form-select">
      @foreach(['available'=>$t('متاح','Available'),'booked'=>$t('محجوز','Booked'),'unavailable'=>$t('غير متاح','Unavailable')] as $k=>$v)
        <option value="{{ $k }}" @selected(old('status', $time->status ?? 'available')==$k)>{{ $v }}</option>
      @endforeach
    </select>
    <div class="form-help">{{ $t('اضبط حالة الفتحة الزمنية','Set the time slot status') }}</div>
  </div>

  <div class="col-md-4 d-flex align-items-end">
    <div class="form-check form-switch">
      <input class="form-check-input" type="checkbox" name="active" value="1"
             id="activeSwitch"
             {{ old('active', $time->active ?? true) ? 'checked' : '' }}>
      <label class="form-check-label" for="activeSwitch">{{ $t('مُفعّل','Active') }}</label>
    </div>
  </div>
</div>
