<div class="mb-3">
    <label for="name" class="form-label">{{ __('pixels.name') }}</label>
    <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $pixel->name ?? '') }}" required>
</div>

<div class="mb-3">
    <label for="pixel_id" class="form-label">{{ __('pixels.pixel_id') }}</label>
    <input type="text" name="pixel_id" id="pixel_id" class="form-control" value="{{ old('pixel_id', $pixel->pixel_id ?? '') }}" required>
</div>
