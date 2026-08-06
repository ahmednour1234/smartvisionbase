@extends('layouts.layoutMaster')

@section('content')
<div class="container">
    <h2 class="my-4">@lang('prizes.add')</h2>
    <form action="{{ route('dashboard.prizes.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @include('content.prizes.form')
        <button type="submit" class="btn btn-success">@lang('prizes.save')</button>
    </form>
</div>
@endsection
