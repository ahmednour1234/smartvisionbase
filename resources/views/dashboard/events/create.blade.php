@extends('layouts.dashboard')
@section('title', 'Create Event')
@section('page_title', 'Create Event')

@section('content')
    <div class="mx-auto max-w-4xl">
        <form method="POST" action="{{ route('dashboard.events.store') }}" class="space-y-8" enctype="multipart/form-data">
            @csrf
            @include('dashboard.events.partials.form', ['event' => null])
            <div>
                <button class="inline-flex items-center rounded-md bg-gray-900 px-4 py-2 text-sm font-medium text-white hover:bg-gray-800">
                    Create
                </button>
            </div>
        </form>
    </div>
@endsection


