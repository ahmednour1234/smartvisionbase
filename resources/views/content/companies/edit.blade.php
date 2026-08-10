@extends('layouts.layoutMaster')

@section('title', __('Edit Company'))

@section('content')
<div class="container">
    <h4 class="mb-4">{{ __('Edit Company') }}</h4>
    @include('content.companies._form', ['company' => $company])
</div>
@endsection
