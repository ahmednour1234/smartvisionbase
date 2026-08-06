@extends('layouts.layoutMaster')

@section('title', __('Add Company'))

@section('content')
<div class="container">
    <h4 class="mb-4">{{ __('Add Company') }}</h4>
    @include('content.companies._form')
</div>
@endsection
