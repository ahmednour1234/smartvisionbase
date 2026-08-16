// create.blade.php
@extends('layouts.layoutMaster')

@section('title', __('pixels.create'))

@section('content')
<div class="container">
    <h4>{{ __('pixels.create') }}</h4>
    <form action="{{ route('dashboard.pixels.store') }}" method="POST">
        @csrf
        @include('content.pixels.form')
        <button type="submit" class="btn btn-primary">{{ __('pixels.create') }}</button>
    </form>
</div>
@endsection


// edit.blade.php