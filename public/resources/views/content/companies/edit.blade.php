@extends('layouts.layoutMaster')

@section('title', __('Edit Influncer'))

@section('content')
<div class="container">
    <h4 class="mb-4">{{ __('Edit Influncer') }}</h4>
    @include('content.companies._form', ['company' => $company])
</div>
@endsection
