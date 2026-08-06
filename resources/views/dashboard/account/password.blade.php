@extends('layouts.dashboard')
@section('title', 'Change Password')
@section('page_title', 'Account Settings')

@section('content')
    <div class="mx-auto max-w-2xl">
        <div class="rounded-xl border bg-white p-6 shadow-sm dash-card">
            <h2 class="form-section-title">Change Password</h2>
            <form method="POST" action="{{ route('dashboard.account.password.update') }}" class="mt-4 space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700">Current Password</label>
                    <input type="password" name="current_password" class="mt-1 block w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"/>
                    @error('current_password') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">New Password</label>
                        <input type="password" name="password" class="mt-1 block w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"/>
                        @error('password') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Confirm Password</label>
                        <input type="password" name="password_confirmation" class="mt-1 block w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"/>
                    </div>
                </div>
                <div class="pt-2">
                    <button class="btn-primary">Update Password</button>
                </div>
            </form>
        </div>
    </div>
@endsection


