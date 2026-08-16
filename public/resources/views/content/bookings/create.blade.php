@extends('layouts.layoutMaster')
@section('title','Create Booking')
@section('content')
@if($errors->any()) <div class="alert alert-danger">{{ $errors->first() }}</div> @endif
<div class="card shadow-sm">
  <div class="card-body">
    <form method="get" class="row g-2 mb-3">
      <div class="col-md-6">
        <label class="form-label">Speaker (type=1)</label>
        <select name="speaker_id" class="form-select" onchange="this.form.submit()">
          <option value="">Choose…</option>
          @foreach($speakers as $sp)
            <option value="{{ $sp->id }}" @selected(request('speaker_id')==$sp->id)>{{ app()->getLocale()==='ar'?($sp->name_ar??$sp->id):($sp->name_en??$sp->id) }}</option>
          @endforeach
        </select>
      </div>
    </form>

    <form method="post" action="{{ route('dashboard.bookings.store') }}">
      @csrf
      @include('content.bookings._form', ['booking'=>null])
      <div class="mt-3 d-flex gap-2">
        <button class="btn btn-primary"><i class="fas fa-save me-1"></i> Save</button>
        <a href="{{ route('dashboard.bookings.index') }}" class="btn btn-light">Back</a>
      </div>
    </form>
  </div>
</div>
@endsection
