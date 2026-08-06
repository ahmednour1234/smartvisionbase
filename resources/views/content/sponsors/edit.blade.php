@extends('layouts.layoutMaster')

@section('title', __('sponsor.edit'))

@section('content')
<div class="card">
  <div class="card-header">
    <h5 class="mb-0">{{ __('sponsor.edit') }}</h5>
  </div>
  <div class="card-body">
    <form action="{{ route('admin.sponsors.update', $sponsor->id) }}" method="POST" enctype="multipart/form-data">
      @csrf
      @method('PUT')
      @include('content.sponsors._form', ['sponsor' => $sponsor])
      <div class="mt-3 text-center">
        <button type="submit" class="btn btn-primary">{{ __('general.update') }}</button>
        <a href="{{ route('admin.sponsors.index') }}" class="btn btn-secondary">{{ __('general.cancel') }}</a>
      </div>
    </form>
  </div>
</div>
@endsection
