@extends('layouts.layoutMaster')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center my-4">
        <h2>@lang('prizes.title')</h2>
        <a href="{{ route('dashboard.prizes.create') }}" class="btn btn-primary">@lang('prizes.add')</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>#</th>
                <th>@lang('prizes.name_ar')</th>
                <th>@lang('prizes.name_en')</th>
                <th>@lang('prizes.title_en')</th>
                <th>@lang('prizes.image')</th>
                <th>@lang('prizes.actions')</th>
            </tr>
        </thead>
        <tbody>
            @foreach($prizes as $index => $prize)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $prize->name_ar }}</td>
                    <td>{{ $prize->name_en }}</td>
                    <td>{{ $prize->title_en }}</td>
                    <td>
                        @if($prize->img)
                            <img src="{{ asset($prize->img) }}" width="60" height="60" class="rounded">
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('dashboard.prizes.edit', $prize->id) }}" class="btn btn-sm btn-warning">@lang('prizes.edit')</a>
                        <form action="{{ route('dashboard.prizes.destroy', $prize->id) }}" method="POST" class="d-inline-block"
                              onsubmit="return confirm('@lang("prizes.confirm_delete")')">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-danger">@lang('prizes.delete')</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{ $prizes->links() }}
</div>
@endsection
