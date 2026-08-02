@extends('layouts.layoutMaster')

@section('title', __('home_sections'))

@section('content')
<div class="container py-4">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">{{ __('home_sections') }}</h4>
    <a href="{{ route('dashboard.home_sections.create') }}" class="btn btn-primary">
      <i class="bi bi-plus-circle me-1"></i> {{ __('add_section') }}
    </a>
  </div>

  @if($sections->isEmpty())
    <div class="alert alert-info">{{ __('no_sections') }}</div>
  @else
    <div class="table-responsive">
      <table class="table table-striped align-middle">
        <thead>
          <tr>
            <th>#</th>
            <th>{{ __('title') }}</th>
            <th>{{ __('media') }}</th>
            <th>{{ __('order') }}</th>
            <th>{{ __('status') }}</th>
            <th>{{ __('actions') }}</th>
          </tr>
        </thead>
        <tbody>
          @foreach($sections as $section)
            <tr>
              <td>{{ $loop->iteration }}</td>
              <td>{{ $section->title[app()->getLocale()] ?? '-' }}</td>
              <td>
                @if(Str::endsWith($section->media_path, ['.mp4']))
                  <video width="120" controls>
                    <source src="{{ asset($section->media_path) }}" type="video/mp4">
                  </video>
                @else
                  <img src="{{ asset($section->media_path) }}" width="100" class="img-thumbnail" />
                @endif
              </td>
              <td>{{ $section->section_order }}</td>
              <td>
                <span class="badge {{ $section->is_active ? 'bg-success' : 'bg-secondary' }}">
                  {{ $section->is_active ? __('active') : __('inactive') }}
                </span>
              </td>
              <td>
                <a href="{{ route('dashboard.home_sections.edit', $section->id) }}" class="btn btn-sm btn-outline-primary">
                  <i class="bi bi-pencil-square"></i> {{ __('edit') }}
                </a>

                <form action="{{ route('dashboard.home_sections.toggle', $section->id) }}" method="POST" class="d-inline">
                  @csrf
                  @method('PUT')
                  <button type="submit" class="btn btn-sm btn-outline-{{ $section->is_active ? 'danger' : 'success' }}">
                    <i class="bi bi-power"></i>
                    {{ $section->is_active ? __('deactivate') : __('activate') }}
                  </button>
                </form>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  @endif
</div>
@endsection
