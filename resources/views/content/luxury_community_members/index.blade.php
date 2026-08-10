@extends('layouts.layoutMaster')

@section('title', __('luxury_community.title'))

@section('content')
<div class="container">
  <div class="d-flex align-items-center justify-content-between mb-3">
    <h2 class="mb-0">{{ __('luxury_community.title') }}</h2>
    <a href="{{ route('dashboard.luxury-members.create') }}" class="btn btn-success">
      {{ __('luxury_community.create') }}
    </a>
  </div>

  {{-- Tabs filter --}}
  @php $activeFilter = request('active'); @endphp
  <ul class="nav nav-pills mb-3">
    <li class="nav-item">
      <a class="nav-link {{ $activeFilter===null ? 'active' : '' }}" href="{{ route('dashboard.luxury-members.index') }}">
        {{ __('All') }}
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link {{ $activeFilter==='0' ? 'active' : '' }}" href="{{ route('dashboard.luxury-members.index', ['active' => 0]) }}">
        {{ __('Requests') }}
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link {{ $activeFilter==='1' ? 'active' : '' }}" href="{{ route('dashboard.luxury-members.index', ['active' => 1]) }}">
        {{ __('Active') }}
      </a>
    </li>
  </ul>

  <div class="table-responsive">
    <table class="table table-bordered table-striped align-middle">
      <thead>
        <tr>
          <th>#</th>
          <th>{{ __('luxury_community.name') }}</th>
          <th>{{ __('luxury_community.title_col') }}</th>
          <th>{{ __('luxury_community.company') }}</th>
          <th>{{ __('luxury_community.email') }}</th>
          <th>{{ __('luxury_community.phone') }}</th>
          <th>{{ __('luxury_community.image') }}</th>
          <th>{{ __('Status') }}</th>
          <th>{{ __('luxury_community.actions') }}</th>
        </tr>
      </thead>
      <tbody>
        @forelse($members as $member)
          <tr>
            <td>
              {{ ($members instanceof \Illuminate\Pagination\LengthAwarePaginator) ? $members->firstItem() + $loop->index : $loop->iteration }}
            </td>
            <td>{{ $member->name_ar }}</td>
            <td>{{ $member->title_ar }}</td>
            <td>{{ $member->company }}</td>
            <td>{{ $member->email ?? '-' }}</td>
            <td>{{ $member->phone ?? '-' }}</td>
            <td>
              @if($member->image)
                <img src="{{ asset('public/'.$member->image) }}" width="50" height="50" class="rounded" style="object-fit:cover;">
              @else
                -
              @endif
            </td>
            <td>
              @if($member->active)
                <span class="badge bg-success">{{ __('multi_media.status_active') }}</span>
              @else
                <span class="badge bg-secondary">{{ __('multi_media.status_inactive') }}</span>
              @endif
            </td>
            <td class="d-flex gap-1">
              {{-- Activate button (only when inactive) --}}
                <form action="{{ route('dashboard.luxury-members.activate', $member->id) }}" method="POST" onsubmit="return confirm('{{ __('Are you sure to activate?') }}')">
                  @csrf
                  @method('PATCH')
                  <button class="btn btn-sm btn-success">
                    {{ __('Activate') }}
                  </button>
                </form>

              <a href="{{ route('dashboard.luxury-members.edit', $member->id) }}" class="btn btn-sm btn-primary">
                {{ __('luxury_community.edit') }}
              </a>

              <form method="POST" action="{{ route('dashboard.luxury-members.destroy', $member->id) }}" onsubmit="return confirm('{{ __('luxury_community.confirm_delete') }}')" style="display:inline-block;">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-sm btn-danger">
                  {{ __('luxury_community.delete') }}
                </button>
              </form>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="9" class="text-center text-muted">{{ __('No data found') }}</td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  {{-- Pagination (إن وُجد) --}}
  @if($members instanceof \Illuminate\Pagination\LengthAwarePaginator)
    <div class="mt-3">
      {{ $members->withQueryString()->links() }}
    </div>
  @endif
</div>
@endsection
