@extends('layouts.layoutMaster')

@section('title', app()->getLocale() === 'ar' ? $speaker->name_ar : $speaker->name_en)

@section('content')
@php
  $isAr   = app()->getLocale() === 'ar';
  $t = fn($ar,$en)=> $isAr ? $ar : $en;

  // نحاول تحديد مسار زر إنشاء موعد حسب أسماء الروت عندك
  $createTimeUrl = route_exists('dashboard.speakers.times.create')
      ? route('dashboard.speakers.times.create', $speaker)
      : (route_exists('admin.speakers.times.create')
          ? route('admin.speakers.times.create', $speaker)
          : '#');

  // Helper صغير للتأكد من وجود اسم روت
  function route_exists($name){
      try { return \Illuminate\Support\Facades\Route::has($name); }
      catch(\Throwable $e){ return false; }
  }
@endphp

<div class="card shadow-sm">
  <div class="card-body">

    {{-- Tabs header --}}
    <ul class="nav nav-tabs" id="speakerTabs" role="tablist">
      <li class="nav-item" role="presentation">
        <a class="nav-link {{ ($activeTab ?? 'other') === 'other' ? 'active' : '' }}"
           href="{{ request()->fullUrlWithQuery(['tab'=>'other']) }}" role="tab">
           {{ $t('تفاصيل','Other') }}
        </a>
      </li>
      <li class="nav-item" role="presentation">
        <a class="nav-link {{ ($activeTab ?? '') === 'times' ? 'active' : '' }}"
           href="{{ request()->fullUrlWithQuery(['tab'=>'times']) }}" role="tab">
           {{ $t('المواعيد','Times') }}
        </a>
      </li>
      <li class="nav-item" role="presentation">
        <a class="nav-link {{ ($activeTab ?? '') === 'bookings' ? 'active' : '' }}"
           href="{{ request()->fullUrlWithQuery(['tab'=>'bookings']) }}" role="tab">
           {{ $t('الحجوزات','Bookings') }}
        </a>
      </li>
    </ul>

    <div class="tab-content pt-4">
      {{-- Tab: Other (تفاصيل السبيكر كما هي) --}}
      <div class="tab-pane fade {{ ($activeTab ?? 'other') === 'other' ? 'show active' : '' }}" id="tab-other">
        <div class="row g-4 align-items-center">
          <div class="col-md-4 text-center">
            @if($speaker->image)
              <img src="{{ asset('public/'. $speaker->image) }}" alt="" class="img-thumbnail rounded-circle" width="200">
            @else
              <img src="https://via.placeholder.com/200" class="img-thumbnail rounded-circle" alt="No Image">
            @endif
            <h4 class="mt-3">{{ $isAr ? $speaker->name_ar : $speaker->name_en }}</h4>
            <p class="text-muted">{{ $isAr ? $speaker->title_ar : $speaker->title_en }}</p>
            @if($speaker->linkedin)
              <a href="{{ $speaker->linkedin }}" class="btn btn-sm btn-outline-primary" target="_blank">
                <i class="fab fa-linkedin"></i> LinkedIn
              </a>
            @endif
          </div>

          <div class="col-md-8">
            <h5 class="mb-3">{{ __('speaker.details') }}</h5>
            <ul class="list-group list-group-flush">
              <li class="list-group-item">
                <strong>{{ __('speaker.name') }}:</strong>
                {{ $isAr ? $speaker->name_ar : $speaker->name_en }}
              </li>
              <li class="list-group-item">
                <strong>{{ __('speaker.title') }}:</strong>
                {{ $isAr ? $speaker->title_ar : $speaker->title_en }}
              </li>
              <li class="list-group-item">
                <strong>{{ __('speaker.company') }}:</strong>
                {{ $isAr ? $speaker->company_name_ar : $speaker->company_name_en }}
              </li>
              <li class="list-group-item">
                <strong>{{ __('speaker.social_links') }}:</strong>
                <p class="mb-0">{!! nl2br(e($speaker->social_links)) !!}</p>
              </li>
            </ul>
            <a href="{{ route('admin.speakers.index' ,[$type]) }}" class="btn btn-outline-secondary mt-4">
              <i class="fas fa-arrow-left me-1"></i> {{ __('general.back') }}
            </a>
          </div>
        </div>
      </div>

      {{-- Tab: Times (مواعيد السبيكر + زر إنشاء) --}}
      <div class="tab-pane fade {{ ($activeTab ?? '') === 'times' ? 'show active' : '' }}" id="tab-times">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <h5 class="mb-0">{{ $t('مواعيد المتحدث','Speaker Times') }}</h5>
          <a href="{{ $createTimeUrl }}" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i> {{ $t('إضافة موعد','Create Time') }}
          </a>
        </div>

        <div class="table-responsive">
          <table class="table table-striped align-middle">
            <thead>
              <tr>
                <th>{{ $t('التاريخ','Date') }}</th>
                <th>{{ $t('من','From') }}</th>
                <th>{{ $t('إلى','To') }}</th>
                <th>{{ $t('الحالة','Status') }}</th>
                <th>{{ $t('مفعل؟','Active?') }}</th>
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
                <td class="text-end">
                  @php
                    $editUrl   = route_exists('dashboard.speakers.times.edit') ? route('dashboard.speakers.times.edit',$tRow) : (route_exists('admin.speakers.times.edit') ? route('admin.speakers.times.edit',$tRow) : '#');
                    $toggleUrl = route_exists('dashboard.speakers.times.toggle') ? route('dashboard.speakers.times.toggle',$tRow) : (route_exists('admin.speakers.times.toggle') ? route('admin.speakers.times.toggle',$tRow) : '#');
                    $delUrl    = route_exists('dashboard.speakers.times.destroy') ? route('dashboard.speakers.times.destroy',$tRow) : (route_exists('admin.speakers.times.destroy') ? route('admin.speakers.times.destroy',$tRow) : '#');
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
              <tr><td colspan="6" class="text-center text-muted">{{ $t('لا توجد مواعيد','No times yet') }}</td></tr>
            @endforelse
            </tbody>
          </table>
        </div>

        {{-- Pagination للمواعيد --}}
        @if(method_exists(($times ?? null), 'links'))
          <div class="mt-2">
            {{ $times->appends(['tab'=>'times'])->links() }}
          </div>
        @endif
      </div>

      {{-- Tab: Bookings (متابعة فقط) --}}
      <div class="tab-pane fade {{ ($activeTab ?? '') === 'bookings' ? 'show active' : '' }}" id="tab-bookings">
        <h5 class="mb-3">{{ $t('حجوزات المتحدث','Speaker Bookings') }}</h5>

        <div class="table-responsive">
          <table class="table table-hover align-middle">
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
                <td>{{ optional($bk->time_from)->format('Y-m-d H:i') ?? \Illuminate\Support\Str::limit($bk->time_from,16,'') }}</td>
                <td>{{ optional($bk->time_to)->format('Y-m-d H:i')   ?? \Illuminate\Support\Str::limit($bk->time_to,16,'') }}</td>
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
              <tr><td colspan="5" class="text-center text-muted">{{ $t('لا توجد حجوزات','No bookings') }}</td></tr>
            @endforelse
            </tbody>
          </table>
        </div>

        {{-- Pagination للحجوزات --}}
        @if(method_exists(($bookings ?? null), 'links'))
          <div class="mt-2">
            {{ $bookings->appends(['tab'=>'bookings'])->links() }}
          </div>
        @endif
      </div>
    </div>
  </div>
</div>
@endsection
