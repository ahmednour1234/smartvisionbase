@extends('docs.layout')

@section('content')
@php
    $event = $company->event;
    $package = $company->package;
    $price = $package?->price ?? 0;
@endphp

<div class="header">
    <div>
        <div class="brand">Smart Vision CRM</div>
        <div class="muted">Proforma Invoice</div>
        <div class="muted">Issued: {{ $issuedAt->format('Y-m-d H:i') }}</div>
    </div>
    <div class="right">
        <div class="muted">Company</div>
        <div><strong>{{ $company->company_name }}</strong></div>
        <div class="muted">Country: {{ $company->country?->name ?? '-' }}</div>
    </div>
</div>

<div class="card">
    <h3 style="margin:0 0 8px 0;">Client Details</h3>
    <table>
        <tr>
            <th style="width: 25%;">Contact Person</th>
            <td>{{ $company->contact_person ?? '-' }}</td>
        </tr>
        <tr>
            <th>Mobile</th>
            <td>{{ $company->contact_mobile ?? '-' }}</td>
        </tr>
        <tr>
            <th>Email</th>
            <td>{{ $company->contact_email ?? '-' }}</td>
        </tr>
        <tr>
            <th>Owner</th>
            <td>{{ $company->owner?->name ?? 'Unclaimed' }}</td>
        </tr>
    </table>
</div>

<div class="card">
    <h3 style="margin:0 0 8px 0;">Event / Package</h3>
    <table>
        <tr>
            <th style="width: 25%;">Event</th>
            <td>{{ $event?->name ?? '-' }}</td>
        </tr>
        <tr>
            <th>Event Dates</th>
            <td>
                {{ $event?->start_date ? $event->start_date->format('Y-m-d') : '-' }}
                —
                {{ $event?->end_date ? $event->end_date->format('Y-m-d') : '-' }}
            </td>
        </tr>
        <tr>
            <th>Package</th>
            <td>{{ $package?->name ?? '-' }}</td>
        </tr>
    </table>

    <table>
        <thead>
            <tr>
                <th>Description</th>
                <th class="right">Amount</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>{{ $package?->name ?? 'Package' }} @if($event) ({{ $event->name }}) @endif</td>
                <td class="right">{{ number_format((float) $price, 2) }}</td>
            </tr>
        </tbody>
        <tfoot>
            <tr>
                <td class="right total">Total</td>
                <td class="right total">{{ number_format((float) $price, 2) }}</td>
            </tr>
        </tfoot>
    </table>
</div>

<div class="card">
    <h3 style="margin:0 0 8px 0;">Bank Details</h3>
    <div class="muted">Use the details below to complete payment.</div>
    <pre style="white-space: pre-wrap; margin-top: 10px;">{{ $event?->bank_details ?? 'N/A' }}</pre>
</div>

<div class="card">
    <h3 style="margin:0 0 8px 0;">Notes</h3>
    <div>{{ $company->notes ?? '—' }}</div>
</div>
@endsection
