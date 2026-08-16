@extends('layouts.layoutMaster')

@section('title', (app()->getLocale()==='ar' ? 'جدول المتحدث: ' : 'Speaker Schedule: ') . (app()->getLocale()==='ar' ? ($speaker->name_ar ?? $speaker->id) : ($speaker->name_en ?? $speaker->id)))

@section('content')
@php
  $isAr = app()->getLocale()==='ar';
  $t = fn($ar,$en)=> $isAr ? $ar : $en;

  // قراءة التبويب النشط من الـ query (times | bookings)
  $activeTab = request('tab', 'times');

  // Helpers صغيرة
  function route_exists($name){
      try { return \Illuminate\Support\Facades\Route::has($name); }
      catch(\Throwable $e){ return false; }
  }

  $createTimeUrl = route_exists('dashboard.speakers.times.create')
      ? route('dashboard.speakers.times.create', $speaker)
      : (route_exists('admin.speakers.times.create') ? route('admin.speakers.times.create', $speaker) : '#');

  $editTime = fn($time)=>
      route_exists('dashboard.speakers.times.edit') ? route('dashboard.speakers.times.edit',$time)
      : (route_exists('admin.speakers.times.edit') ? route('admin.speakers.times.edit',$time) : '#');

  $toggleTime = fn($time)=>
      route_exists('dashboard.speakers.times.toggle') ? route('dashboard.speakers.times.toggle',$time)
      : (route_exists('admin.speakers.times.toggle') ? route('admin.speakers.times.toggle',$time) : '#');

  $deleteTime = fn($time)=>
      route_exists('dashboard.speakers.times.destroy') ? route('dashboard.speakers.times.destroy',$time)
      : (route_exists('admin.speakers.times.destroy') ? route('admin.speakers.times.destroy',$time) : '#');

  $backToSpeakers = route_exists('admin.speakers.index',[$speaker->type]) ? route('admin.speakers.index',[$speaker->type]) : '#';

  // فلاتر المواعيد (يُفضَّل أن تكون وصلت من الكنترولر)
  $filters = $filters ?? ['date' => request('date'), 'status' => request('status')];
@endphp

