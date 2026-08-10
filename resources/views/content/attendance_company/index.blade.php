{{-- resources/views/dashboard/attendance_company/index.blade.php --}}
@extends('layouts.layoutMaster')

@section('title', 'غياب الشركات')

<style>
  :root{
    --brand-1:#4f46e5; /* indigo-600 */
    --brand-2:#0ea5e9; /* sky-500 */
    --surface:#ffffff;
    --text-1:#0f172a; /* slate-900 */
    --text-2:#475569; /* slate-600 */
    --muted:#94a3b8;  /* slate-400 */
    --ring:rgba(79,70,229,.35);
    --shadow:0 10px 30px rgba(2,6,23,.08);
    --radius:16px;
  }
  @media (prefers-color-scheme: dark){
    :root{
      --surface:#0b1220;
      --text-1:#e2e8f0;
      --text-2:#94a3b8;
      --muted:#64748b;
      --shadow:0 12px 30px rgba(0,0,0,.45);
    }
    .table thead th{ background:#0f172a !important; }
  }
  .card-elevated{ border:0; border-radius:var(--radius); background:var(--surface); box-shadow:var(--shadow); }

  /* Hero */


  /* Inputs */
  .input-with-icon .input-group-text{ background:#fff; border-right:0; }
  .input-with-icon .form-control{ border-left:0; }
  .form-control:focus,.form-select:focus{ box-shadow:0 0 0 .25rem var(--ring); }

  /* Table */

  /* Badges */

  /* Pagination */
  .pager{ display:flex; align-items:center; gap:.5rem; flex-wrap:wrap; }
  .pager .page-link{
    border:1px solid #e2e8f0; border-radius:.5rem;
    padding:.5rem .75rem; color:var(--text-1);
    text-decoration:none; display:inline-flex; align-items:center; gap:.35rem;
  }
  .pager .page-link:hover{ background:#f8fafc; }
  .pager .page-link.active,.pager .active>.page-link{
    background: linear-gradient(135deg, var(--brand-1), var(--brand-2));
    color:#fff; border-color:transparent;
  }
  .pager .page-link.disabled,.pager .disabled>.page-link{ opacity:.5; pointer-events:none; }
  .btn-size{ min-width: 130px; } /* توحيد مقاسات الأزرار */
</style>

@section('content')

{{-- ===== Hero / Toolbar ===== --}}
<div class="card card-hero mb-4">
  <div class="card-body d-flex flex-column flex-lg-row align-items-start align-items-lg-center justify-content-between gap-3">
    <div class="d-flex align-items-center gap-3">
      <div class="rounded-circle bg-white bg-opacity-25 p-3">
        <i class="bi bi-people fs-3 text-white"></i>
      </div>
      <div>
        <h3 class="mb-1">غياب الشركات</h3>
        <p class="mb-0 text-white-75">إدارة حضور/غياب الشركات مع البحث والتصفية والاستيراد/التصدير</p>
      </div>
    </div>
    <div class="d-flex flex-wrap gap-2">
      <a href="{{ route('dashboard.attendance_company.create') }}" class="btn btn-light text-primary btn-size">
        <i class="bi bi-plus-circle me-1"></i> إضافة
      </a>
      <a href="{{ route('dashboard.attendance_company.export') }}" class="btn btn-outline-light btn-size">
        <i class="bi bi-filetype-xlsx me-1"></i> تصدير Excel
      </a>
      <button class="btn btn-outline-light btn-size" type="button" data-bs-toggle="offcanvas" data-bs-target="#filtersOffcanvas">
        <i class="bi bi-funnel me-1"></i> تصفية وبحث
      </button>
    </div>
  </div>
</div>

{{-- ===== Filters Offcanvas ===== --}}
<div class="offcanvas offcanvas-end" tabindex="-1" id="filtersOffcanvas" aria-labelledby="filtersLabel">
  <div class="offcanvas-header">
    <h5 id="filtersLabel" class="mb-0"><i class="bi bi-funnel me-2"></i>بحث وتصفية</h5>
    <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="إغلاق"></button>
  </div>
  <div class="offcanvas-body">
    <form method="GET" class="row g-3">
      <div class="col-12">
        <label class="form-label">بحث بالاسم</label>
        <div class="input-group input-with-icon">
          <span class="input-group-text"><i class="bi bi-search"></i></span>
          <input name="search" class="form-control" placeholder="ابحث بالاسم" value="{{ request('search') }}">
        </div>
      </div>
      <div class="col-md-6">
        <label class="form-label">حالة التفعيل</label>
        <select name="active" class="form-select">
          <option value="">الكل</option>
          <option value="1" @selected(request('active')==='1')>مفعّل</option>
          <option value="0" @selected(request('active')==='0')>غير مفعّل</option>
        </select>
      </div>
      <div class="col-md-6">
        <label class="form-label">الحضور</label>
        <select name="attendance" class="form-select">
          <option value="">الكل</option>
          <option value="1" @selected(request('attendance')==='1')>حاضر</option>
          <option value="0" @selected(request('attendance')==='0')>غير حاضر</option>
        </select>
      </div>
      <div class="col-12 d-flex gap-2">
        <button class="btn btn-primary flex-fill btn-size">
          <i class="bi bi-search me-1"></i> تصفية
        </button>
        <a href="{{ route('dashboard.attendance_company.index') }}" class="btn btn-outline-secondary flex-fill btn-size">
          <i class="bi bi-arrow-counterclockwise me-1"></i> إعادة تعيين
        </a>
      </div>
    </form>

    <hr>

    {{-- Inline Import --}}
    <form class="d-flex flex-column flex-xl-row gap-2" action="{{ route('dashboard.attendance_company.import') }}" method="post" enctype="multipart/form-data">
      @csrf
      <input type="file" name="file" class="form-control" required>
      <button class="btn btn-outline-success btn-size">
        <i class="bi bi-upload me-1"></i> استيراد
      </button>
    </form>
  </div>
</div>

{{-- ===== Helpers: نحسب صفحات الباجينج مرة ونستخدمها أينما نحتاج ===== --}}
@php
  /** @var \Illuminate\Contracts\Pagination\LengthAwarePaginator $items */
  $paginator = $items;

  $hasManyPages = $paginator->lastPage() > 1;
  $current      = $paginator->currentPage();
  $last         = $paginator->lastPage();
  $perPage      = $paginator->perPage();
  $total        = $paginator->total();

  $from = ($current - 1) * $perPage + 1;
  $to   = min($current * $perPage, $total);

  $q = request()->query();

  $window = 2; // ±2 صفحات
  $start  = max(1, $current - $window);
  $end    = min($last, $current + $window);

  $pages = [1];
  for ($i = $start; $i <= $end; $i++) { $pages[] = $i; }
  $pages[] = $last;

  $pages = array_values(array_unique(array_filter($pages, fn($n)=>$n>=1 && $n <= $last)));
  sort($pages);

  $urlFor = function($page) use ($q) {
      $params = array_merge($q, ['page' => $page]);
      return url()->current() . '?' . http_build_query($params);
  };
@endphp

{{-- ===== Data Card ===== --}}
<div class="card card-elevated">
  <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
    <div class="d-flex align-items-center gap-2">
      <span class="badge text-bg-primary"><i class="bi bi-people me-1"></i> السجلات</span>
      @if($items->total())
        <span class="badge text-bg-light"><i class="bi bi-collection me-1"></i>{{ $items->total() }}</span>
      @endif
    </div>
    <div class="d-flex gap-2">
      <a href="{{ route('dashboard.attendance_company.export') }}" class="btn btn-outline-secondary btn-size">
        <i class="bi bi-filetype-xlsx me-1"></i> تصدير Excel
      </a>
      <a href="{{ route('dashboard.attendance_company.create') }}" class="btn btn-primary btn-size">
        <i class="bi bi-plus-circle me-1"></i> إضافة
      </a>
    </div>
  </div>

  <div class="card-body">
    {{-- Top Pagination --}}
    @if($hasManyPages)
      <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
        <div class="text-muted small">
          <i class="bi bi-list-check me-1"></i>
          عرض <strong>{{ $from }}</strong> إلى <strong>{{ $to }}</strong> من <strong>{{ $total }}</strong>
        </div>
        <nav class="pager" aria-label="pagination">
          <a class="page-link {{ $current === 1 ? 'disabled' : '' }}" href="{{ $current === 1 ? '#' : $urlFor(1) }}" title="الأولى"><i class="bi bi-skip-backward-fill"></i></a>
          <a class="page-link {{ $current === 1 ? 'disabled' : '' }}" href="{{ $current === 1 ? '#' : $urlFor($current-1) }}" aria-label="السابق"><i class="bi bi-chevron-right"></i> السابق</a>
          @php $prev = null; @endphp
          @foreach ($pages as $num)
            @if(!is_null($prev) && $num > $prev + 1)
              <span class="page-link disabled">…</span>
            @endif
            <a class="page-link {{ $num === $current ? 'active' : '' }}" href="{{ $urlFor($num) }}">{{ $num }}</a>
            @php $prev = $num; @endphp
          @endforeach
          <a class="page-link {{ $current >= $last ? 'disabled' : '' }}" href="{{ $current >= $last ? '#' : $urlFor($current+1) }}" aria-label="التالي">التالي <i class="bi bi-chevron-left"></i></a>
          <a class="page-link {{ $current >= $last ? 'disabled' : '' }}" href="{{ $current >= $last ? '#' : $urlFor($last) }}" title="الأخيرة"><i class="bi bi-skip-forward-fill"></i></a>
        </nav>
      </div>
    @endif

    <div class="table-responsive">
      <table class="table table-hover align-middle">
        <thead class="table-light">
          <tr>
            <th>#</th>
            <th>الاسم</th>
            <th>مفعّل</th>
            <th>حضور</th>
            <th>وقت الحضور</th>
            <th>أُنشئ في</th>
            <th class="text-end">إجراءات</th>
          </tr>
        </thead>
        <tbody>
          @forelse($items as $row)
            <tr>
              <td>{{ $row->id }}</td>
              <td class="fw-semibold">
                <div class="d-flex align-items-center gap-2">
                  <span class="badge rounded-pill badge-soft">ID {{ $row->id }}</span>
                  <span>{{ $row->name }}</span>
                </div>
              </td>
              <td><span class="badge bg-{{ $row->active ? 'success':'secondary' }}">{{ $row->active ? 'نعم' : 'لا' }}</span></td>
              <td><span class="badge bg-{{ $row->attendance ? 'info':'warning' }}">{{ $row->attendance ? 'حاضر' : 'غير حاضر' }}</span></td>
              <td>{{ optional($row->attendance_at)->format('Y-m-d H:i') ?: '-' }}</td>
              <td>{{ optional($row->created_at)->format('Y-m-d') }}</td>
              <td class="text-end">
                <div class="btn-group" role="group" aria-label="row actions">
                  <a href="{{ route('dashboard.attendance_company.edit',$row) }}" class="btn btn-sm btn-outline-primary btn-size">تعديل</a>

                  <form method="post" action="{{ route('dashboard.attendance_company.toggleActive',$row) }}">
                    @csrf
                    <button class="btn btn-sm btn-outline-secondary btn-size">{{ $row->active ? 'تعطيل' : 'تفعيل' }}</button>
                  </form>

                  @if(!$row->attendance)
                    <form method="post" action="{{ route('dashboard.attendance_company.markAttendance',$row) }}">
                      @csrf
                      <button class="btn btn-sm btn-outline-success btn-size">تسجيل حضور</button>
                    </form>
                  @else
                    <form method="post" action="{{ route('dashboard.attendance_company.unmarkAttendance',$row) }}">
                      @csrf
                      <button class="btn btn-sm btn-outline-warning btn-size">إلغاء حضور</button>
                    </form>
                  @endif

                  <form method="post" action="{{ route('dashboard.attendance_company.destroy',$row) }}" onsubmit="return confirm('حذف السجل؟')">
                    @csrf @method('delete')
                    <button class="btn btn-sm btn-outline-danger btn-size">حذف</button>
                  </form>
                </div>
              </td>
            </tr>
          @empty
            <tr><td colspan="7" class="text-center text-muted py-4"><i class="bi bi-inbox me-1"></i> لا توجد بيانات</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>

    {{-- Bottom Pagination --}}
    @if($hasManyPages)
      <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mt-3">
        <div class="text-muted small">
          <i class="bi bi-list-check me-1"></i>
          عرض <strong>{{ $from }}</strong> إلى <strong>{{ $to }}</strong> من <strong>{{ $total }}</strong>
        </div>
        <nav class="pager" aria-label="pagination">
          <a class="page-link {{ $current === 1 ? 'disabled' : '' }}" href="{{ $current === 1 ? '#' : $urlFor(1) }}" title="الأولى"><i class="bi bi-skip-backward-fill"></i></a>
          <a class="page-link {{ $current === 1 ? 'disabled' : '' }}" href="{{ $current === 1 ? '#' : $urlFor($current-1) }}" aria-label="السابق"><i class="bi bi-chevron-right"></i> السابق</a>
          @php $prev = null; @endphp
          @foreach ($pages as $num)
            @if(!is_null($prev) && $num > $prev + 1)
              <span class="page-link disabled">…</span>
            @endif
            <a class="page-link {{ $num === $current ? 'active' : '' }}" href="{{ $urlFor($num) }}">{{ $num }}</a>
            @php $prev = $num; @endphp
          @endforeach
          <a class="page-link {{ $current >= $last ? 'disabled' : '' }}" href="{{ $current >= $last ? '#' : $urlFor($current+1) }}" aria-label="التالي">التالي <i class="bi bi-chevron-left"></i></a>
          <a class="page-link {{ $current >= $last ? 'disabled' : '' }}" href="{{ $current >= $last ? '#' : $urlFor($last) }}" title="الأخيرة"><i class="bi bi-skip-forward-fill"></i></a>
        </nav>
      </div>
    @endif
  </div>
</div>
@endsection

@push('scripts')
<script>
  // تفعيل Tooltips إن وُجدت عناصر لاحقًا
  const tooltipTriggerList = Array.from(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
  tooltipTriggerList.forEach(el => new bootstrap.Tooltip(el));
</script>
@endpush
