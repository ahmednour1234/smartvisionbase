{{-- resources/views/content/galleries/_form.blade.php --}}
<div class="mb-3">
    <label for="image" class="form-label">{{ __('gallery.image') }}
        @if(!$isEdit)
            <span class="text-danger">*</span>
        @endif
    </label>

    @if($isEdit)
        <div class="mb-2">
            <img src="{{ asset($gallery->image) }}" alt="preview" class="img-thumbnail" style="max-height: 150px;">
        </div>
    @endif

    <input type="file"
           name="{{ $isEdit ? 'image' : 'images[]' }}"
           id="image"
           class="form-control @error($isEdit ? 'image' : 'images.*') is-invalid @enderror"
           {{ $isEdit ? '' : 'multiple required' }}>

    @error($isEdit ? 'image' : 'images.*')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>
