@extends('layouts.layoutMaster')

@section('title', __('Companies'))

@section('content')
<style>
    .pagination {
        display: flex;
        list-style: none;
        padding-left: 0;
    }

    .page-item {
        margin: 0 3px;
    }

    .page-link {
        display: block;
        padding: 8px 14px;
        border: 1px solid #dee2e6;
        border-radius: 4px;
        color: #007bff;
        text-decoration: none;
        background-color: #fff;
        transition: all 0.3s ease;
    }

    .page-link:hover {
        background-color: #f1f1f1;
        color: #0056b3;
    }

    .page-item.active .page-link {
        background-color: #007bff;
        color: white;
        border-color: #007bff;
    }

    .page-item.disabled .page-link {
        color: #6c757d;
        pointer-events: none;
        background-color: #f8f9fa;
        border-color: #dee2e6;
    }
</style>

<div class="container">
    <form method="GET" action="{{ route('dashboard.companies.index') }}" class="row g-2 align-items-end mb-3">
    <div class="col-md-4">
        <label class="form-label">{{ __('Search') }}</label>
        <input type="text" name="q" class="form-control"
               placeholder="{{ __('Search by Arabic/English name') }}"
               value="{{ request('q') }}">
    </div>

    <div class="col-md-2">
        <label class="form-label">{{ __('Status') }}</label>
        <select name="status" class="form-select">
            <option value="all">{{ __('All') }}</option>
            <option value="active"   @selected(request('status')==='active')>{{ __('Active') }}</option>
            <option value="inactive" @selected(request('status')==='inactive')>{{ __('Inactive') }}</option>
        </select>
    </div>

    <div class="col-md-2">
        <label class="form-label">{{ __('Min Votes') }}</label>
        <input type="number" name="min_votes" class="form-control" value="{{ request('min_votes') }}">
    </div>

    <div class="col-md-2">
        <label class="form-label">{{ __('Max Votes') }}</label>
        <input type="number" name="max_votes" class="form-control" value="{{ request('max_votes') }}">
    </div>

    <div class="col-md-2">
        <label class="form-label">{{ __('Per page') }}</label>
        <select name="per_page" class="form-select">
            @foreach ([10,20,30,50,100,200] as $n)
                <option value="{{ $n }}" @selected(request('per_page', 20)==$n)>{{ $n }}</option>
            @endforeach
        </select>
    </div>

    <div class="col-md-2 d-flex gap-2">
        <button type="submit" class="btn btn-secondary w-100">{{ __('Filter') }}</button>
        <a href="{{ route('dashboard.companies.index') }}" class="btn btn-light w-100">{{ __('Reset') }}</a>
    </div>
</form>

    <h4 class="mb-4">{{ __('Influncer List') }}</h4>

    <a href="{{ route('dashboard.companies.create') }}" class="btn btn-primary mb-3">
        + {{ __('Add Influncer') }}
    </a>

    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>#</th>
                <th>{{ __('Name (AR)') }}</th>
                <th>{{ __('Name (EN)') }}</th>
                <th>{{ __('Votes') }}</th>
                <th>{{ __('Status') }}</th>
                <th>{{ __('Image') }}</th>
                <th>{{ __('Actions') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($companies as $company)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $company->name_ar }}</td>
                    <td>{{ $company->name_en }}</td>
                    <td>{{ $company->count_vote }}</td>
                    <td>
                        @if ($company->active)
                            <span class="badge bg-success">{{ __('Active') }}</span>
                        @else
                            <span class="badge bg-danger">{{ __('Inactive') }}</span>
                        @endif
                    </td>
                    <td>
                        @if ($company->image)
                            <img src="{{ asset('public/'.$company->image) }}" alt="Image" width="60">
                        @endif
                    </td>
                   <td>
    <a href="{{ route('dashboard.companies.show', $company->id) }}" class="btn btn-info btn-sm">
        {{ __('Show') }}
    </a>

    <a href="{{ route('dashboard.companies.edit', $company->id) }}" class="btn btn-warning btn-sm">
        {{ __('Edit') }}
    </a>

    @if($company->active)
        <form action="{{ route('dashboard.companies.deactivate', $company->id) }}" method="POST" style="display:inline-block;">
            @csrf
            @method('PATCH')
            <button type="submit" class="btn btn-danger btn-sm">{{ __('Deactivate') }}</button>
        </form>
    @else
        <form action="{{ route('dashboard.companies.activate', $company->id) }}" method="POST" style="display:inline-block;">
            @csrf
            @method('PATCH')
            <button type="submit" class="btn btn-success btn-sm">{{ __('Activate') }}</button>
        </form>
    @endif

    {{-- زر الحذف --}}
    <form action="{{ route('dashboard.companies.destroy', $company->id) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('{{ __('Are you sure you want to delete this company?') }}')">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-outline-danger btn-sm">
            {{ __('Delete') }}
        </button>
    </form>
</td>

                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center">{{ __('No companies found.') }}</td>
                </tr>
            @endforelse
        </tbody>
    </table>
@if ($companies->hasPages())
    <nav class="mt-4">
        <ul class="pagination justify-content-center">

            {{-- زر السابق --}}
            @if ($companies->onFirstPage())
                <li class="page-item disabled"><span class="page-link">&laquo; السابق</span></li>
            @else
                <li class="page-item">
                    <a class="page-link" href="{{ $companies->previousPageUrl() }}" rel="prev">&laquo; السابق</a>
                </li>
            @endif

            {{-- الصفحات --}}
            @for ($page = 1; $page <= $companies->lastPage(); $page++)
                <li class="page-item {{ $companies->currentPage() == $page ? 'active' : '' }}">
                    <a class="page-link" href="{{ $companies->url($page) }}">{{ $page }}</a>
                </li>
            @endfor

            {{-- زر التالي --}}
            @if ($companies->hasMorePages())
                <li class="page-item">
                    <a class="page-link" href="{{ $companies->nextPageUrl() }}" rel="next">التالي &raquo;</a>
                </li>
            @else
                <li class="page-item disabled"><span class="page-link">التالي &raquo;</span></li>
            @endif

        </ul>
    </nav>
@endif

</div>
@endsection
