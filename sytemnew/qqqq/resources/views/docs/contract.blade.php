@extends('docs.layout')

@section('content')
@php
    $event = $company->event;
    $package = $company->package;
@endphp

<div class="header">
    <div>
        <div class="brand">Smart Vision CRM</div>
        <div class="muted">Contract</div>
        <div class="muted">Issued: {{ $issuedAt->format('Y-m-d H:i') }}</div>
    </div>
    <div class="right">
        <div class="muted">Client</div>
        <div><strong>{{ $company->company_name }}</strong></div>
        <div class="muted">Country: {{ $company->country?->name ?? '-' }}</div>
    </div>
</div>

<div class="card">
    <h3 style="margin:0 0 8px 0;">Parties</h3>
    <table>
        <tr>
            <th style="width: 25%;">Provider</th>
            <td>Smart Vision</td>
        </tr>
        <tr>
            <th>Client</th>
            <td>{{ $company->company_name }}</td>
        </tr>
        <tr>
            <th>Representative</th>
            <td>{{ $company->contact_person ?? '-' }}</td>
        </tr>
        <tr>
            <th>Contact</th>
            <td>
                {{ $company->contact_mobile ?? '-' }}<br>
                {{ $company->contact_email ?? '-' }}
            </td>
        </tr>
    </table>
</div>

<div class="card">
    <h3 style="margin:0 0 8px 0;">Scope</h3>
    <div class="muted">This contract covers the agreed package for the specified event.</div>
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
        <tr>
            <th>Price</th>
            <td>{{ number_format((float) ($package?->price ?? 0), 2) }}</td>
        </tr>
    </table>
</div>

<div class="card">
    <h3 style="margin:0 0 8px 0;">Payment</h3>
    <div class="muted">Payment is due as per the proforma invoice and the bank details below.</div>
    <pre style="white-space: pre-wrap; margin-top: 10px;">{{ $event?->bank_details ?? 'N/A' }}</pre>
</div>

<div class="card">
    <h3 style="margin:0 0 8px 0;">Signatures</h3>
    <table>
        <tr>
            <th style="width: 25%;">Smart Vision</th>
            <td style="height: 70px;"></td>
        </tr>
        <tr>
            <th>Client</th>
            <td style="height: 70px;"></td>
        </tr>
    </table>
</div>
@endsection
