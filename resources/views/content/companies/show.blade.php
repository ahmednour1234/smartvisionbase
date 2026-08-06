@extends('layouts.layoutMaster')

@section('title', __('Show Company'))

@section('content')
<div class="container">
    <h4 class="mb-4">{{ __('Company Details') }}</h4>

    <div class="card">
        <div class="card-body">

            <div class="mb-3">
                <strong>{{ __('Name (AR)') }}:</strong>
                <p>{{ $company->name_ar }}</p>
            </div>

            <div class="mb-3">
                <strong>{{ __('Name (EN)') }}:</strong>
                <p>{{ $company->name_en }}</p>
            </div>

            <div class="mb-3">
                <strong>{{ __('Title (AR)') }}:</strong>
                <p>{{ $company->title_ar }}</p>
            </div>

            <div class="mb-3">
                <strong>{{ __('Title (EN)') }}:</strong>
                <p>{{ $company->title_en }}</p>
            </div>

            <div class="mb-3">
                <strong>{{ __('Country') }}:</strong>
                <p>{{ $company->country }}</p>
            </div>

            <div class="mb-3">
                <strong>{{ __('Link') }}:</strong>
                <p><a href="{{ $company->link }}" target="_blank">{{ $company->link }}</a></p>
            </div>

            <div class="mb-3">
                <strong>{{ __('Votes') }}:</strong>
                <p>{{ $company->count_vote }}</p>
            </div>

            <div class="mb-3">
                <strong>{{ __('Status') }}:</strong>
                <p>
                    @if ($company->active)
                        <span class="badge bg-success">{{ __('Active') }}</span>
                    @else
                        <span class="badge bg-danger">{{ __('Inactive') }}</span>
                    @endif
                </p>
            </div>

            <div class="mb-3">
                <strong>{{ __('Description (AR)') }}:</strong>
                <p>{{ $company->description_ar }}</p>
            </div>

            <div class="mb-3">
                <strong>{{ __('Description (EN)') }}:</strong>
                <p>{{ $company->description_en }}</p>
            </div>

            <div class="mb-3">
                <strong>{{ __('Regulation') }}:</strong>
                <p>{{ $company->regulation }}</p>
            </div>

            <div class="mb-3">
                <strong>{{ __('Image') }}:</strong><br>
                @if ($company->image)
                    <img src="{{ asset($company->image) }}" alt="Image" width="150">
                @else
                    <span class="text-muted">{{ __('No Image') }}</span>
                @endif
            </div>

        </div>
    </div>

    <a href="{{ route('dashboard.companies.index') }}" class="btn btn-secondary mt-3">
        {{ __('Back to List') }}
    </a>
</div>
@endsection
