@php $isEdit = isset($seo); @endphp

<div class="row g-3">
  <div class="col-md-6">
    <label for="route" class="form-label">{{ __('seo_route') }}</label>
    <select name="route" id="route" class="form-select" required>
      <option value="">{{ __('global.select') }}</option>
      @foreach(['home','about','speakers','sponsors','blogs','schedulings'] as $route)
        <option value="{{ $route }}" {{ old('route', $isEdit ? $seo->route : '') == $route ? 'selected' : '' }}>
          {{ __('seo_route_' . $route) }}
        </option>
      @endforeach
    </select>
  </div>

  @foreach(['title', 'description', 'keywords', 'og_title', 'og_description'] as $field)
    <div class="col-md-6">
      <label class="form-label">{{ __("seo_{$field}_ar") }}</label>
      <input type="text" name="{{ $field }}[ar]" class="form-control"
             value="{{ old("{$field}.ar", $isEdit ? ($seo->$field['ar'] ?? '') : '') }}">
    </div>
    <div class="col-md-6">
      <label class="form-label">{{ __("seo_{$field}_en") }}</label>
      <input type="text" name="{{ $field }}[en]" class="form-control"
             value="{{ old("{$field}.en", $isEdit ? ($seo->$field['en'] ?? '') : '') }}">
    </div>
  @endforeach

  <div class="col-md-6">
    <label for="og_image" class="form-label">{{ __('seo_og_image') }}</label>
    <input type="file" name="og_image" class="form-control">
    @if($isEdit && $seo->og_image)
      <div class="mt-2">
        <img src="{{ asset($seo->og_image) }}" class="img-thumbnail" width="150">
      </div>
    @endif
  </div>
</div>
