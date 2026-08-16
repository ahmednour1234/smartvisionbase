{{-- resources/views/content/customer/index.blade.php --}}
@extends('layouts.layoutMaster')

@section('title', __('Leads'))

@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>

@php
  use Illuminate\Support\Facades\Schema;
  use Illuminate\Support\Facades\DB;

  // Build a users list for filters / assign action
  $usersForFilters = $users
      ?? \App\Models\User::query()
            ->when(Schema::hasColumn('users','type'), fn($q)=>$q->where('type','callcenter'))
            ->orderBy('name')
            ->select('id','name','email')
            ->get();

  // Helper to keep filter values
  $filters = $filters ?? request()->only(['q','status','assigned_user_id','assigned_by','assigned_by_id','from','to']);

  // Statuses (fallback)
  $statuses = $statuses ?? ['new','follow_up','accept','reject','lost'];

  // ==== Prefetch QR filenames for all leads on the page (to avoid N+1) ====
  $leadQrIds = collect($leads ?? [])->pluck('qrcode_id')->filter();

  // Support misspelled column "qecode_id" if it exists and has values
  try {
      if (Schema::hasColumn((new \App\Models\Lead)->getTable(), 'qecode_id')) {
          $leadQrIds = $leadQrIds->merge(collect($leads ?? [])->pluck('qecode_id')->filter());
      }
  } catch (\Throwable $e) { /* ignore */ }

  $qrMap = $leadQrIds->isNotEmpty()
      ? DB::table('qrcodes')->whereIn('id', $leadQrIds->unique()->values())->pluck('qrcode', 'id')   // id => filename.png
      : collect();
@endphp

