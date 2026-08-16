@extends('layouts.layoutMaster')
@section('title','Edit Booking #'.$booking->id)
@section('content')
@if($errors->any()) <div class="alert alert-danger">{{ $errors->first() }}</div> @endif
<div class="card shadow-sm">
  <div class="card-body">
    <form method="post" action="{{ route('dashboard.bookings.update',$booking) }}">
      @csrf @method('PUT')
      @include('content.bookings._form', ['booking'=>$booking])
      <div class="mt-3 d-flex gap-2">
        <button class="btn btn-primary"><i class="fas fa-save me-1"></i> Update</button>
        <a href="{{ route('dashboard.bookings.index') }}" class="btn btn-light">Back</a>
      </div>
    </form>
  </div>
</div>
@endsection
