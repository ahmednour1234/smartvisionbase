@extends('layouts.layoutMaster')

@section('title', __('clients.edit'))

@section('content')
<div class="card">
  <div class="card-header bg-warning text-dark">
    <h4 class="mb-0">{{ __('clients.edit') }}</h4>
  </div>

  <div class="card-body">
    <form action="{{ route('dashboard.clients.update', $client->id) }}" method="POST" enctype="multipart/form-data">
      @csrf
      @method('PUT')
      @include('content.clients._form', ['client' => $client])
      <div class="text-end mt-3">
        <button type="submit" class="btn btn-primary">{{ __('clients.update') }}</button>
      </div>
    </form>
  </div>
</div>
@endsection
