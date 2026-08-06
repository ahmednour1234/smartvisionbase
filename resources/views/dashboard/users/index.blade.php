@extends('layouts.dashboard')
@section('title', 'Users')
@section('page_title', 'Users')

@section('content')
    <div class="mx-auto max-w-7xl">
        <div class="mb-4 flex items-center justify-between">
            <h2 class="form-section-title">All Users</h2>
            <a href="{{ route('dashboard.users.create') }}" class="btn-primary">Create User</a>
        </div>
        <div class="rounded-xl border bg-white shadow-sm dash-card">
            <div class="p-5 overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                    <tr class="text-left text-gray-500">
                        <th class="py-2">Name</th>
                        <th class="py-2">Email</th>
                        <th class="py-2">Active</th>
                        <th class="py-2 text-right">Actions</th>
                    </tr>
                    </thead>
                    <tbody class="divide-y">
                    @forelse($users as $u)
                        <tr class="hover:bg-gray-50">
                            <td class="py-3">{{ $u->name }}</td>
                            <td class="py-3 text-gray-600">{{ $u->email }}</td>
                            <td class="py-3">{{ $u->is_active ? 'Yes' : 'No' }}</td>
                            <td class="py-3 text-right space-x-2">
                                <a href="{{ route('dashboard.users.edit', $u) }}" class="text-indigo-600 hover:underline">Edit</a>
                                <form method="POST" action="{{ route('dashboard.users.destroy', $u) }}" class="inline" onsubmit="return confirm('Delete user?');">
                                    @csrf @method('DELETE')
                                    <button class="text-red-600 hover:underline">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td class="py-6 text-center text-gray-500" colspan="4">No users.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
                <div class="mt-4">
                    {{ $users->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection


