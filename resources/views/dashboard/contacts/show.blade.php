@extends('layouts.dashboard')
@section('title', 'Message Details')
@section('page_title', 'Message Details')

@section('content')
    <div class="mx-auto max-w-3xl space-y-6">
        <div class="rounded-xl border bg-white p-6 shadow-sm">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                <div>
                    <div class="text-gray-500">Name</div>
                    <div class="font-medium">{{ $contactMessage->name }}</div>
                </div>
                <div>
                    <div class="text-gray-500">Email</div>
                    <div class="font-medium">{{ $contactMessage->email }}</div>
                </div>
                <div>
                    <div class="text-gray-500">Phone</div>
                    <div class="font-medium">{{ $contactMessage->phone ?: '—' }}</div>
                </div>
                <div>
                    <div class="text-gray-500">Received</div>
                    <div class="font-medium">{{ $contactMessage->created_at->toDayDateTimeString() }}</div>
                </div>
            </div>
        </div>
        <div class="rounded-xl border bg-white p-6 shadow-sm">
            <div class="text-sm text-gray-500 mb-2">Subject</div>
            <div class="font-medium mb-4">{{ $contactMessage->subject }}</div>
            <div class="text-sm text-gray-500 mb-2">Message</div>
            <div class="whitespace-pre-line leading-7">{{ $contactMessage->message }}</div>
        </div>
        <div>
            <a href="{{ route('dashboard.contacts.index') }}" class="inline-flex items-center rounded-md bg-white border px-3 py-2 text-sm font-medium hover:bg-gray-50">Back to Inbox</a>
        </div>
    </div>
@endsection


