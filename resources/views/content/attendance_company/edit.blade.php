{{-- resources/views/dashboard/attendance_company/edit.blade.php --}}
@extends('layouts.layoutMaster')
@section('title','تعديل شركة')
@section('content')
<form class="card" method="post" action="{{ route('dashboard.attendance_company.update',$item) }}">
  @csrf @method('put')
  <div class="card-body row g-3">
    <div class="col-md-6">
      <label class="form-label">الاسم</label>
      <input name="name" class="form-control" value="{{ old('name',$item->name) }}" required>
    </div>
    <div class="col-md-3">
      <label class="form-label">مفعّل</label>
      <select name="active" class="form-select">
        <option value="1" @selected($item->active)>نعم</option>
        <option value="0" @selected(!$item->active)>لا</option>
      </select>
    </div>
    <div class="col-md-3">
      <label class="form-label">حضور</label>
      <select name="attendance" class="form-select">
        <option value="0" @selected(!$item->attendance)>غير حاضر</option>
        <option value="1" @selected($item->attendance)>حاضر</option>
      </select>
    </div>
    <div class="col-md-4">
      <label class="form-label">وقت الحضور</label>
      <input type="datetime-local" name="attendance_at" class="form-control"
             value="{{ $item->attendance_at ? $item->attendance_at->format('Y-m-d\TH:i') : '' }}">
    </div>
  </div>
  <div class="card-footer d-flex justify-content-end gap-2">
    <a href="{{ route('dashboard.attendance_company.index') }}" class="btn btn-secondary">رجوع</a>
    <button class="btn btn-primary">تحديث</button>
  </div>
</form>
@endsection
