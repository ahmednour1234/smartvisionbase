@extends('layouts.layoutMaster')
@section('title','Times Sheets')
@section('content')
@php
  $isAr = app()->getLocale()==='ar';
  $t = fn($ar,$en)=> $isAr? $ar:$en;
@endphp

@if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif
@if($errors->any())   <div class="alert alert-danger">{{ $errors->first() }}</div> @endif

<div class="card shadow-sm mb-3">
  <div class="card-body">
    <form class="row g-3">
      <div class="col-md-3">
        <label class="form-label">{{ $t('المتحدث','Speaker') }}</label>
        <select name="speaker_id" class="form-select">
          <option value="">{{ $t('الكل','All') }}</option>
          @foreach($speakers as $sp)
            <option value="{{ $sp->id }}" @selected(($filters['speaker_id']??null)==$sp->id)>
              {{ $isAr?($sp->name_ar??$sp->id):($sp->name_en??$sp->id) }}
            </option>
          @endforeach
        </select>
      </div>
      <div class="col-md-2">
        <label class="form-label">{{ $t('من يوم','From day') }}</label>
        <input type="date" name="date_from" value="{{ $filters['date_from'] }}" class="form-control">
      </div>
      <div class="col-md-2">
        <label class="form-label">{{ $t('إلى يوم','To day') }}</label>
        <input type="date" name="date_to" value="{{ $filters['date_to'] }}" class="form-control">
      </div>
      <div class="col-md-2">
        <label class="form-label">{{ $t('من وقت','From time') }}</label>
        <input type="time" name="time_from" value="{{ $filters['time_from'] }}" class="form-control">
      </div>
      <div class="col-md-2">
        <label class="form-label">{{ $t('إلى وقت','To time') }}</label>
        <input type="time" name="time_to" value="{{ $filters['time_to'] }}" class="form-control">
      </div>
      <div class="col-md-1">
        <label class="form-label">{{ $t('مفعل','Active') }}</label>
        <select name="active" class="form-select">
          <option value="">{{ $t('الكل','All') }}</option>
          <option value="1" @selected(($filters['active']??null)===true)> {{ $t('نعم','Yes') }} </option>
          <option value="0" @selected(($filters['active']??null)===false)> {{ $t('لا','No') }} </option>
        </select>
      </div>
      <div class="col-md-2">
        <label class="form-label">{{ $t('الحالة','Status') }}</label>
        <select name="status" class="form-select">
          <option value="">{{ $t('الكل','All') }}</option>
          <option value="available"  @selected(($filters['status']??'')==='available')>{{ $t('متاح','Available') }}</option>
          <option value="booked"     @selected(($filters['status']??'')==='booked')>{{ $t('محجوز','Booked') }}</option>
          <option value="unavailable"@selected(($filters['status']??'')==='unavailable')>{{ $t('غير متاح','Unavailable') }}</option>
        </select>
      </div>
      <div class="col-md-12 d-flex gap-2">
        <button class="btn btn-primary"><i class="fas fa-filter me-1"></i> {{ $t('تصفية','Filter') }}</button>
        <a href="{{ route('dashboard.speaker-times.index') }}" class="btn btn-light">{{ $t('إعادة ضبط','Reset') }}</a>
        <a href="{{ route('dashboard.speaker-times.create') }}" class="btn btn-success ms-auto"><i class="fas fa-plus me-1"></i> {{ $t('إضافة موعد','Create') }}</a>
      </div>
    </form>
  </div>
</div>

<div class="card shadow-sm">
  <div class="table-responsive">
    <table class="table table-striped align-middle mb-0">
      <thead>
        <tr>
          <th>#</th>
          <th>{{ $t('المتحدث','Speaker') }}</th>
          <th>{{ $t('اليوم','Date') }}</th>
          <th>{{ $t('من','From') }}</th>
          <th>{{ $t('إلى','To') }}</th>
          <th>{{ $t('الحالة','Status') }}</th>
          <th>{{ $t('مفعل؟','Active?') }}</th>
          <th class="text-end">{{ $t('إجراءات','Actions') }}</th>
        </tr>
      </thead>
      <tbody>
        @forelse($times as $row)
          <tr>
            <td>{{ $row->id }}</td>
            <td>{{ $isAr ? ($row->speaker->name_ar ?? $row->speaker_id) : ($row->speaker->name_en ?? $row->speaker_id) }}</td>
            <td>{{$row->date->format('Y-m-d') ?? $row->date }}</td>
            <td>{{ $row->time_from }}</td>
            <td>{{ $row->time_to }}</td>
            <td>
              @if($row->status==='available') <span class="badge bg-success">{{ $t('متاح','Available') }}</span>
              @elseif($row->status==='booked') <span class="badge bg-primary">{{ $t('محجوز','Booked') }}</span>
              @else <span class="badge bg-secondary">{{ $t('غير متاح','Unavailable') }}</span> @endif
            </td>
            <td>{!! $row->active ? '<span class="badge bg-success">'.$t('نعم','Yes').'</span>' : '<span class="badge bg-danger">'.$t('لا','No').'</span>' !!}</td>
            <td class="text-end">
              <a class="btn btn-sm btn-outline-secondary" href="{{ route('dashboard.speaker-times.show',$row) }}">{{ $t('عرض','Show') }}</a>
              <a class="btn btn-sm btn-outline-primary" href="{{ route('dashboard.speaker-times.edit',$row) }}">{{ $t('تعديل','Edit') }}</a>
              <form class="d-inline" method="post" action="{{ route('dashboard.speaker-times.toggle',$row) }}">@csrf @method('PATCH')
                <button class="btn btn-sm btn-outline-warning">{{ $row->active ? $t('تعطيل','Disable') : $t('تفعيل','Enable') }}</button>
              </form>
              <form class="d-inline" method="post" action="{{ route('dashboard.speaker-times.destroy',$row) }}" onsubmit="return confirm('{{ $t('حذف؟','Delete?') }}')">@csrf @method('DELETE')
                <button class="btn btn-sm btn-outline-danger">{{ $t('حذف','Delete') }}</button>
              </form>
            </td>
          </tr>
        @empty
          <tr><td colspan="8" class="text-center text-muted py-4">{{ $t('لا توجد بيانات','No data') }}</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  <div class="card-footer">{{ $times->links() }}</div>
</div>
@endsection
