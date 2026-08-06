@extends('layouts.layoutMaster')

@section('title', __('event.page_title'))

@section('vendor-style')
  <link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css') }}">
@endsection

@section('content')
<div class="card">
  <div class="card-header d-flex justify-content-between align-items-center">
    <h5 class="mb-0">{{ __('event.page_title') }}</h5>
    {{-- <a href="{{ route('admin.events.create') }}" class="btn btn-primary">
      <i class="fas fa-plus me-1"></i> {{ __('event.add') }}
    </a> --}}
  </div>

  <div class="card-body">
    <div class="table-responsive">
      <table class="table table-bordered table-striped datatable">
        <thead class="table-light">
          <tr>
            <th>#</th>
            <th>{{ __('event.name') }}</th>
            <th>{{ __('event.date') }}</th>
            <th>{{ __('event.address') }}</th>
            <th>{{ __('event.status') }}</th>
            <th>{{ __('event.actions') }}</th>
          </tr>
        </thead>
        <tbody>
          @foreach($events as $key => $event)
            <tr>
              <td>{{ $key + 1 }}</td>
              <td>{{ app()->getLocale() == 'ar' ? $event->name_ar : $event->name_en }}</td>
              <td>{{ \Carbon\Carbon::parse($event->event_date)->format('Y-m-d H:i') }}</td>
              <td>{{ app()->getLocale() == 'ar' ? $event->address_ar : $event->address_en }}</td>
              <td>
                @if($event->active)
                  <span class="badge bg-success">{{ __('event.active') }}</span>
                @else
                  <span class="badge bg-danger">{{ __('event.inactive') }}</span>
                @endif
              </td>
              <td>
                <a href="{{ route('admin.events.edit', $event->id) }}" class="btn btn-sm btn-outline-primary">
                  <i class="fas fa-edit"></i>
                </a>
                {{-- <form action="{{ route('admin.events.destroy', $event->id) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('{{ __('event.confirm_delete') }}')">
                  @csrf
                  @method('DELETE')
                  <button class="btn btn-sm btn-outline-danger">
                    <i class="fas fa-trash-alt"></i>
                  </button>
                </form> --}}
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>

    <div class="mt-3">
      {{ $events->links() }}
    </div>
  </div>
</div>
@endsection

@section('vendor-script')
  <script src="{{ asset('assets/vendor/libs/datatables/jquery.dataTables.js') }}"></script>
  <script src="{{ asset('assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js') }}"></script>
@endsection

@section('page-script')
  <script>
    $(function () {
      $('.datatable').DataTable({
        paging: false,
        searching: false,
        info: false
      });
    });
  </script>
@endsection
