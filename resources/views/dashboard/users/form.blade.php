@extends('layouts.dashboard')
@section('title', $user->exists ? 'Edit User' : 'Create User')
@section('page_title', $user->exists ? 'Edit User' : 'Create User')

@section('content')
    <div class="mx-auto max-w-2xl">
        <div class="rounded-xl border bg-white p-6 shadow-sm dash-card">
            <h2 class="form-section-title">{{ $user->exists ? 'Update user' : 'New user' }}</h2>
            <form method="POST" action="{{ $user->exists ? route('dashboard.users.update', $user) : route('dashboard.users.store') }}" class="mt-4 space-y-4">
                @csrf
                @if($user->exists) @method('PUT') @endif
                <div>
                    <label class="block text-sm font-medium text-gray-700">Name</label>
                    <input name="name" value="{{ old('name', $user->name) }}" class="mt-1 block w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"/>
                    @error('name') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Email</label>
                    <input name="email" value="{{ old('email', $user->email) }}" class="mt-1 block w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"/>
                    @error('email') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">{{ $user->exists ? 'New Password (optional)' : 'Password' }}</label>
                        <input type="password" name="password" class="mt-1 block w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"/>
                        @error('password') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Confirm Password</label>
                        <input type="password" name="password_confirmation" class="mt-1 block w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"/>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $user->is_active ?? true) ? 'checked' : '' }} class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                    <span class="text-sm text-gray-700">Active</span>
                </div>
                <div class="pt-2">
                    <button class="btn-primary">{{ $user->exists ? 'Save Changes' : 'Create User' }}</button>
                </div>
            </form>
        </div>
    </div>
@endsection


