@extends('layouts.layoutMaster')

@section('content')
    <div class="container">
        <h2>{{ __('Edit Luxury Community Member') }}</h2>
        <form action="{{ route('dashboard.luxury-members.update', $member->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            @include('content.luxury_community_members.form', ['member' => $member])
            <button type="submit" class="btn btn-primary">{{ __('Update') }}</button>
        </form>
    </div>
@endsection
