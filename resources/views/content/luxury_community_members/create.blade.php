@extends('layouts.layoutMaster')

@section('content')
    <div class="container">
        <h2>{{ __('Create Luxury Community Member') }}</h2>
        <form action="{{ route('dashboard.luxury-members.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @include('content.luxury_community_members.form', ['member' => null])
            <button type="submit" class="btn btn-primary">{{ __('Save') }}</button>
        </form>
    </div>
@endsection
