@extends('layouts.layoutMaster')
@section('title','Edit Time #'.$time->id)
@section('content')
@if($errors->any()) <div class="alert alert-danger">{{ $errors->first() }}</div> @endif
<div class="card shadow-sm">
  <div class="card-body">
    <form method="post" action="{{ route('dashboard.speaker-times.update',$time) }}">
      @csrf @method('PUT')
      @include('content.speaker_times._form', ['time'=>$time])
      <div class="mt-3 d-flex gap-2">
        <button class="btn btn-primary"><i class="fas fa-save me-1"></i> Update</button>
        <a href="{{ route('dashboard.speaker-times.index') }}" class="btn btn-light">Back</a>
      </div>
    </form>
  </div>
</div>
@endsection
