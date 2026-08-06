@extends('layouts.layoutMaster')

@section('title', __('sponsor.add'))

@section('content')
<div class="card">
  <div class="card-header">
    <h5 class="mb-0">{{ __('sponsor.add') }}</h5>
  </div>
  <div class="card-body">
    <form action="{{ route('admin.sponsors.store') }}" method="POST" enctype="multipart/form-data">
      @csrf
      @include('content.sponsors._form')
      <div class="mt-3 text-center">
        <button type="submit" class="btn btn-primary">{{ __('general.save') }}</button>
        <a href="{{ route('admin.sponsors.index') }}" class="btn btn-secondary">{{ __('general.cancel') }}</a>
      </div>
    </form>
  </div>
</div>
@endsection

