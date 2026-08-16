@extends('layouts.layoutMaster')
@section('title','Booking #'.$booking->id)
@section('content')
<div class="card shadow-sm">
  <div class="card-body">
    <div class="row g-3">
      <div class="col-md-3"><strong>Speaker</strong><br>{{ optional($booking->speaker)->name_en ?? optional($booking->speaker)->name_ar ?? $booking->speaker_id }}</div>
      <div class="col-md-3"><strong>Client</strong><br>{{ optional($booking->client)->name ?? $booking->client_id }}</div>
      <div class="col-md-3"><strong>From</strong><br>{{ optional($booking->time_from)->format('Y-m-d H:i') ?? $booking->time_from }}</div>
      <div class="col-md-3"><strong>To</strong><br>{{ optional($booking->time_to)->format('Y-m-d H:i') ?? $booking->time_to }}</div>
      <div class="col-md-3"><strong>Status</strong><br>{{ ucfirst($booking->status) }}</div>
      <div class="col-md-3"><strong>Active</strong><br>{{ $booking->active ? 'Yes':'No' }}</div>
      <div class="col-md-3"><strong>Slot</strong><br>{{ $booking->speaker_time_id ?: '—' }}</div>
    </div>
    <div class="mt-3">
      <a href="{{ route('dashboard.bookings.edit',$booking) }}" class="btn btn-primary">Edit</a>
      <a href="{{ route('dashboard.bookings.index') }}" class="btn btn-light">Back</a>
    </div>
  </div>
</div>
@endsection