<style>
  :root{
    --card-radius: 18px;
    --card-border: #e9edf3;
    --card-shadow: 0 8px 26px rgba(15,23,42,.05);
    --danger-200:#fecaca;
    --danger-300:#fca5a5;

    --btn-h:40px;
    --btn-r:12px;
    --ic:18px;
  }

  .btn{ height: var(--btn-h); display:inline-flex; align-items:center; gap:8px; border-radius: var(--btn-r); }
  .btn-soft{ background:#fff; border:1px solid var(--card-border); }
  .btn-soft:hover{ box-shadow: var(--card-shadow); }
  .btn-icon{ width: var(--btn-h); height: var(--btn-h); padding:0; border-radius: var(--btn-r); background:#fff; border:1px solid var(--card-border); display:inline-flex; align-items:center; justify-content:center; }
  .ic{ font-size: var(--ic); }
  .fa-fw{ width: 1.25em; text-align: center; }

  .page-hero,
  .stat-card,
  .lead-card,
  .filter-card,
  .offcanvas-card .card {
    background:#fff;
    border:1px solid var(--card-border);
    border-radius: var(--card-radius);
  }
  .page-hero{ padding: 22px; }
  .stat-card{ transition:.2s ease; }
  .stat-card:hover{ transform: translateY(-2px); box-shadow: var(--card-shadow); }
  .lead-card{ height:100%; transition:.2s ease; }
  .lead-card:hover{ transform: translateY(-3px); box-shadow: var(--card-shadow); }

  .lead-card.overdue{ border-color: var(--danger-300); box-shadow: 0 0 0 3px var(--danger-200); }

  .avatar{
    width:36px;height:36px;border-radius:50%;
    background:#f3f6fa;display:inline-flex;align-items:center;justify-content:center;
    font-weight:700;color:#64748b;border:1px solid #eef2f7;
  }

  .lead-chip{ border-radius:999px;padding:4px 12px;font-size:.78rem;font-weight:700;border:1px solid #e5e7eb; height: var(--btn-h); display:inline-flex; align-items:center; gap:8px; }
  .chip-new{ background:#eef2f7; color:#0f172a; }
  .chip-follow{ background:#e6fbff; color:#164e63; }
  .chip-accept{ background:#e9fbf0; color:#166534; }
  .chip-reject{ background:#ffe9e9; color:#991b1b; }
  .chip-lost{ background:#f1f3f5; color:#374151; }

  .offcanvas-card .offcanvas-body{ background:#fff; }
  .text-trim-2{ display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden; }

  .pagination-wrap{ display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap; padding:12px 0; }
  .pagination{ list-style:none;display:flex;gap:6px;margin:0;padding:0; }
  .pagination .page-link{
    display:inline-flex;align-items:center;justify-content:center;
    min-width:38px;height:38px;padding:0 12px;border:1px solid var(--card-border);
    border-radius:10px;background:#fff;text-decoration:none;color:#334155;font-weight:600;
  }
  .pagination .page-link:hover{ box-shadow: var(--card-shadow); }
  .pagination .active .page-link{ background:#111827;color:#fff;border-color:#111827; }
  .pagination .disabled .page-link{ opacity:.5;pointer-events:none; }
</style>

<div class="container-xxl py-3">

  {{-- HERO --}}
  <div class="page-hero mb-3">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
      <div>
        <h4 class="mb-1">Leads Management</h4>
        <div class="text-muted small">Track statuses, calls, comments, assignments, and next follow-ups</div>
      </div>
      <div class="toolbar d-flex gap-2">
        <a href="{{ route('dashboard.leads.export', request()->query()) }}" class="btn btn-soft">
          <i class="fa fa-file-export ic fa-fw"></i> Export Excel
        </a>
        <button class="btn btn-soft text-success" data-bs-toggle="offcanvas" data-bs-target="#offcanvasImport">
          <i class="fa fa-file-import ic fa-fw"></i> Import
        </button>
        <button class="btn btn-dark" data-bs-toggle="offcanvas" data-bs-target="#offcanvasCreate">
          <i class="fa fa-plus ic fa-fw"></i> New Lead
        </button>
      </div>
    </div>
  </div>

  {{-- FILTERS --}}
  <div class="filter-card card mb-3 shadow-sm border-0">
    <div class="card-body">
      <form class="row g-2 align-items-end" method="get">
        <div class="col-md-3">
          <label class="form-label small text-muted">Search</label>
          <input name="q" value="{{ $filters['q'] ?? '' }}" class="form-control" placeholder="Search by name/email/phone/job">
        </div>

        <div class="col-md-2">
          <label class="form-label small text-muted">Status</label>
          <select name="status" class="form-select">
            <option value="">All statuses</option>
            @foreach ($statuses as $st)
              <option value="{{ $st }}" @selected(($filters['status'] ?? '')===$st)>{{ ucfirst(str_replace('_',' ', $st)) }}</option>
            @endforeach
          </select>
        </div>

        <div class="col-md-3">
          <label class="form-label small text-muted">Assigned to (Agent)</label>
          <select name="assigned_user_id" class="form-select">
            <option value="">All agents</option>
            @foreach($usersForFilters as $u)
              <option value="{{$u->id}}" @selected(($filters['assigned_user_id'] ?? '') == $u->id)>{{ $u->name }} {{ $u->email ? "($u->email)" : '' }}</option>
            @endforeach
          </select>
        </div>

        <div class="col-md-2">
          <label class="form-label small text-muted">From</label>
          <input type="date" name="from" value="{{ $filters['from'] ?? '' }}" class="form-control">
        </div>

        <div class="col-md-2">
          <label class="form-label small text-muted">To</label>
          <input type="date" name="to" value="{{ $filters['to'] ?? '' }}" class="form-control">
        </div>

        <div class="col-md-2 ms-auto d-flex gap-2">
          <a href="{{ request()->url() }}" class="btn btn-soft w-100"><i class="fa fa-rotate-left ic fa-fw"></i> Reset</a>
          <button class="btn btn-primary w-100" title="Filter"><i class="fa fa-filter ic fa-fw"></i> Filter</button>
        </div>
      </form>
    </div>
  </div>

  {{-- STATS --}}
  <div class="row g-3 mb-3">
    @foreach ($userStats as $stat)
      <div class="col-sm-6 col-lg-4">
        <div class="stat-card p-3 h-100">
          <div class="d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center gap-2">
              <div class="avatar">{{ mb_substr($stat->name,0,1) }}</div>
              <div>
                <div class="fw-semibold">{{ $stat->name }}</div>
                <div class="text-muted small">Total Leads: {{ $stat->total_leads }}</div>
              </div>
            </div>
            <div class="text-end small">
              <span class="badge bg-secondary me-1">New {{ $stat->cnt_new }}</span>
              <span class="badge bg-info text-dark me-1">F/U {{ $stat->cnt_follow_up }}</span>
              <span class="badge bg-success me-1">Acc {{ $stat->cnt_accept }}</span>
              <span class="badge bg-danger me-1">Rej {{ $stat->cnt_reject }}</span>
              <span class="badge bg-dark me-1">Lost {{ $stat->cnt_lost }}</span>
              <span class="badge bg-primary me-1"><i class="fa fa-comment ic fa-fw me-1"></i>{{ $stat->comments_count ?? 0 }}</span>
              <span class="badge bg-warning text-dark"><i class="fa fa-phone ic fa-fw me-1"></i>{{ $stat->calllogs_count ?? 0 }}</span>
            </div>
          </div>
        </div>
      </div>
    @endforeach
  </div>

  {{-- LEADS GRID --}}
  @php
    $chipClass = [
      'new' => 'chip-new', 'follow_up' => 'chip-follow',
      'accept' => 'chip-accept', 'reject' => 'chip-reject', 'lost' => 'chip-lost'
    ];
    $iconMap = [
      'new'=>'fa-star','follow_up'=>'fa-arrows-rotate','accept'=>'fa-circle-check',
      'reject'=>'fa-circle-xmark','lost'=>'fa-circle-minus'
    ];
  @endphp

  @if($leads->count())
    <div class="row g-3">
      @foreach ($leads as $lead)
        @php
          $isOverdue = $lead->next_call_at && in_array($lead->status,['new','follow_up']) && \Carbon\Carbon::parse($lead->next_call_at)->lt(now());
          $locked = in_array($lead->status,['accept','reject']);

          // Split phone into country code + local for the Edit form (UI only)
          $rawPhone = (string)($lead->phone ?? '');
          $ccPrefill = '+20'; $localPrefill = '';
          if (preg_match('/^\s*(\+\d{1,3})\s*[-\s]?(.+)$/', $rawPhone, $m)) {
              $ccPrefill = $m[1];
              $localPrefill = trim($m[2]);
          } elseif ($rawPhone !== '') {
              if (preg_match('/^\s*(\d{1,3})\s*[-\s]?(.+)$/', $rawPhone, $m2)) {
                  $ccPrefill = $m2[1];
                  $localPrefill = trim($m2[2]);
              } else {
                  $localPrefill = $rawPhone;
              }
          }

          // WhatsApp link
          $ccDigits   = preg_replace('/\D+/', '', $ccPrefill ?? '');
          $localDigits= preg_replace('/\D+/', '', $localPrefill ?? '');
          $waNumber = $ccDigits && $localDigits ? ($ccDigits.$localDigits) : preg_replace('/\D+/', '', $rawPhone);
          $waHref = $waNumber ? "https://wa.me/{$waNumber}" : null;
          $telHref = preg_replace('/[^+\d]/', '', $rawPhone);
        @endphp
        <div class="col-12 col-md-6 col-xl-4">
          <div class="lead-card p-3 {{ $isOverdue ? 'overdue' : '' }}">
            <div class="d-flex justify-content-between align-items-start mb-2">
              <span class="lead-chip {{ $chipClass[$lead->status] ?? 'chip-new' }}">
                <i class="fa {{ $iconMap[$lead->status] ?? 'fa-tag' }} ic fa-fw"></i>
                {{ ucfirst(str_replace('_',' ',$lead->status)) }}
                @if($isOverdue)
                  <span class="ms-1 text-danger small"><i class="fa fa-circle-exclamation ic fa-fw"></i> Overdue</span>
                @endif
              </span>

              <div class="dropdown">
                <button class="btn-icon" data-bs-toggle="dropdown" aria-expanded="false" title="Options">
                  <i class="fa fa-ellipsis ic fa-fw"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                  <li><hr class="dropdown-divider"></li>

                  @if(!$locked)
                    @foreach (['new','follow_up','accept','reject','lost'] as $st)
                      <li>
                        <form action="{{ route('dashboard.leads.status',$lead) }}" method="post">
                          @csrf
                          <input type="hidden" name="status" value="{{ $st }}">
                          <button class="dropdown-item" type="submit">
                            <i class="fa fa-tag ic fa-fw me-1"></i> {{ ucfirst(str_replace('_',' ',$st)) }}
                          </button>
                        </form>
                      </li>
                    @endforeach
                    <li><hr class="dropdown-divider"></li>
                    <li>
                      <form action="{{ route('dashboard.leads.accept',$lead) }}" method="post">
                        @csrf
                        <button class="dropdown-item text-success"><i class="fa fa-check ic fa-fw me-1"></i> Accept & Convert to Client</button>
                      </form>
                    </li>
                  @else
                    <li>
                      <button class="dropdown-item" type="button" data-bs-toggle="modal" data-bs-target="#statusLocked{{$lead->id}}">
                        <i class="fa fa-lock ic fa-fw me-1"></i> Status is final
                      </button>
                    </li>
                  @endif

                  <li><hr class="dropdown-divider"></li>
                  <li>
                    <form action="{{ route('dashboard.leads.destroy',$lead) }}" method="post" onsubmit="return confirm('Delete this lead?');">
                      @csrf @method('DELETE')
                      <button class="dropdown-item text-danger"><i class="fa fa-trash ic fa-fw me-1"></i> Delete</button>
                    </form>
                  </li>
                </ul>
              </div>
            </div>

            <div class="d-flex align-items-center gap-3 mb-2">
              <div class="avatar">{{ mb_substr($lead->name,0,1) }}</div>
              <div>
                <div class="fw-semibold">{{ $lead->name }}</div>
                <div class="text-muted small">{{ $lead->job ?: '—' }}</div>
              </div>
            </div>

            <div class="mb-2 small">
              <div class="mb-1">
                <i class="fa fa-envelope ic fa-fw me-1"></i>
                <a href="mailto:{{ $lead->email }}">{{ $lead->email ?: '—' }}</a>
              </div>

              <div>
                <i class="fa-brands fa-whatsapp ic fa-fw me-1"></i>
                @if($waHref)
                  <a href="{{ $waHref }}" target="_blank" rel="noopener">
                    {{ $rawPhone ?: '—' }}
                  </a>
                @elseif($telHref)
                  <a href="tel:{{ $telHref }}">{{ $rawPhone ?: '—' }}</a>
                @else
                  <span class="text-muted">—</span>
                @endif
              </div>
            </div>

            {{-- QR (accepted only) --}}
            @php
              $leadQrId  = $lead->qrcode_id ?? $lead->qecode_id ?? null;
              $qrFile    = $leadQrId ? ($qrMap[$leadQrId] ?? null) : null;  // filename.png
              $qrImgUrl  = $qrFile ? asset('public/qrcodes/'.$qrFile) : null;
            @endphp
            @if($lead->status === 'accept' && $qrImgUrl)
              <div class="mb-3">
                <button class="btn btn-soft w-100" data-bs-toggle="modal" data-bs-target="#qrForLead{{$lead->id}}">
                  <i class="fa fa-qrcode ic fa-fw"></i> Show QR
                </button>
              </div>
            @endif

            <div class="mb-2 small">
              <div class="mb-1 text-muted"><i class="fa fa-calendar-check ic fa-fw me-1"></i>
                Next follow-up:
                <span class="{{ $isOverdue ? 'text-danger fw-semibold' : '' }}">
                  {{ $lead->next_call_at ? \Carbon\Carbon::parse($lead->next_call_at)->format('Y-m-d H:i') : '—' }}
                </span>
              </div>
            </div>

            <div class="mb-2 small text-muted">
              <i class="fa fa-user-tie ic fa-fw me-1"></i> Assigned to: <b>{{ $lead->assignedUser?->name ?? 'Unassigned' }}</b>
              @if(Schema::hasColumn((new \App\Models\Lead)->getTable(), 'assigned_at') && $lead->assigned_at)
                <span class="ms-2"><i class="fa fa-clock ic fa-fw me-1"></i>{{ \Carbon\Carbon::parse($lead->assigned_at)->format('Y-m-d H:i') }}</span>
              @endif
            </div>

            <div class="mb-3">
              <div class="text-muted small mb-1"><i class="fa fa-comment ic fa-fw me-1"></i> Latest Comment</div>
              <div class="text-trim-2">{{ optional($lead->comments->first())->comment ?? '—' }}</div>
            </div>

            <div class="d-flex justify-content-between align-items-center">
              <div class="small text-muted">
                <i class="fa fa-id-badge ic fa-fw me-1"></i> #{{ $lead->id }}
              </div>
              <div class="d-flex gap-2">
                @if($waHref)
                  <a class="btn-icon" href="{{ $waHref }}" target="_blank" rel="noopener" title="WhatsApp">
                    <i class="fa-brands fa-whatsapp ic fa-fw"></i>
                  </a>
                @endif
                @if($lead->status === 'accept' && $qrImgUrl)
                  <button class="btn-icon" data-bs-toggle="modal" data-bs-target="#qrForLead{{$lead->id}}" title="QR Code">
                    <i class="fa fa-qrcode ic fa-fw"></i>
                  </button>
                @endif
                <a class="btn-icon" href="{{ route('dashboard.leads.show',$lead) }}" title="View">
                  <i class="fa fa-eye ic fa-fw"></i>
                </a>
                <button class="btn-icon" data-bs-toggle="offcanvas" data-bs-target="#offcanvasComment{{$lead->id}}" title="Comment">
                  <i class="fa fa-comment-dots ic fa-fw"></i>
                </button>
                <button class="btn-icon" data-bs-toggle="offcanvas" data-bs-target="#offcanvasCall{{$lead->id}}" title="Log Call">
                  <i class="fa fa-phone ic fa-fw"></i>
                </button>
                <button class="btn-icon" data-bs-toggle="offcanvas" data-bs-target="#offcanvasEdit{{$lead->id}}" title="Edit">
                  <i class="fa fa-pen ic fa-fw"></i>
                </button>
              </div>
            </div>
          </div>
        </div>

        {{-- Offcanvas: Assign --}}
        <div class="offcanvas offcanvas-end offcanvas-card" tabindex="-1" id="offcanvasAssign{{$lead->id}}" aria-labelledby="offcanvasAssignLabel{{$lead->id}}">
          <div class="offcanvas-header">
            <h5 id="offcanvasAssignLabel{{$lead->id}}">Assign Lead — {{ $lead->name }}</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
          </div>
          <div class="offcanvas-body">
            <div class="card">
              <div class="card-body">
                <form method="post" action="{{ route('dashboard.leads.assign',$lead) }}">
                  @csrf
                  <label class="form-label">Select Agent</label>
                  <select class="form-select mb-3" name="assigned_user_id" required>
                    <option value="" disabled selected>Choose...</option>
                    @foreach($usersForFilters as $u)
                      <option value="{{$u->id}}" @selected($lead->assigned_user_id == $u->id)>{{ $u->name }} {{ $u->email ? "($u->email)" : '' }}</option>
                    @endforeach
                  </select>
                  <div class="text-end">
                    <button class="btn btn-dark"><i class="fa fa-user-check ic fa-fw me-1"></i> Assign</button>
                  </div>
                </form>
                <div class="form-text mt-2">Assigner & time are tracked automatically (if columns exist).</div>
              </div>
            </div>
          </div>
        </div>

        {{-- Offcanvas: Comment --}}
        <div class="offcanvas offcanvas-end offcanvas-card" tabindex="-1" id="offcanvasComment{{$lead->id}}" aria-labelledby="offcanvasCommentLabel{{$lead->id}}">
          <div class="offcanvas-header">
            <h5 id="offcanvasCommentLabel{{$lead->id}}">Comment on {{ $lead->name }}</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
          </div>
          <div class="offcanvas-body">
            <div class="card">
              <div class="card-body">
                <form method="post" action="{{ route('dashboard.leads.comments.store',$lead) }}">
                  @csrf
                  <div class="mb-3">
                    <label class="form-label">Comment</label>
                    <textarea name="comment" class="form-control" rows="5" required placeholder="Write your comment here"></textarea>
                  </div>
                  <div class="mb-3">
                    <label class="form-label small text-muted">Next follow-up (optional)</label>
                    <input type="datetime-local" name="next_call_at" class="form-control">
                  </div>
                  <div class="text-end">
                    <button class="btn btn-dark"><i class="fa fa-paper-plane ic fa-fw me-1"></i> Save</button>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>

        {{-- Offcanvas: Call --}}
        <div class="offcanvas offcanvas-end offcanvas-card" tabindex="-1" id="offcanvasCall{{$lead->id}}" aria-labelledby="offcanvasCallLabel{{$lead->id}}">
          <div class="offcanvas-header">
            <h5 id="offcanvasCallLabel{{$lead->id}}">Log Call — {{ $lead->name }}</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
          </div>
          <div class="offcanvas-body">
            <div class="card">
              <div class="card-body">
                @php $locked = in_array($lead->status,['accept','reject']); @endphp
                @if($locked)
                  <div class="alert alert-light border d-flex align-items-center" role="alert" style="border-radius:14px">
                    <i class="fa fa-lock ic fa-fw me-2"></i>
                    <div>Status is final (<b>{{ ucfirst(str_replace('_',' ',$lead->status)) }}</b>) — logging a call will not change the status.</div>
                  </div>
                @else
                  <div class="small text-muted mb-2"><i class="fa fa-circle-info ic fa-fw me-1"></i>Logging a call will automatically set status to <b>Follow up</b>.</div>
                @endif
                <form method="post" action="{{ route('dashboard.leads.calllogs.store',$lead) }}">
                  @csrf
                  <div class="row g-2">
                    <div class="col-12">
                      <label class="form-label">Call Time</label>
                      <input type="datetime-local" name="called_at" class="form-control" required>
                    </div>
                    <div class="col-6">
                      <label class="form-label">Duration (sec)</label>
                      <input type="number" min="0" name="duration_sec" class="form-control" value="0">
                    </div>
                    <div class="col-6">
                      <label class="form-label">Outcome</label>
                      <select name="outcome" class="form-select">
                        <option value="answered">Answered</option>
                        <option value="no_answer">No Answer</option>
                        <option value="busy">Busy</option>
                        <option value="wrong_number">Wrong Number</option>
                        <option value="voicemail">Voicemail</option>
                      </select>
                    </div>
                    <div class="col-12">
                      <label class="form-label">Notes</label>
                      <textarea name="notes" class="form-control" rows="3"></textarea>
                    </div>
                    <div class="col-12">
                      <label class="form-label small text-muted">Next follow-up</label>
                      <input type="datetime-local" name="next_call_at" class="form-control">
                    </div>
                  </div>
                  <div class="text-end mt-3">
                    <button class="btn btn-dark"><i class="fa fa-save ic fa-fw me-1"></i> Save Log</button>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>

        {{-- Offcanvas: Edit Lead --}}
        <div class="offcanvas offcanvas-end offcanvas-card" tabindex="-1" id="offcanvasEdit{{$lead->id}}" aria-labelledby="offcanvasEditLabel{{$lead->id}}">
          <div class="offcanvas-header">
            <h5 id="offcanvasEditLabel{{$lead->id}}">Edit Lead</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
          </div>
          <div class="offcanvas-body">
            <div class="card">
              <div class="card-body">
                @php $locked = in_array($lead->status,['accept','reject']); @endphp
                @if($locked)
                  <div class="alert alert-light border d-flex align-items-center" role="alert" style="border-radius:14px">
                    <i class="fa fa-lock ic fa-fw me-2"></i>
                    <div>Status is final (<b>{{ ucfirst(str_replace('_',' ',$lead->status)) }}</b>) — it cannot be changed here.</div>
                  </div>
                @endif
                <form method="post" action="{{ route('dashboard.leads.update',$lead) }}" class="js-phone-joiner">
                  @csrf @method('PUT')
                  <input type="hidden" name="phone" value="{{ $rawPhone }}">
                  <div class="row g-2">
                    <div class="col-12">
                      <label class="form-label">Name</label>
                      <input name="name" value="{{ $lead->name }}" class="form-control" required>
                    </div>
                    <div class="col-6">
                      <label class="form-label">Email</label>
                      <input name="email" value="{{ $lead->email }}" class="form-control" placeholder="email">
                    </div>
                    <div class="col-6">
                      <label class="form-label">Phone</label>
                      <div class="input-group">
                        <input type="text" class="form-control" name="country_code_ui" value="{{ $ccPrefill }}" placeholder="+20 أو 20" style="max-width:110px">
                        <input type="text" class="form-control" name="local_phone_ui" value="{{ $localPrefill }}" placeholder="123456789">
                      </div>
                      <div class="form-text">سيتم ضم الحقلين وإرسالهما في مفتاح <code>phone</code> كما كُتبا (بـ + أو بدون +).</div>
                    </div>
                    <div class="col-12">
                      <label class="form-label">Job</label>
                      <input name="job" value="{{ $lead->job }}" class="form-control" placeholder="job">
                    </div>
                    <div class="col-12">
                      <label class="form-label">Status</label>
                      <select name="status" class="form-select" {{ $locked ? 'disabled' : '' }}>
                        @foreach (['new','follow_up','accept','reject','lost'] as $st)
                          <option value="{{ $st }}" @selected($lead->status==$st)>{{ ucfirst(str_replace('_',' ',$st)) }}</option>
                        @endforeach
                      </select>
                      @if($locked)
                        <input type="hidden" name="status" value="{{ $lead->status }}">
                      @endif
                    </div>
                    <div class="col-12">
                      <label class="form-label">Source</label>
                      <input name="source" value="{{ $lead->source }}" class="form-control" placeholder="source">
                    </div>
                    <div class="col-12">
                      <label class="form-label">Notes</label>
                      <textarea name="notes" class="form-control" rows="3" placeholder="notes">{{ $lead->notes }}</textarea>
                    </div>
                    <div class="col-12">
                      <label class="form-label small text-muted">Next follow-up</label>
                      <input type="datetime-local" name="next_call_at" class="form-control" value="{{ $lead->next_call_at ? \Carbon\Carbon::parse($lead->next_call_at)->format('Y-m-d\TH:i') : '' }}">
                    </div>
                  </div>
                  <div class="text-end mt-3">
                    <button class="btn btn-warning"><i class="fa fa-pen ic fa-fw me-1"></i> Update</button>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>

        {{-- Modal: Status Locked --}}
        <div class="modal fade" id="statusLocked{{$lead->id}}" tabindex="-1" aria-labelledby="statusLockedLabel{{$lead->id}}" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border-radius:20px; border:1px solid var(--card-border);">
              <div class="modal-header">
                <h6 id="statusLockedLabel{{$lead->id}}" class="mb-0">
                  <i class="fa fa-lock ic fa-fw me-1"></i> Status is final
                </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
              </div>
              <div class="modal-body">
                This lead status is <strong>{{ ucfirst(str_replace('_',' ',$lead->status)) }}</strong>, so
                <strong>you cannot change it here</strong>. You can only add comments or call logs.
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-dark" data-bs-dismiss="modal"><i class="fa fa-check ic fa-fw"></i> Got it</button>
              </div>
            </div>
          </div>
        </div>

        {{-- Modal: QR (only if accepted & QR exists) --}}
        @if($lead->status === 'accept' && $qrImgUrl)
          <div class="modal fade" id="qrForLead{{$lead->id}}" tabindex="-1" aria-labelledby="qrForLeadLabel{{$lead->id}}" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
              <div class="modal-content" style="border-radius:20px; border:1px solid var(--card-border);">
                <div class="modal-header">
                  <h5 class="modal-title" id="qrForLeadLabel{{$lead->id}}">
                    <i class="fa fa-qrcode me-2"></i> QR Code — {{ $lead->name }}
                  </h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center">
                  <img src="{{ $qrImgUrl }}" alt="QR" class="qr-img img-fluid">
                </div>
                <div class="modal-footer d-flex justify-content-between">
                  <a href="{{ $qrImgUrl }}" target="_blank" class="btn btn-soft">
                    <i class="fa fa-up-right-from-square ic fa-fw"></i> Open
                  </a>
                  <a href="{{ $qrImgUrl }}" download class="btn btn-dark">
                    <i class="fa fa-download ic fa-fw"></i> Download PNG
                  </a>
                </div>
              </div>
            </div>
          </div>
        @endif

      @endforeach
    </div>

    {{-- PAGINATION (robust: always inherits filters) --}}
    @php
      $current = $leads->currentPage();
      $last    = $leads->lastPage();
      $total   = $leads->total();
      $perPage = $leads->perPage();
      $from    = $total > 0 ? (($current - 1) * $perPage + 1) : 0;
      $to      = min($total, $current * $perPage);
      $window  = 2;
      $start   = max(1, $current - $window);
      $end     = min($last, $current + $window);

      // دوال توليد الروابط مع الحفاظ على كل الفلاتر الحالية
      $fullWith = function(array $extra = []) {
          return request()->fullUrlWithQuery($extra);
      };
      $pageUrl = function(int $page) use ($fullWith) {
          return $fullWith(['page' => $page]);
      };
      $urlFirst = $current == 1    ? '#' : $pageUrl(1);
      $urlPrev  = $current == 1    ? '#' : $pageUrl(max(1, $current-1));
      $urlNext  = $current == $last? '#' : $pageUrl(min($last, $current+1));
      $urlLast  = $current == $last? '#' : $pageUrl($last);
    @endphp

    <div class="pagination-wrap">
      <div class="text-muted small">
        Showing {{ $from }}–{{ $to }} of {{ $total }}
      </div>
      <ul class="pagination">
        <li class="{{ $current == 1 ? 'disabled' : '' }}">
          <a class="page-link" href="{{ $urlFirst }}" title="First">
            <i class="fa fa-angles-left"></i>
          </a>
        </li>
        <li class="{{ $current == 1 ? 'disabled' : '' }}">
          <a class="page-link" href="{{ $urlPrev }}" title="Previous">
            <i class="fa fa-angle-left"></i>
          </a>
        </li>

        @if ($start > 1)
          <li><a class="page-link" href="{{ $pageUrl(1) }}">1</a></li>
          @if ($start > 2)
            <li class="disabled"><span class="page-link">…</span></li>
          @endif
        @endif

        @for ($i = $start; $i <= $end; $i++)
          <li class="{{ $i == $current ? 'active' : '' }}">
            <a class="page-link" href="{{ $i == $current ? '#' : $pageUrl($i) }}">{{ $i }}</a>
          </li>
        @endfor

        @if ($end < $last)
          @if ($end < $last - 1)
            <li class="disabled"><span class="page-link">…</span></li>
          @endif
          <li><a class="page-link" href="{{ $pageUrl($last) }}">{{ $last }}</a></li>
        @endif

        <li class="{{ $current == $last ? 'disabled' : '' }}">
          <a class="page-link" href="{{ $urlNext }}" title="Next">
            <i class="fa fa-angle-right"></i>
          </a>
        </li>
        <li class="{{ $current == $last ? 'disabled' : '' }}">
          <a class="page-link" href="{{ $urlLast }}" title="Last">
            <i class="fa fa-angles-right"></i>
          </a>
        </li>
      </ul>
    </div>
  @else
    <div class="card p-5 text-center text-muted">No data</div>
  @endif

</div>

{{-- Offcanvas: Create Lead --}}
<div class="offcanvas offcanvas-end offcanvas-card" tabindex="-1" id="offcanvasCreate" aria-labelledby="offcanvasCreateLabel">
  <div class="offcanvas-header">
    <h5 id="offcanvasCreateLabel">New Lead</h5>
    <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
  </div>
  <div class="offcanvas-body">
    <div class="card">
      <div class="card-body">
        <form method="post" action="{{ route('dashboard.leads.store') }}" class="js-phone-joiner">
          @csrf
          <input type="hidden" name="phone" value="">
          <div class="row g-2">
            <div class="col-12">
              <label class="form-label">Name</label>
              <input name="name" class="form-control" required>
            </div>
            <div class="col-6">
              <label class="form-label">Email</label>
              <input name="email" class="form-control" placeholder="email">
            </div>
            <div class="col-12">
              <label class="form-label">Phone</label>
              <div class="input-group">
                <input type="text" class="form-control" name="country_code_ui" value="+20" placeholder="+20 أو 20" style="max-width:110px">
                <input type="text" class="form-control" name="local_phone_ui" placeholder="123456789">
              </div>
            </div>
            <div class="col-12">
              <label class="form-label">Job</label>
              <input name="job" class="form-control" placeholder="job">
            </div>
            <div class="col-12">
              <label class="form-label">Status</label>
              <select name="status" class="form-select">
                @foreach (['new','follow_up','accept','reject','lost'] as $st)
                  <option value="{{ $st }}">{{ ucfirst(str_replace('_',' ',$st)) }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-12">
              <label class="form-label">Source</label>
              <input name="source" class="form-control" placeholder="source">
            </div>
            <div class="col-12">
              <label class="form-label">Notes</label>
              <textarea name="notes" class="form-control" rows="3" placeholder="notes"></textarea>
            </div>
            <div class="col-12">
              <label class="form-label small text-muted">Next follow-up (optional)</label>
              <input type="datetime-local" name="next_call_at" class="form-control">
            </div>
          </div>
          <div class="text-end mt-3">
            <button class="btn btn-dark"><i class="fa fa-plus ic fa-fw me-1"></i> Save</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

{{-- Offcanvas: Import --}}
<div class="offcanvas offcanvas-end offcanvas-card" tabindex="-1" id="offcanvasImport" aria-labelledby="offcanvasImportLabel">
  <div class="offcanvas-header">
    <h5 id="offcanvasImportLabel">Import Leads from Excel</h5>
    <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
  </div>
  <div class="offcanvas-body">
    <div class="card">
      <div class="card-body">
        <form method="post" action="{{ route('dashboard.leads.import') }}" enctype="multipart/form-data">
          @csrf
          <p class="small text-muted mb-2">
            Expected headers: <code>name, email, phone, job, status, assigned_user_email, source, notes</code>
          </p>
          <input type="file" name="file" class="form-control mb-3" required accept=".xlsx,.xls">
          <div class="text-end">
            <button class="btn btn-success"><i class="fa fa-file-import ic fa-fw me-1"></i> Import</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

{{-- Legacy QR popup (session) --}}
@if(session('qr_popup'))
  @php $qr = session('qr_popup'); @endphp
  <div class="modal fade" id="qrPopupModal" tabindex="-1" aria-labelledby="qrPopupLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content" style="border-radius:20px; border:1px solid var(--card-border);">
        <div class="modal-header">
          <h5 class="modal-title" id="qrPopupLabel">
            <i class="fa fa-qrcode me-2"></i> QR Code — {{ $qr['lead_name'] ?? 'Accepted Lead' }}
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body text-center">
          @if(!empty($qr['qr_image_url']))
            <img src="{{ $qr['qr_image_url'] }}" alt="QR" class="qr-img img-fluid">
          @endif
          <div class="small text-muted mb-2">
            Short code: <code>{{ $qr['short_code'] ?? '' }}</code>
          </div>
          @if(!empty($qr['qr_image_url']))
            <div class="mb-3">
              <a href="{{ $qr['qr_image_url'] }}" target="_blank" class="small">{{ $qr['qr_url'] }}</a>
            </div>
          @endif
        </div>
        <div class="modal-footer d-flex justify-content-between"></div>
      </div>
    </div>
  </div>
@endif

{{-- JS: phone joiner + auto-open QR --}}
<script>
  (function(){
    function sanitizeCC(cc){ return (cc || '').toString().trim().replace(/[^+\d]/g, ''); }
    function sanitizeLocal(v){ return (v || '').toString().trim().replace(/[^\d]/g, ''); }
    function combine(cc, local){
      cc = sanitizeCC(cc); local = sanitizeLocal(local);
      if(!cc && !local) return '';
      if(!cc && local) return local;
      if(cc && !local) return cc;
      return cc + ' ' + local;
    }
    document.querySelectorAll('form.js-phone-joiner').forEach(function(form){
      form.addEventListener('submit', function(){
        var cc   = form.querySelector('input[name="country_code_ui"]');
        var loc  = form.querySelector('input[name="local_phone_ui"]');
        var hide = form.querySelector('input[name="phone"]');
        if(hide){ hide.value = combine(cc ? cc.value : '', loc ? loc.value : ''); }
      });
    });

    @if(session('qr_popup'))
      document.addEventListener('DOMContentLoaded', function(){
        if (window.bootstrap && document.getElementById('qrPopupModal')) {
          var m = new bootstrap.Modal(document.getElementById('qrPopupModal'));
          m.show();
        } else {
          var el = document.getElementById('qrPopupModal');
          if (el){ el.classList.add('show'); el.style.display = 'block'; }
        }
      });
    @endif
  })();
</script>

@endsection
