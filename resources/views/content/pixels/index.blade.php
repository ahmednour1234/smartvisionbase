@extends('layouts.layoutMaster')

@section('title', __('pixels.title'))

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4>{{ __('pixels.title') }}</h4>
        <a href="{{ route('dashboard.pixels.create') }}" class="btn btn-primary">
            <i class="fa fa-plus"></i> {{ __('pixels.create') }}
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>#</th>
                <th>{{ __('pixels.name') }}</th>
                <th>{{ __('pixels.pixel_id') }}</th>
                <th>{{ __('pixels.status') }}</th>
                <th>{{ __('pixels.actions') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($pixels as $pixel)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $pixel->name }}</td>
                <td>{{ $pixel->pixel_id }}</td>
                <td>
                    <form action="{{ route('dashboard.pixels.toggle-active', $pixel->id) }}" method="POST">
                        @csrf
                        <button class="btn btn-sm {{ $pixel->active ? 'btn-success' : 'btn-secondary' }}">
                            {{ $pixel->active ? __('pixels.active') : __('pixels.inactive') }}
                        </button>
                    </form>
                </td>
                <td>
                    <a href="{{ route('dashboard.pixels.edit', $pixel->id) }}" class="btn btn-warning btn-sm">{{ __('pixels.edit') }}</a>
                    <form action="{{ route('dashboard.pixels.destroy', $pixel->id) }}" method="POST" style="display:inline-block;">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-danger btn-sm" onclick="return confirm('{{ __('pixels.confirm_delete') }}')">{{ __('pixels.delete') }}</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{ $pixels->links() }}
</div>
@endsection
