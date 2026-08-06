@extends('layouts.layoutMaster')

@section('title', 'Client Registrations')

@section('content')
<div class="card shadow-sm">
  <div class="card-header d-flex justify-content-between align-items-center">
    <h4 class="mb-0">Client Registrations</h4>
    <div class="d-flex gap-2">
      <a href="{{ route('dashboard.clients.create') }}" class="btn btn-success">
        <i class="fas fa-plus"></i> Add
      </a>
      <a href="{{ route('dashboard.clients.excel') }}" class="btn btn-secondary">
        <i class="fas fa-file-excel"></i> Export Excel
      </a>
    </div>
  </div>

  <div class="card-body">
    {{-- Filters --}}
    <form method="GET" class="row g-3 mb-4">
      <div class="col-md-3">
        <input type="text" name="name" class="form-control" placeholder="Name" value="{{ request('name') }}">
      </div>
      <div class="col-md-3">
        <input type="text" name="phone" class="form-control" placeholder="Phone" value="{{ request('phone') }}">
      </div>
      <div class="col-md-3">
        <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
      </div>
      <div class="col-md-3">
        <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
      </div>

      {{-- 🔽 Event Filter --}}
      <div class="col-md-3">
        <select name="event_id" class="form-select">
          <option value="">All Events</option>
          @foreach($events as $event)
            <option value="{{ $event->id }}" {{ request('event_id') == $event->id ? 'selected' : '' }}>
              {{ $event->name_en }}
            </option>
          @endforeach
        </select>
      </div>

      <div class="col-12 text-end">
        <button type="submit" class="btn btn-primary">Search</button>
      </div>
    </form>

    {{-- Table --}}
    <div class="table-responsive">
      <table class="table table-bordered table-striped align-middle">
        <thead class="table-light">
          <tr>
            <th>#</th>
            <th>Name</th>
            <th>Email</th>
            <th>Code</th>
            <th>Phone</th>
            <th>Job</th>
            <th>Event</th>
            <th>Experience</th>
            <th>Status</th>
            <th>Created At</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse($clients as $client)
            <tr id="row-{{ $client->id }}">
              <td>{{ $loop->iteration }}</td>
              <td>{{ $client->name }}</td>
              <td>{{ $client->email }}</td>
              <td>{{ $client->country_code }}</td>
              <td>{{ $client->phone }}</td>
              <td>{{ $client->job }}</td>

              {{-- 🔸 Event Name --}}
              <td>{{ $client->event->name_en ?? '-' }}</td>

              <td>
                <span class="badge {{ $client->do_you_have_experince ? 'bg-success' : 'bg-danger' }}">
                  {{ $client->do_you_have_experince ? 'Yes' : 'No' }}
                </span>
              </td>
              <td>
                <span class="badge {{ $client->status == 'complete' ? 'bg-success' : 'bg-warning' }}">
                  {{ ucfirst($client->status) }}
                </span>
              </td>
              <td>{{ $client->created_at->format('Y-m-d') }}</td>
              <td>
                <a href="{{ route('dashboard.clients.show', $client->id) }}" class="btn btn-sm btn-info">Show</a>
                @if($client->status === 'pending')
                  <button class="btn btn-sm btn-outline-primary send-code-btn" data-id="{{ $client->id }}">
                    Send Code
                  </button>
                @endif
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="11" class="text-center">No data found</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    {{-- Pagination --}}
    <div class="mt-4">
      @if ($clients->hasPages())
        <nav>
          <ul class="pagination justify-content-center">
            {{-- Previous --}}
            @if ($clients->onFirstPage())
              <li class="page-item disabled"><span class="page-link">‹</span></li>
            @else
              <li class="page-item">
                <a class="page-link" href="{{ $clients->previousPageUrl() }}" rel="prev">‹</a>
              </li>
            @endif

            {{-- Pages --}}
            @foreach ($clients->getUrlRange(1, $clients->lastPage()) as $page => $url)
              <li class="page-item {{ $clients->currentPage() == $page ? 'active' : '' }}">
                <a class="page-link" href="{{ $url }}">{{ $page }}</a>
              </li>
            @endforeach

            {{-- Next --}}
            @if ($clients->hasMorePages())
              <li class="page-item">
                <a class="page-link" href="{{ $clients->nextPageUrl() }}" rel="next">›</a>
              </li>
            @else
              <li class="page-item disabled"><span class="page-link">›</span></li>
            @endif
          </ul>
        </nav>
      @endif
    </div>
  </div>
</div>


{{-- JS for AJAX Send Code --}}
<script>
  document.querySelectorAll('.send-code-btn').forEach(button => {
    button.addEventListener('click', function () {
      const clientId = this.dataset.id;
      this.disabled = true;
      this.innerText = 'Sending...';

      fetch(`/api/dashboard/clients/${clientId}/send-code`, {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': '{{ csrf_token() }}',
          'Accept': 'application/json',
          'Content-Type': 'application/json'
        }
      })
      .then(res => res.json())
      .then(data => {
        if (data.success) {
          alert('Verification code sent successfully!');
        } else {
          alert('Failed to send code.');
        }
      })
      .catch(() => alert('Error sending code.'))
      .finally(() => {
        this.disabled = false;
        this.innerText = 'Send Code';
      });
    });
  });
</script>
@endsection
