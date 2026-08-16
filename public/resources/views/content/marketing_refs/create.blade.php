@extends('layouts.layoutMaster')

@section('title', __('clients.create'))

@section('content')
<div class="max-w-3xl mx-auto p-6">
    <div class="mb-6">
        <h1 class="text-2xl font-bold">إضافة مرجع تسويقي</h1>
        <p class="text-gray-600 text-sm">سيتم تتبع التسجيلات القادمة من هذا المرجع فقط.</p>
    </div>

    <div class="bg-white shadow rounded-lg p-6">
        <form action="{{ route('marketing-refs.store') }}" method="POST">
            @csrf
            @include('content.marketing_refs._form')
        </form>
    </div>
</div>
@endsection
