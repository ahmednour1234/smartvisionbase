@extends('layouts.layoutMaster')

@section('title', (app()->getLocale()==='ar' ? 'إضافة موعد للمتحدث' : 'Create Speaker Time'))

@section('content')
@php
  $isAr = app()->getLocale()==='ar';
  $t = fn($ar,$en)=> $isAr ? $ar : $en;

  // Helper للتأكد من وجود اسم روت
  function route_exists($name){
    try { return \Illuminate\Support\Facades\Route::has($name); }
    catch (\Throwable $e) { return false; }
  }

  $storeUrl = route_exists('dashboard.speakers.times.store')
      ? route('dashboard.speakers.times.store', $speaker)
      : (route_exists('admin.speakers.times.store')
          ? route('admin.speakers.times.store', $speaker)
          : '#');

  $backUrl = route_exists('dashboard.speakers.schedule')
      ? route('dashboard.speakers.schedule', $speaker)
      : (route_exists('admin.speakers.schedule')
          ? route('admin.speakers.schedule', $speaker)
          : (route_exists('admin.speakers.index') ? route('admin.speakers.index') : '#'));
@endphp

<style>
  .hero {
    position: relative;
    border-radius: 16px;
    overflow: hidden;
    background: linear-gradient(135deg, #4f46e5, #06b6d4);
    color: #fff;
    padding: 22px 24px;
    margin-bottom: 18px;
  }
  .hero .avatar {
    width: 72px; height: 72px; border-radius: 50%;
    border: 3px solid rgba(255,255,255,.7); object-fit: cover;
  }
  .hero .name { font-weight: 700; font-size: 1.15rem; }
  .soft-card {
    border: 1px solid #eef2f7; border-radius: 14px;
    box-shadow: 0 12px 30px rgba(16,24,40,.04);
  }
  .section-title {
    font-weight: 700; display: flex; align-items:center; gap:10px; margin-bottom: 12px;
  }
  .pill {
    display:inline-block; padding: 4px 10px; border-radius: 999px; background: #f1f5f9; font-size: .825rem; color:#0f172a;
  }
  .hint { color:#64748b; font-size:.9rem }
  .form-help { font-size:.8rem; color:#6b7280 }
  .divider { height:1px; background:#f1f5f9; margin:12px 0 18px }
</style>

<div class="hero">
  <div class="d-flex align-items-center gap-3">
    <div>
      @if($speaker->image)
        <img class="avatar" src="{{ asset('public/'.$speaker->image) }}" alt="">
      @else
        <img class="avatar" src="https://via.placeholder.com/72" alt="">
      @endif
    </div>
    <div>
      <div class="name">{{ $isAr ? ($speaker->name_ar ?? 'المتحدث') : ($speaker->name_en ?? 'Speaker') }}</div>
      <div class="hint">
        {{ $isAr ? ($speaker->title_ar ?? '') : ($speaker->title_en ?? '') }}
        @if($speaker->company_name_ar || $speaker->company_name_en)
          • {{ $isAr ? ($speaker->company_name_ar ?? '') : ($speaker->company_name_en ?? '') }}
        @endif
      </div>
      <span class="pill">{{ $t('إضافة موعد جديد','Create New Time Slot') }}</span>
    </div>
    <div class="ms-auto">
      <a href="{{ $backUrl }}" class="btn btn-light text-primary">
        <i class="fas fa-arrow-left me-1"></i> {{ $t('رجوع','Back') }}
      </a>
    </div>
  </div>
</div>

@if($errors->any())
  <div class="alert alert-danger soft-card p-3">
    <i class="fas fa-exclamation-triangle me-1"></i> {{ $errors->first() }}
  </div>
@endif

<form method="post" action="{{ $storeUrl }}" class="soft-card p-3 p-md-4">
  @csrf

  <div class="section-title">
    <i class="far fa-clock"></i> {{ $t('بيانات الموعد','Time Details') }}
  </div>

  @includeIf('content.speakers.times._form')

  <div class="divider"></div>

  <div class="d-flex gap-2">
    <button type="submit" class="btn btn-primary">
      <i class="fas fa-save me-1"></i> {{ $t('حفظ','Save') }}
    </button>
    <a href="{{ $backUrl }}" class="btn btn-outline-secondary">
      {{ $t('إلغاء','Cancel') }}
    </a>
  </div>
</form>

{{-- تحسين تجربة المستخدم: حساب "إلى" تلقائيًا + مدد سريعة --}}
<script>
  (function(){
    const fromInput = document.querySelector('input[name="time_from"]');
    const toInput   = document.querySelector('input[name="time_to"]');
    const dateInput = document.querySelector('input[name="date"]');
    const duration  = document.getElementById('duration');

    function pad(n){ return String(n).padStart(2,'0'); }

    function addMinutesToTime(timeHHmm, minutes){
      if(!timeHHmm) return '';
      const [hh,mm] = timeHHmm.split(':').map(Number);
      const d = new Date(2000,0,1, hh, mm, 0);
      d.setMinutes(d.getMinutes() + minutes);
      return pad(d.getHours())+':'+pad(d.getMinutes());
    }

    function updateTo(){
      if(fromInput && toInput && duration){
        const mins = parseInt(duration.value || '30', 10);
        toInput.value = addMinutesToTime(fromInput.value, mins);
      }
    }

    if(fromInput){ fromInput.addEventListener('change', updateTo); }
    if(duration){ duration.addEventListener('change', updateTo); }
  })();
</script>
@endsection
