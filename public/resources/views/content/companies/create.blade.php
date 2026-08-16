@extends('layouts.layoutMaster')

@section('title', __('Add Influncer'))

@section('content')
<div class="container">
    <h4 class="mb-4">{{ __('Add Influncer') }}</h4>
    @include('content.companies._form')
</div>
@endsection
