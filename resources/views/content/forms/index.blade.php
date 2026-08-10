@extends('layouts.layoutMaster')

@section('title', __('form_title'))

@section('content')
<div class="card shadow-sm">
  <div class="card-header bg-primary text-white">
    <h5 class="mb-0">{{ __('form_settings') }}</h5>
  </div>

  <div class="card-body">
    @if (session('success'))
      <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form action="{{ route('admin.forms.store') }}" method="POST" enctype="multipart/form-data">
      @csrf
      @include('content.forms._form', ['form' => $form])

      <div class="mt-4 text-center">
        <button type="submit" class="btn btn-success px-4">
          <i class="bi bi-save me-1"></i> {{ __('save_changes') }}
        </button>
      </div>
    </form>
  </div>
</div>
@endsection
  <script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      document.querySelectorAll('.html-editor').forEach((el) => {
        ClassicEditor.create(el).catch(error => console.error(error));
      });
    });
  </script>
