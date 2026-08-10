@extends('layouts.layoutMaster')

@section('title', __('Companies'))

@section('content')
<style>
    .pagination{display:flex;list-style:none;padding-left:0}
    .page-item{margin:0 3px}
    .page-link{display:block;padding:8px 14px;border:1px solid #dee2e6;border-radius:4px;color:#007bff;text-decoration:none;background:#fff;transition:all .3s ease}
    .page-link:hover{background:#f1f1f1;color:#0056b3}
    .page-item.active .page-link{background:#007bff;color:#fff;border-color:#007bff}
    .page-item.disabled .page-link{color:#6c757d;pointer-events:none;background:#f8f9fa;border-color:#dee2e6}
    .search-wrap{display:flex;gap:10px;align-items:center;margin-bottom:12px}
    .search-wrap input{max-width:360px}
    .search-meta{font-size:.9rem;color:#666}
</style>

@php
    // مسار JSON لكل الشركات (يتطلب Route باسم dashboard.companies.all)
    $allRoute = \Illuminate\Support\Facades\Route::has('dashboard.companies.all')
        ? route('dashboard.companies.all')
        : null;

    $i18n = [
        'show'        => __('Show'),
        'edit'        => __('Edit'),
        'deactivate'  => __('Deactivate'),
        'activate'    => __('Activate'),
        'delete'      => __('Delete'),
        'confirmDel'  => __('Are you sure you want to delete this company?'),
        'active'      => __('Active'),
        'inactive'    => __('Inactive'),
        'noResults'   => __('No companies found.'),
        'listTitle'   => __('Companies List'),
        'addCompany'  => __('Add Company'),
        'nameAr'      => __('Name (AR)'),
        'nameEn'      => __('Name (EN)'),
        'country'     => __('Country'),
        'votes'       => __('Votes'),
        'status'      => __('Status'),
        'image'       => __('Image'),
        'actions'     => __('Actions'),
        'searchPh'    => __('Search by name (AR/EN)…'),
        'searchHint'  => __('Searching all pages'),
        'prev'        => __('« Previous'),
        'next'        => __('Next »'),
    ];
@endphp

<div class="container">
    <h4 class="mb-2">{{ $i18n['listTitle'] }}</h4>

<form method="GET" class="row g-2 mb-3">
  <div class="col-md-3">
    <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="{{ __('Search name/country') }}">
  </div>
  <div class="col-md-2">
    <select name="status" class="form-control">
      <option value="">{{ __('Any status') }}</option>
      <option value="active"   @selected(request('status')==='active')>{{ __('Active') }}</option>
      <option value="inactive" @selected(request('status')==='inactive')>{{ __('Inactive') }}</option>
    </select>
  </div>
  <div class="col-md-2">
    <input type="text" name="country" value="{{ request('country') }}" class="form-control" placeholder="{{ __('Country') }}">
  </div>
  <div class="col-md-3">
    <div class="d-flex gap-2">
      <select name="sort" class="form-control">
        <option value="orders"      @selected(request('sort','orders')==='orders')>Orders</option>
        <option value="name_en"     @selected(request('sort')==='name_en')>Name (EN)</option>
        <option value="name_ar"     @selected(request('sort')==='name_ar')>Name (AR)</option>
        <option value="count_vote"  @selected(request('sort')==='count_vote')>Votes</option>
        <option value="created_at"  @selected(request('sort')==='created_at')>Created</option>
      </select>
      <select name="dir" class="form-control">
        <option value="asc"  @selected(request('dir','asc')==='asc')>ASC</option>
        <option value="desc" @selected(request('dir')==='desc')>DESC</option>
      </select>
    </div>
  </div>
  <div class="col-md-2">
    <select name="per_page" class="form-control">
      @foreach([10,20,30,50,100] as $n)
        <option value="{{ $n }}" @selected((int)request('per_page',20)===$n)>{{ $n }} / page</option>
      @endforeach
    </select>
  </div>
  <div class="col-12 mt-2">
    <button class="btn btn-primary">{{ __('Filter') }}</button>
    <a href="{{ route('dashboard.companies.index') }}" class="btn btn-outline-secondary">{{ __('Reset') }}</a>
  </div>
</form>

    <a href="{{ route('dashboard.companies.create') }}" class="btn btn-primary mb-3">
        + {{ $i18n['addCompany'] }}
    </a>

    <div class="table-responsive">
        <table class="table table-bordered table-striped" id="companies-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>{{ $i18n['nameAr'] }}</th>
                    <th>{{ $i18n['nameEn'] }}</th>
                    <th>{{ $i18n['country'] }}</th>
                    <th>{{ $i18n['votes'] }}</th>
                    <th>{{ $i18n['status'] }}</th>
                    <th>{{ $i18n['image'] }}</th>
                    <th>{{ $i18n['actions'] }}</th>
                </tr>
            </thead>
            <tbody id="companies-tbody">
                @forelse ($companies as $company)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $company->name_ar }}</td>
                        <td>{{ $company->name_en }}</td>
                        <td>{{ $company->country }}</td>
                        <td>{{ $company->count_vote }}</td>
                        <td>
                            @if ($company->active)
                                <span class="badge bg-success">{{ $i18n['active'] }}</span>
                            @else
                                <span class="badge bg-danger">{{ $i18n['inactive'] }}</span>
                            @endif
                        </td>
                        <td>
                            @if ($company->image)
                                <img src="{{ asset($company->image) }}" alt="Image" width="60">
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('dashboard.companies.show', $company->id) }}" class="btn btn-info btn-sm">
                                {{ $i18n['show'] }}
                            </a>

                            <a href="{{ route('dashboard.companies.edit', $company->id) }}" class="btn btn-warning btn-sm">
                                {{ $i18n['edit'] }}
                            </a>

                            @if($company->active)
                                <form action="{{ route('dashboard.companies.deactivate', $company->id) }}" method="POST" style="display:inline-block;">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-danger btn-sm">{{ $i18n['deactivate'] }}</button>
                                </form>
                            @else
                                <form action="{{ route('dashboard.companies.activate', $company->id) }}" method="POST" style="display:inline-block;">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-success btn-sm">{{ $i18n['activate'] }}</button>
                                </form>
                            @endif

                            <form action="{{ route('dashboard.companies.destroy', $company->id) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('{{ $i18n['confirmDel'] }}')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger btn-sm">
                                    {{ $i18n['delete'] }}
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="text-center">{{ $i18n['noResults'] }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination (تفضل ظاهرة مبدئيًا، بتتخبّى أثناء البحث) --}}
    @if ($companies->hasPages())
        <nav class="mt-4" id="pagination-wrap">
            <ul class="pagination justify-content-center">
                {{-- prev --}}
                @if ($companies->onFirstPage())
                    <li class="page-item disabled"><span class="page-link">{{ $i18n['prev'] }}</span></li>
                @else
                    <li class="page-item">
                        <a class="page-link" href="{{ $companies->previousPageUrl() }}" rel="prev">{{ $i18n['prev'] }}</a>
                    </li>
                @endif

                {{-- pages --}}
                @for ($page = 1; $page <= $companies->lastPage(); $page++)
                    <li class="page-item {{ $companies->currentPage() == $page ? 'active' : '' }}">
                        <a class="page-link" href="{{ $companies->url($page) }}">{{ $page }}</a>
                    </li>
                @endfor

                {{-- next --}}
                @if ($companies->hasMorePages())
                    <li class="page-item">
                        <a class="page-link" href="{{ $companies->nextPageUrl() }}" rel="next">{{ $i18n['next'] }}</a>
                    </li>
                @else
                    <li class="page-item disabled"><span class="page-link">{{ $i18n['next'] }}</span></li>
                @endif
            </ul>
        </nav>
    @endif

</div>

{{-- ======= JS: بحث في كل الشركات (مش الباجينشن) ======= --}}

@endsection
