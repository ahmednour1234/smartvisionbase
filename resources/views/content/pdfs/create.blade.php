@extends('layouts.layoutMaster')

@section('title', __('pdfs.add_new'))

@section('content')
<div class="container">
    <h2 class="mb-4">{{ __('pdfs.add_new') }}</h2>

    @include('content.pdfs._form')
</div>
@endsection
