@extends('layouts.layoutMaster')
@section('title','Time #'.$time->id)
@section('content')
<div class="card shadow-sm mb-3">
  <div class="card-body">
    <div class="row g-3">
      <div class="col-md-3"><strong>Speaker</strong><br>{{ optional($time->speaker)->name_en ?? optional($time->speaker)->name_ar ?? $time->speaker_id }}</div>
      <div class="col-md-3"><strong>Date</strong><br>{{ optional($time->date)->format('Y-m-d') ?? $time->date }}</div>
      <div class="col-md-3"><strong>From</strong><br>{{ $time->time_from }}</div>
      <div class="col-md-3"><strong>To</strong><br>{{ $time->time_to }}</div>
      <div class="col-md-3"><strong>Status</strong><br>{{ ucfirst($time->status) }}</div>
      <div class="col-md-3"><strong>Active</strong><br>{{ $time->active ? 'Yes' : 'No' }}</div>
    </div>
    <div class="mt-3">
      <a href="{{ route('dashboard.speaker-times.edit',$time) }}" class="btn btn-primary">Edit</a>
      <a href="{{ route('dashboard.speaker-times.index') }}" class="btn btn-light">Back</a>
    </div>
  </div>
</div>

<div class="card shadow-sm">
  <div class="card-header">Bookings in this slot</div>
  <div class="table-responsive">
    <table class="table table-hover mb-0">
      <thead><tr><th>#</th><th>Client</th><th>From</th><th>To</th><th>Status</th></tr></thead>
      <tbody>
        @forelse($time->bookings as $b)
          <tr>
            <td>{{ $b->id }}</td>
            <td>{{ optional($b->client)->name ?? $b->client_id }}</td>
            <td>{{ optional($b->time_from)->format('Y-m-d H:i') ?? $b->time_from }}</td>
            <td>{{ optional($b->time_to)->format('Y-m-d H:i') ?? $b->time_to }}</td>
            <td>{{ ucfirst($b->status) }}</td>
          </tr>
        @empty
          <tr><td colspan="5" class="text-center text-muted py-3">No bookings</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
@endsection
