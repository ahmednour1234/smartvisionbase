{{-- resources/views/content/invitations/index.blade.php --}}
@extends('layouts.layoutMaster')

@section('title', __('ads_title'))

@section('content')
@php
  // حضّر أدوات الترقيم اليدوي (نفس الصفحة + نفس الـ query string بدون page)
  $baseUrl = url()->current();
  $qs = request()->query();
  unset($qs['page']);
  $qsStr = http_build_query($qs);

  $pageUrl = function($p) use ($baseUrl, $qsStr) {
    return $baseUrl . '?' . ($qsStr ? ($qsStr . '&') : '') . 'page=' . max(1, (int)$p);
  };

  // نافذة أرقام الصفحات
  $current = $invitations->currentPage();
  $last    = $invitations->lastPage();
  $window  = 2; // عدد الأرقام يمين ويسار الصفحة الحالية
  $start   = max(1, $current - $window);
  $end     = min($last, $current + $window);
  if ($end - $start < $window * 2) {
    // وسّع البداية/النهاية لو القائمة قصيرة
    $start = max(1, min($start, $last - $window * 2));
    $end   = min($last, max($end, 1 + $window * 2));
  }
@endphp

<div class="card">
  <div class="card-header d-flex justify-content-between align-items-center">
    <h5 class="mb-0">{{ __('Invitations') }}</h5>
    <div class="d-flex gap-2">
      <a class="btn btn-outline-secondary btn-sm" href="{{ route('dashboard.invitations.template') }}">{{ __('Template') }}</a>
      <a class="btn btn-primary btn-sm" href="{{ route('dashboard.invitations.export', request()->query()) }}">{{ __('Export') }}</a>
    </div>
  </div>

  <div class="card-body">
    @if(session('success'))
      <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    {{-- Quick stats --}}
    <div class="mb-3">
      <span class="badge bg-secondary">{{ __('Total') }}: {{ $stats['total'] }}</span>
      <span class="badge bg-success">{{ __('Present') }}: {{ $stats['present'] }}</span>
      <span class="badge bg-danger">{{ __('Absent') }}: {{ $stats['absent'] }}</span>
    </div>

    {{-- Filters / Search --}}
    <form class="row g-2 mb-3" method="get">
      <div class="col-md-4">
        <input type="text" name="q" value="{{ $search }}" class="form-control" placeholder="{{ __('Search name or number') }}">
      </div>
      <div class="col-md-3">
        <input type="text" name="type" value="{{ $type }}" class="form-control" placeholder="{{ __('Type (e.g. VIP)') }}">
      </div>
      <div class="col-md-3">
        <select name="attendance" class="form-select">
          <option value="all" {{ $attendance==='all' ? 'selected' : '' }}>{{ __('All') }}</option>
          <option value="present" {{ $attendance==='present' ? 'selected' : '' }}>{{ __('Present') }}</option>
          <option value="absent" {{ $attendance==='absent' ? 'selected' : '' }}>{{ __('Absent') }}</option>
        </select>
      </div>
      <div class="col-md-2">
        <button class="btn btn-outline-primary w-100">{{ __('Filter') }}</button>
      </div>
    </form>

    {{-- Import --}}
    <form action="{{ route('dashboard.invitations.import') }}" method="post" enctype="multipart/form-data" class="mb-3">
      @csrf
      <div class="row g-2 align-items-center">
        <div class="col-md-6">
          <input type="file" name="file" class="form-control" accept=".xlsx,.xls,.csv" required>
        </div>
        <div class="col-md-3">
          <button class="btn btn-success w-100">{{ __('Import Excel') }}</button>
        </div>
        {{-- <div class="col-md-3 text-end">
          <a href="{{ route('dashboard.invitations.create') }}" class="btn btn-dark w-100">{{ __('Add Invitation') }}</a>
        </div> --}}
      </div>
    </form>

    {{-- Table --}}
    <div class="table-responsive">
      <table class="table table-striped align-middle">
        <thead>
          <tr>
            <th>#</th>
            <th>{{ __('Name') }}</th>
            <th>{{ __('Invitation Number') }}</th>
            <th>{{ __('Type') }}</th>
            <th>{{ __('Attendance') }}</th>
            <th>{{ __('Actions') }}</th>
          </tr>
        </thead>
        <tbody>
          @forelse($invitations as $inv)
            <tr>
              <td>{{ $invitations->firstItem() + $loop->index }}</td>
              <td>{{ $inv->name }}</td>
              <td>{{ $inv->invitation_number }}</td>
              <td>{{ $inv->type }}</td>
              <td>
                <button
                  class="btn btn-sm {{ $inv->attendance ? 'btn-success' : 'btn-outline-secondary' }}"
                  onclick="toggleAttendance({{ $inv->id }})">
                  {{ $inv->attendance ? __('Present') : __('Absent') }}
                </button>
              </td>
              <td class="d-flex gap-2">
                {{-- <a class="btn btn-sm btn-primary" href="{{ route('dashboard.invitations.edit', $inv) }}">{{ __('Edit') }}</a> --}}
                <form action="{{ route('dashboard.invitations.destroy', $inv) }}" method="post" onsubmit="return confirm('{{ __('Delete?') }}')">
                  @csrf
                  @method('DELETE')
                  <button class="btn btn-sm btn-danger">{{ __('Delete') }}</button>
                </form>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="6" class="text-center text-muted">{{ __('No data') }}</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    {{-- Native Pagination (بدون أي مكتبات) --}}
    @if($last > 1)
      <nav aria-label="Pagination" class="mt-3">
        <ul class="pagination mb-0">
          {{-- First --}}
          <li class="page-item {{ $current === 1 ? 'disabled' : '' }}">
            <a class="page-link" href="{{ $current === 1 ? '#' : $pageUrl(1) }}" tabindex="-1">« {{ __('First') }}</a>
          </li>

          {{-- Prev --}}
          <li class="page-item {{ $current === 1 ? 'disabled' : '' }}">
            <a class="page-link" href="{{ $current === 1 ? '#' : $pageUrl($current - 1) }}" tabindex="-1">‹ {{ __('Prev') }}</a>
          </li>

          {{-- Leading ellipsis --}}
          @if($start > 1)
            <li class="page-item">
              <a class="page-link" href="{{ $pageUrl(1) }}">1</a>
            </li>
            @if($start > 2)
              <li class="page-item disabled"><span class="page-link">…</span></li>
            @endif
          @endif

          {{-- Window --}}
          @for($i = $start; $i <= $end; $i++)
            <li class="page-item {{ $i === $current ? 'active' : '' }}">
              <a class="page-link" href="{{ $i === $current ? '#' : $pageUrl($i) }}">{{ $i }}</a>
            </li>
          @endfor

          {{-- Trailing ellipsis --}}
          @if($end < $last)
            @if($end < $last - 1)
              <li class="page-item disabled"><span class="page-link">…</span></li>
            @endif
            <li class="page-item">
              <a class="page-link" href="{{ $pageUrl($last) }}">{{ $last }}</a>
            </li>
          @endif

          {{-- Next --}}
          <li class="page-item {{ $current === $last ? 'disabled' : '' }}">
            <a class="page-link" href="{{ $current === $last ? '#' : $pageUrl($current + 1) }}">{{ __('Next') }} ›</a>
          </li>

          {{-- Last --}}
          <li class="page-item {{ $current === $last ? 'disabled' : '' }}">
            <a class="page-link" href="{{ $current === $last ? '#' : $pageUrl($last) }}">{{ __('Last') }} »</a>
          </li>
        </ul>
      </nav>

      {{-- ملخص بسيط --}}
      <div class="text-muted small mt-2">
        {{ __('Showing') }} {{ $invitations->firstItem() }}–{{ $invitations->lastItem() }}
        {{ __('of') }} {{ $invitations->total() }}
      </div>
    @endif
  </div>
</div>

{{-- CSRF for fetch --}}
<meta name="csrf-token" content="{{ csrf_token() }}">

<script>
function toggleAttendance(id) {
  fetch(`{{ url('dashboard/invitations') }}/${id}/attendance`, {
    method: 'PATCH',
    headers: {
      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
      'Accept': 'application/json'
    }
  })
  .then(r => r.json())
  .then(data => {
    if (data && data.success) {
      // إعادة تحميل للحفاظ على الفلاتر والترقيم
      window.location.reload();
    }
  })
  .catch(() => {});
}
</script>
@endsection
