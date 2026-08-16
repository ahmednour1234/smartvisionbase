{{-- resources/views/content/customer/index-table.blade.php --}}
@extends('layouts.layoutMaster')

@section('title', $pageTitle ?? 'Leads')

@section('content')
@php
  use Illuminate\Support\Facades\Schema;

  // Build users (agents) list for selects if not provided by the controller
  $usersForFilters = $users
      ?? \App\Models\User::query()
            ->when(Schema::hasColumn('users','type'), fn($q)=>$q->where('type','callcenter'))
            ->orderBy('name')
            ->select('id','name','email')
            ->get();

  // statuses (fallback if controller didn't pass $statuses)
  $statuses = $statuses ?? ['new','follow_up','accept','reject','lost'];

  // Keep filter values
  $filters = $filters ?? request()->only(['q','status','assigned_user_id','assigned_by_id','from','to']);

  // Whether we can show "Assigned By" column
  $showAssignedBy = Schema::hasColumn('leads','assigned_by_id');
@endphp

<div class="card">
  <div class="card-header d-flex justify-content-between align-items-center">
    <h5 class="mb-0">{{ $pageTitle ?? 'Leads' }}</h5>

    <div class="d-flex gap-2">
      <a href="{{ route('dashboard.leads.export', request()->query()) }}" class="btn btn-outline-secondary btn-sm">
        <i class="ti ti-table-export me-1"></i> Export
      </a>
      <a href="{{ request()->url() }}" class="btn btn-outline-dark btn-sm">
        <i class="ti ti-rotate-2 me-1"></i> Reset
      </a>
    </div>
  </div>

  <div class="card-body">
    {{-- Filters --}}
    <form method="get" class="row g-2 mb-3">
      <div class="col-lg-4">
        <input type="text" name="q" value="{{ $filters['q'] ?? '' }}" class="form-control" placeholder="Search name / phone / email / job">
      </div>



      <div class="col-lg-3">
        <select name="assigned_user_id" class="form-select">
          <option value="">Assigned to (agent): Any</option>
          @foreach($usersForFilters as $u)
            <option value="{{ $u->id }}" @selected(($filters['assigned_user_id'] ?? '')==$u->id)>
              {{ $u->name }} {{ $u->email ? "($u->email)" : '' }}
            </option>
          @endforeach
        </select>
      </div>


      <div class="col-lg-2">
        <input type="date" name="from" value="{{ $filters['from'] ?? '' }}" class="form-control" placeholder="From">
      </div>
      <div class="col-lg-2">
        <input type="date" name="to" value="{{ $filters['to'] ?? '' }}" class="form-control" placeholder="To">
      </div>

      <div class="col-lg-2 d-grid">
        <button class="btn btn-primary"><i class="ti ti-filter me-1"></i> Filter</button>
      </div>
    </form>

    {{-- Table --}}
    <div class="table-responsive">
      <table class="table align-middle">
        <thead>
          <tr>
            <th>#</th>
            <th>Lead</th>
            <th>Email</th>
            <th>Phone</th>
            <th>Assigned To</th>
            @if($showAssignedBy)
              <th>Assigned By</th>
              <th>Assigned At</th>
            @endif
            <th>Status</th>
            <th>Created</th>
            <th style="width:120px;">Actions</th>
          </tr>
        </thead>
        <tbody>
        @forelse($leads as $i => $lead)
          <tr @class(['table-danger' => in_array($lead->status,['new','follow_up']) && $lead->next_call_at && \Carbon\Carbon::parse($lead->next_call_at)->lt(now())])>
            <td>{{ $leads->firstItem() + $i }}</td>
            <td>
              <div class="fw-semibold">{{ $lead->name }}</div>
              <div class="text-muted small">{{ $lead->job ?? '—' }}</div>
            </td>
            <td><a href="mailto:{{ $lead->email }}">{{ $lead->email ?? '—' }}</a></td>
            <td><a href="tel:{{ $lead->phone }}">{{ $lead->phone ?? '—' }}</a></td>
            <td>{{ optional($lead->assignedUser)->name ?? '—' }}</td>

            @if($showAssignedBy)
              <td>{{ optional($lead->assignedBy ?? null)->name ?? '—' }}</td>
              <td>{{ $lead->assigned_at ? \Carbon\Carbon::parse($lead->assigned_at)->format('Y-m-d H:i') : '—' }}</td>
            @endif

            <td>
              @switch($lead->status)
                @case('accept')    <span class="badge bg-success">Accepted</span> @break
                @case('follow_up') <span class="badge bg-info text-dark">Follow Up</span> @break
                @case('new')       <span class="badge bg-secondary">New</span> @break
                @case('reject')    <span class="badge bg-danger">Rejected</span> @break
                @case('lost')      <span class="badge bg-dark">Lost</span> @break
                @default           <span class="badge bg-light text-dark">{{ $lead->status }}</span>
              @endswitch
            </td>

            <td>{{ $lead->created_at?->format('Y-m-d H:i') }}</td>

            <td>
              <div class="btn-group btn-group-sm">
                <a href="{{ route('dashboard.leads.show', $lead) }}" class="btn btn-outline-primary">View</a>
                {{-- Quick assign (optional): opens assign offcanvas if you use it --}}
                @if(!$lead->status || !in_array($lead->status,['accept','reject']))
                  <a href="{{ route('dashboard.leads.status', $lead) }}"
                     onclick="event.preventDefault(); document.getElementById('toFollowUp-{{ $lead->id }}').submit();"
                     class="btn btn-outline-secondary" title="Mark Follow Up">F/U</a>
                  <form id="toFollowUp-{{ $lead->id }}" action="{{ route('dashboard.leads.status', $lead) }}" method="post" class="d-none">
                    @csrf
                    <input type="hidden" name="status" value="follow_up">
                  </form>
                @endif
              </div>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="{{ 10 - ($showAssignedBy ? 0 : 2) }}" class="text-center text-muted">No leads found.</td>
          </tr>
        @endforelse
        </tbody>
      </table>
    </div>

    <div class="mt-3">
      {{ $leads->appends(request()->query())->links() }}
    </div>
  </div>
</div>
@endsection
