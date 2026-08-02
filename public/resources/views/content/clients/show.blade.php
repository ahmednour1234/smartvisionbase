@extends('layouts.layoutMaster')

@section('title', __('registrations.details'))

@section('content')
<div class="card shadow-sm border-0">
  <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
    <h5 class="mb-0"><i class="fas fa-user"></i> {{ __('registrations.details') }}</h5>
    <a href="{{ route('dashboard.clients.index') }}" class="btn btn-light btn-sm">
      <i class="fas fa-arrow-left"></i> {{ __('registrations.back') }}
    </a>
  </div>

  <div class="card-body py-4">
    <div class="row gy-4">

      {{-- Full Name --}}
      <div class="col-md-6">
        <label class="text-muted small">{{ __('registrations.name') }}</label>
        <div class="fw-semibold">{{ $client->name }}</div>
      </div>

      {{-- Email --}}
      <div class="col-md-6">
        <label class="text-muted small">{{ __('registrations.email') }}</label>
        <div class="fw-semibold">{{ $client->email }}</div>
      </div>

      {{-- Phone --}}
      <div class="col-md-6">
        <label class="text-muted small">{{ __('registrations.phone') }}</label>
        <div class="fw-semibold">{{ $client->country_code }} {{ $client->phone }}</div>
      </div>

      {{-- Job --}}
      <div class="col-md-6">
        <label class="text-muted small">{{ __('registrations.job') }}</label>
        <div class="fw-semibold">{{ $client->job ?? '-' }}</div>
      </div>

      {{-- Status --}}
      <div class="col-md-6">
        <label class="text-muted small">{{ __('registrations.active') }}</label>
        <div>
          <span class="badge {{ $client->active ? 'bg-success' : 'bg-secondary' }}">
            {{ $client->active ? __('registrations.active_yes') : __('registrations.active_no') }}
          </span>
        </div>
      </div>

      {{-- Form --}}
      <div class="col-md-6">
        <label class="text-muted small">{{ __('registrations.form') }}</label>
        <div class="fw-semibold">{{ $client->form->number ?? '-' }}</div>
      </div>

      {{-- Image --}}
      @if($client->img)
      <div class="col-md-6">
        <label class="text-muted small">{{ __('registrations.image') }}</label>
        <div class="mt-2">
          <img src="{{ asset($client->img) }}" class="img-thumbnail border" width="140">
        </div>
      </div>
      @endif

      {{-- Created At --}}
      <div class="col-md-6">
        <label class="text-muted small">{{ __('registrations.created_at') }}</label>
        <div class="fw-semibold">{{ $client->created_at->format('Y-m-d H:i') }}</div>
      </div>

    </div>
  </div>
</div>
@endsection