<style>
  .hero {
    position: relative; border-radius: 16px; overflow: hidden;
    background: linear-gradient(135deg, #4f46e5, #06b6d4);
    color: #fff; padding: 18px 20px; margin-bottom: 18px;
    box-shadow: 0 12px 32px rgba(2,6,23,.15);
  }
  .hero .avatar { width: 72px; height: 72px; border-radius: 50%; object-fit: cover; border: 3px solid rgba(255,255,255,.7); }
  .hero .name { font-weight: 800; font-size: 1.15rem; letter-spacing:.2px }
  .hero .meta { opacity: .95; }
  .soft-card { border: 1px solid #eef2f7; border-radius: 14px; box-shadow: 0 10px 28px rgba(16,24,40,.04); }
  .stat { display:flex; gap:12px; align-items:center; padding:10px 12px; border-radius:12px; background:#f8fafc; }
  .pill { display:inline-block; padding:4px 10px; border-radius:999px; background:#f1f5f9; font-size:.82rem; color:#0f172a; }
  .divider { height:1px; background:#f1f5f9; margin:10px 0 18px }
  .nav-tabs .nav-link { font-weight:600 }
</style>

{{-- Hero --}}
<div class="hero">
  <div class="d-flex align-items-center gap-3">
    <div>
      @if($speaker->image)
        <img class="avatar" src="{{ asset('public/'. $speaker->image) }}" alt="">
      @else
        <img class="avatar" src="https://via.placeholder.com/72" alt="">
      @endif
    </div>
    <div>
      <div class="name">
        {{ $isAr ? ($speaker->name_ar ?? 'متحدث') : ($speaker->name_en ?? 'Speaker') }}
      </div>
      <div class="meta">
        {{ $isAr ? ($speaker->title_ar ?? '') : ($speaker->title_en ?? '') }}
        @if($speaker->company_name_ar || $speaker->company_name_en)
          • {{ $isAr ? ($speaker->company_name_ar ?? '') : ($speaker->company_name_en ?? '') }}
        @endif
      </div>
      <div class="mt-1"><span class="pill">{{ $t('جدول المتحدث','Speaker Schedule') }}</span></div>
    </div>
    <div class="ms-auto d-flex gap-2">
      <a href="{{ $createTimeUrl }}" class="btn btn-light text-primary">
        <i class="fas fa-plus me-1"></i> {{ $t('إضافة موعد','Create Time') }}
      </a>
      <a href="{{ $backToSpeakers }}" class="btn btn-outline-light">
        <i class="fas fa-arrow-left me-1"></i> {{ $t('رجوع','Back') }}
      </a>
    </div>
  </div>
</div>

@if(session('success'))
  <div class="alert alert-success soft-card p-3"><i class="far fa-check-circle me-1"></i> {{ session('success') }}</div>
@endif
@if($errors->any())
  <div class="alert alert-danger soft-card p-3"><i class="fas fa-exclamation-triangle me-1"></i> {{ $errors->first() }}</div>
@endif

{{-- Tabs --}}
<ul class="nav nav-tabs" role="tablist">
  <li class="nav-item">
    <a class="nav-link {{ $activeTab==='times' ? 'active' : '' }}"
       href="{{ request()->fullUrlWithQuery(['tab'=>'times']) }}" role="tab">
      {{ $t('المواعيد','Times') }}
    </a>
  </li>
  <li class="nav-item">
    <a class="nav-link {{ $activeTab==='bookings' ? 'active' : '' }}"
       href="{{ request()->fullUrlWithQuery(['tab'=>'bookings']) }}" role="tab">
      {{ $t('الحجوزات','Bookings') }}
    </a>
  </li>
</ul>

<div class="tab-content pt-3">
  {{-- TAB: Times --}}
  <div class="tab-pane fade {{ $activeTab==='times' ? 'show active' : '' }}" id="tab-times">
    {{-- Stats + Filters --}}
    <div class="row g-3 mb-3">
      <div class="col-md-8">
        <div class="soft-card p-3">
          <div class="row g-3">
            <div class="col-sm-4">
              <div class="stat">
                <i class="far fa-calendar-check"></i>
                <div>
                  <div class="text-muted small">{{ $t('مواعيد في الصفحة','Times on page') }}</div>
                  <div class="fw-bold">{{ ($times instanceof \Illuminate\Pagination\LengthAwarePaginator) ? $times->count() : count($times ?? []) }}</div>
                </div>
              </div>
            </div>
            <div class="col-sm-4">
              <div class="stat">
                <i class="far fa-clock"></i>
                <div>
                  <div class="text-muted small">{{ $t('اليوم المُصفّى','Filtered day') }}</div>
                  <div class="fw-bold">{{ $filters['date'] ?: $t('غير محدد','Not set') }}</div>
                </div>
              </div>
            </div>
            <div class="col-sm-4">
              <div class="stat">
                <i class="far fa-flag"></i>
                <div>
                  <div class="text-muted small">{{ $t('الحالة','Status') }}</div>
                  <div class="fw-bold">{{ $filters['status'] ?: $t('الكل','All') }}</div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      {{-- Filters --}}
      <div class="col-md-4">
        <form class="soft-card p-3" method="get" action="">
          <input type="hidden" name="tab" value="times">
          <div class="mb-2">
            <label class="form-label">{{ $t('التاريخ','Date') }}</label>
            <input type="date" name="date" value="{{ $filters['date'] }}" class="form-control">
          </div>
          <div class="mb-3">
            <label class="form-label">{{ $t('الحالة','Status') }}</label>
            <select name="status" class="form-select">
              <option value="">{{ $t('الكل','All') }}</option>
              <option value="available"  @selected($filters['status']==='available')>{{ $t('متاح','Available') }}</option>
              <option value="booked"     @selected($filters['status']==='booked')>{{ $t('محجوز','Booked') }}</option>
              <option value="unavailable"@selected($filters['status']==='unavailable')>{{ $t('غير متاح','Unavailable') }}</option>
            </select>
          </div>
          <div class="d-flex gap-2">
            <button class="btn btn-primary w-100"><i class="fas fa-filter me-1"></i> {{ $t('تصفية','Filter') }}</button>
            <a href="{{ route_exists('dashboard.speakers.schedule') ? route('dashboard.speakers.schedule',$speaker) : request()->url() }}"
               class="btn btn-light w-100">{{ $t('إعادة ضبط','Reset') }}</a>
          </div>
        </form>
      </div>
    </div>

    {{-- Times Table --}}
    <div class="soft-card p-0">
      <div class="d-flex justify-content-between align-items-center px-3 py-2 border-bottom">
        <h6 class="mb-0">{{ $t('قائمة المواعيد','Time Slots') }}</h6>
        <a href="{{ $createTimeUrl }}" class="btn btn-sm btn-primary"><i class="fas fa-plus me-1"></i>{{ $t('إضافة موعد','Add Time') }}</a>
      </div>

      <div class="table-responsive">
        <table class="table table-striped align-middle mb-0">
          <thead>
            <tr>
              <th>{{ $t('التاريخ','Date') }}</th>
              <th>{{ $t('من','From') }}</th>
              <th>{{ $t('إلى','To') }}</th>
              <th>{{ $t('الحالة','Status') }}</th>
              <th>{{ $t('مفعّل؟','Active?') }}</th>
              <th>{{ $t('حجوزات مؤكدة','Confirmed bookings') }}</th>
              <th class="text-end">{{ $t('إجراءات','Actions') }}</th>
            </tr>
          </thead>
          <tbody>
          @forelse(($times ?? []) as $tRow)
            <tr>
              <td>{{ optional($tRow->date)->format('Y-m-d') ?? $tRow->date }}</td>
              <td>{{ $tRow->time_from }}</td>
              <td>{{ $tRow->time_to }}</td>
              <td>
                @php $st = $tRow->status; @endphp
                @if($st==='available')
                  <span class="badge bg-success">{{ $t('متاح','Available') }}</span>
                @elseif($st==='booked')
                  <span class="badge bg-primary">{{ $t('محجوز','Booked') }}</span>
                @else
                  <span class="badge bg-secondary">{{ $t('غير متاح','Unavailable') }}</span>
                @endif
              </td>
              <td>{!! $tRow->active ? '<span class="badge bg-success">'.$t('نعم','Yes').'</span>' : '<span class="badge bg-danger">'.$t('لا','No').'</span>' !!}</td>
              <td>{{ $tRow->confirmed_bookings_count ?? 0 }}</td>
              <td class="text-end">
                @php
                  $editUrl   = $editTime($tRow);
                  $toggleUrl = $toggleTime($tRow);
                  $delUrl    = $deleteTime($tRow);
                @endphp

                <a href="{{ $editUrl }}" class="btn btn-sm btn-outline-primary">{{ $t('تعديل','Edit') }}</a>

                @if($toggleUrl !== '#')
                <form action="{{ $toggleUrl }}" method="post" class="d-inline">
                  @csrf @method('PATCH')
                  <button class="btn btn-sm btn-outline-warning">
                    {{ $tRow->active ? $t('تعطيل','Disable') : $t('تفعيل','Enable') }}
                  </button>
                </form>
                @endif

                @if($delUrl !== '#')
                <form action="{{ $delUrl }}" method="post" class="d-inline" onsubmit="return confirm('{{ $t('حذف الموعد؟','Delete this time?') }}');">
                  @csrf @method('DELETE')
                  <button class="btn btn-sm btn-outline-danger">{{ $t('حذف','Delete') }}</button>
                </form>
                @endif
              </td>
            </tr>
          @empty
            <tr><td colspan="7" class="text-center text-muted py-4">{{ $t('لا توجد مواعيد','No times yet') }}</td></tr>
          @endforelse
          </tbody>
        </table>
      </div>
    </div>

    {{-- Pagination --}}
    @if(method_exists(($times ?? null), 'links'))
      <div class="mt-2">
        {{ $times->appends(['tab'=>'times','date'=>$filters['date'],'status'=>$filters['status']])->links() }}
      </div>
    @endif
  </div>

  {{-- TAB: Bookings (عرض فقط) --}}
  <div class="tab-pane fade {{ $activeTab==='bookings' ? 'show active' : '' }}" id="tab-bookings">
    <div class="soft-card p-0">
      <div class="d-flex justify-content-between align-items-center px-3 py-2 border-bottom">
        <h6 class="mb-0">{{ $t('الحجوزات (عرض فقط)','Bookings (read-only)') }}</h6>
        <span class="text-muted small px-2">{{ $t('الداشبورد لا ينشئ حجوزات – للمتابعة فقط','Dashboard does not create bookings – read-only') }}</span>
      </div>

      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead>
            <tr>
              <th>{{ $t('العميل','Client') }}</th>
              <th>{{ $t('من','From') }}</th>
              <th>{{ $t('إلى','To') }}</th>
              <th>{{ $t('الحالة','Status') }}</th>
              <th>{{ $t('فتحة زمنية','Slot') }}</th>
            </tr>
          </thead>
          <tbody>
          @forelse(($bookings ?? []) as $bk)
            <tr>
              <td>{{ optional($bk->client)->name ?? ('#'.$bk->client_id) }}</td>
              <td>
                @if($bk->time_from instanceof \Illuminate\Support\Carbon)
                  {{ $bk->time_from->format('Y-m-d H:i') }}
                @else
                  {{ \Illuminate\Support\Str::limit($bk->time_from, 16, '') }}
                @endif
              </td>
              <td>
                @if($bk->time_to instanceof \Illuminate\Support\Carbon)
                  {{ $bk->time_to->format('Y-m-d H:i') }}
                @else
                  {{ \Illuminate\Support\Str::limit($bk->time_to, 16, '') }}
                @endif
              </td>
              <td>
                @php $bs = $bk->status; @endphp
                @if($bs==='confirmed')
                  <span class="badge bg-success">{{ $t('مؤكد','Confirmed') }}</span>
                @elseif($bs==='pending')
                  <span class="badge bg-warning text-dark">{{ $t('معلّق','Pending') }}</span>
                @else
                  <span class="badge bg-secondary">{{ $t('ملغي','Cancelled') }}</span>
                @endif
              </td>
              <td>{{ $bk->speaker_time_id ? '#'.$bk->speaker_time_id : $t('لا يوجد','—') }}</td>
            </tr>
          @empty
            <tr><td colspan="5" class="text-center text-muted py-4">{{ $t('لا توجد حجوزات','No bookings') }}</td></tr>
          @endforelse
          </tbody>
        </table>
      </div>
    </div>

    {{-- Pagination --}}
    @if(method_exists(($bookings ?? null), 'links'))
      <div class="mt-2">
        {{ $bookings->appends(['tab'=>'bookings'])->links() }}
      </div>
    @endif
  </div>
</div>
@endsection
