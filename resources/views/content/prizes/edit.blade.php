@extends('layouts.layoutMaster')

@section('content')
<div class="container">
    <h2 class="my-4">@lang('prizes.edit')</h2>
    <form action="{{ route('dashboard.prizes.update', $prize->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        @include('content.prizes.form', ['prize' => $prize])
        <button type="submit" class="btn btn-primary">@lang('prizes.update')</button>
    </form>
</div>
@endsection
