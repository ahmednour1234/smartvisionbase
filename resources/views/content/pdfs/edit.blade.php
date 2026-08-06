@extends('layouts.layoutMaster')

@section('title', __('pdfs.edit'))

@section('content')
<div class="container">
    <h2 class="mb-4">{{ __('pdfs.edit') }}</h2>

    @include('content.pdfs._form', ['pdf' => $pdf])
</div>
@endsection
