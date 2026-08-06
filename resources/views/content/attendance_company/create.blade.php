{{-- resources/views/dashboard/attendance_company/create.blade.php --}}
@extends('layouts.layoutMaster')
@section('title','إضافة شركة')
@section('content')
<form class="card" method="post" action="{{ route('dashboard.attendance_company.store') }}">
  @csrf
  <div class="card-body row g-3">
    <div class="col-md-6">
      <label class="form-label">الاسم</label>
      <input name="name" class="form-control" required>
    </div>
    <div class="col-md-3">
      <label class="form-label">مفعّل</label>
      <select name="active" class="form-select">
        <option value="1" selected>نعم</option>
        <option value="0">لا</option>
      </select>
    </div>
    <div class="col-md-3">
      <label class="form-label">حضور</label>
      <select name="attendance" class="form-select">
        <option value="0" selected>غير حاضر</option>
        <option value="1">حاضر</option>
      </select>
    </div>
    <div class="col-md-4">
      <label class="form-label">وقت الحضور (اختياري)</label>
      <input type="datetime-local" name="attendance_at" class="form-control">
    </div>
  </div>
  <div class="card-footer d-flex justify-content-end gap-2">
    <a href="{{ route('dashboard.attendance_company.index') }}" class="btn btn-secondary">إلغاء</a>
    <button class="btn btn-primary">حفظ</button>
  </div>
</form>
@endsection
