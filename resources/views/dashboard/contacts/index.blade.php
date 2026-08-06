@extends('layouts.dashboard')
@section('title', 'Contact Messages')
@section('page_title', 'Contact Messages')

@section('content')
    <div class="mx-auto max-w-7xl">
        <div class="rounded-xl border bg-white shadow-sm">
            <div class="px-5 py-4 border-b flex items-center justify-between">
                <h2 class="text-sm font-semibold">Inbox</h2>
                <span class="text-sm text-gray-500">{{ $messages->total() }} total</span>
            </div>
            <div class="p-5 overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="text-left text-gray-500">
                            <th class="py-2">Name</th>
                            <th class="py-2">Email</th>
                            <th class="py-2">Subject</th>
                            <th class="py-2">Date</th>
                            <th class="py-2 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @forelse($messages as $msg)
                            <tr class="hover:bg-gray-50">
                                <td class="py-3">{{ $msg->name }}</td>
                                <td class="py-3 text-gray-600">{{ $msg->email }}</td>
                                <td class="py-3">{{ \Illuminate\Support\Str::limit($msg->subject, 60) }}</td>
                                <td class="py-3 text-gray-600">{{ $msg->created_at->diffForHumans() }}</td>
                                <td class="py-3 text-right">
                                    <a href="{{ route('dashboard.contacts.show', $msg) }}" class="text-indigo-600 hover:underline">Open</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td class="py-6 text-center text-gray-500" colspan="5">No messages yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="mt-4">
                    {{ $messages->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection


