@php
    $isEdit = isset($pdf);
@endphp

<form action="{{ $isEdit ? route('pdfs.update', $pdf) : route('pdfs.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    @if($isEdit)
        @method('PUT')
    @endif

    <div class="mb-3">
        <label for="name" class="form-label">{{ __('pdfs.name') }}</label>
        <input type="text" name="name" id="name" class="form-control" required
               value="{{ old('name', $pdf->name ?? '') }}">
    </div>

    <div class="mb-3">
        <label for="pdf" class="form-label">{{ __('pdfs.link') }}</label>
        <input type="file" name="pdf" id="pdf" class="form-control" {{ $isEdit ? '' : 'required' }}>
        @if($isEdit && $pdf->pdf)
            <small class="text-muted">
                {{ __('pdfs.current_file') }}:
                <a href="{{ asset($pdf->pdf) }}" target="_blank">{{ __('pdfs.view') }}</a>
            </small>
        @endif
    </div>

    <button type="submit" class="btn btn-success">
        {{ $isEdit ? __('pdfs.update') : __('pdfs.save') }}
    </button>
</form>
