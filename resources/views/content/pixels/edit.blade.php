@extends('layouts.layoutMaster')

@section('title', __('pixels.edit'))

@section('content')
<div class="container">
    <h4>{{ __('pixels.edit') }}</h4>
    <form action="{{ route('dashboard.pixels.update', $pixel->id) }}" method="POST">
        @csrf
        @method('PUT')
        @include('content.pixels.form')
        <button type="submit" class="btn btn-warning">{{ __('pixels.edit') }}</button>
    </form>
</div>
@endsection