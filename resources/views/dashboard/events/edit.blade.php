@extends('layouts.dashboard')
@section('title', 'Edit Event')
@section('page_title', 'Edit Event')

@section('content')
    <div class="mx-auto max-w-4xl">
        <form method="POST" action="{{ route('dashboard.events.update', $event) }}" class="space-y-8" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            @include('dashboard.events.partials.form', ['event' => $event])
            <div>
                <button class="inline-flex items-center rounded-md bg-gray-900 px-4 py-2 text-sm font-medium text-white hover:bg-gray-800">
                    Save Changes
                </button>
            </div>
        </form>
    </div>
@endsection


