@extends('layouts.layoutMaster')

@section('title', __('clients.create'))

@section('content')
<div class="max-w-3xl mx-auto p-6">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold">تعديل المرجع: {{ $ref->name }}</h1>
            <p class="text-gray-600 text-sm">تحكم في الكود، السماح بالمسارات، وتاريخ الانتهاء.</p>
        </div>
        <a href="{{ route('marketing-refs.index') }}" class="px-4 py-2 rounded-lg border">رجوع</a>
    </div>

    <div class="bg-white shadow rounded-lg p-6">
        <form action="{{ route('marketing-refs.update', $ref) }}" method="POST">
            @csrf @method('PUT')
            @include('content.marketing_refs._form', ['ref' => $ref])
        </form>
    </div>
</div>
@endsection
