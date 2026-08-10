@extends('layouts.layoutMaster')

@section('title', __('qr_code_list'))

@section('content')
<div class="container-fluid" style="direction:rtl">
  <div class="row align-items-center mb-3">
    <div class="col-sm">
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb bg-white px-3 py-2 rounded shadow-sm">

          <li class="breadcrumb-item active text-primary" aria-current="page">سجل الحضور بالـ QR</li>
        </ol>
      </nav>
    </div>
  </div>

  <!-- فلاتر -->
  <div class="card mb-3">
    <div class="card-body">
      <form method="get" class="row g-3">
        <div class="col-12 col-md-3">
          <label class="form-label">Register ID</label>
          <input type="text" name="register_id" value="{{ request('register_id') }}" class="form-control" placeholder="مثال: 123">
        </div>
        <div class="col-12 col-md-3">
          <label class="form-label">Short Code</label>
          <input type="text" name="short_code" value="{{ request('short_code') }}" class="form-control" placeholder="الكود المختصر">
        </div>
        <div class="col-12 col-md-3">
          <label class="form-label">من تاريخ</label>
          <input type="date" name="from_date" value="{{ request('from_date') }}" class="form-control">
        </div>
        <div class="col-12 col-md-3">
          <label class="form-label">إلى تاريخ</label>
          <input type="date" name="to_date" value="{{ request('to_date') }}" class="form-control">
        </div>

        <div class="col-12 d-flex gap-2 justify-content-end">
          <button class="btn btn-primary">بحث</button>
          <a href="{{ route('qrcodes.attendees') }}" class="btn btn-outline-secondary">مسح الفلاتر</a>
        </div>
      </form>
    </div>
  </div>

  <!-- الجدول -->
  <div class="card">
    <div class="card-body table-responsive">
      <table class="table table-striped align-middle">
        <thead class="table-light">
          <tr>
            <th>#</th>
            <th>الاسم</th>
            <th>البريد</th>
            <th>الهاتف</th>
            <th>Register ID</th>
            <th>Short Code</th>
            <th>تاريخ/وقت الحضور</th>
          </tr>
        </thead>
        <tbody>
          @forelse($attendees as $row)
            <tr>
              <td>{{ $attendees->firstItem() + $loop->index }}</td>
              <td>{{ $row->client_name ?? '—' }}</td>
              <td>{{ $row->client_email ?? '—' }}</td>
              <td>{{ $row->client_phone ?? '—' }}</td>
              <td>{{ $row->register_id }}</td>
              <td>{{ $row->short_code ?? '—' }}</td>
              <td>
                @php
                  $dt = $row->attendance_at ? \Carbon\Carbon::parse($row->attendance_at) : null;
                @endphp
                {{ $dt ? $dt->format('Y-m-d H:i') : '—' }}
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="7" class="text-center text-muted">لا توجد نتائج</td>
            </tr>
          @endforelse
        </tbody>
      </table>

      <div class="mt-3">
        {{ $attendees->links() }}
      </div>
    </div>
  </div>
</div>
@endsection
