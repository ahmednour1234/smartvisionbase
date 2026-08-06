@extends('layouts.layoutMaster')

@section('content')
<div class="container py-4">
    <h2 class="mb-4 fw-bold text-primary">{{ __('details') }}</h2>

    <div class="card shadow-sm">
        <div class="card-body">
            <ul class="list-group list-group-flush">
                <li class="list-group-item"><strong>{{ __('name') }}:</strong> {{ $lead->name }}</li>
                <li class="list-group-item"><strong>{{ __('phone') }}:</strong> {{ $lead->phone }}</li>
                <li class="list-group-item"><strong>{{ __('email') }}:</strong> {{ $lead->email }}</li>
                <li class="list-group-item"><strong>{{ __('campaign') }}:</strong> {{ $lead->campaign }}</li>
                <li class="list-group-item"><strong>{{ __('created_at') }}:</strong> {{ $lead->created_at }}</li>
            </ul>
        </div>
    </div>
</div>
@endsection
