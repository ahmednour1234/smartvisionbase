@extends('layouts.layoutMaster')

@section('title', __('clients.create'))

@section('content')
<div class="card">
  <div class="card-header bg-primary text-white">
    <h4 class="mb-0">{{ __('clients.create') }}</h4>
  </div>

  <div class="card-body">
    <form action="{{ route('dashboard.clients.store') }}" method="POST" enctype="multipart/form-data">
      @csrf
      @include('content.clients._form', ['client' => null])
      <div class="text-end mt-3">
        <button type="submit" class="btn btn-success">{{ __('clients.save') }}</button>
      </div>
    </form>
  </div>
</div>
@endsection
