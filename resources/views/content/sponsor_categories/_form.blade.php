@php $isEdit = isset($sponsorCategory); @endphp
<div class="row g-3">
  <div class="col-md-6">
    <label class="form-label">{{ __('sponsor_category.name') }}</label>
    <input type="text" name="name" class="form-control" value="{{ old('name', $isEdit ? $sponsorCategory->name : '') }}" required>
  </div>
  <div class="col-md-6">
    <label class="form-label">{{ __('sponsor_category.name_en') }}</label>
    <input type="text" name="name_en" class="form-control" value="{{ old('name_en', $isEdit ? $sponsorCategory->name_en : '') }}" required>
  </div>

  <div class="col-md-6">
    <label class="form-label">{{ __('sponsor_category.status') }}</label>
    <select name="active" class="form-select">
      <option value="1" {{ old('active', $isEdit ? $sponsorCategory->active : 1) == 1 ? 'selected' : '' }}>{{ __('general.active') }}</option>
      <option value="0" {{ old('active', $isEdit ? $sponsorCategory->active : 1) == 0 ? 'selected' : '' }}>{{ __('general.inactive') }}</option>
    </select>
  </div>

  <div class="col-md-6">
    <label class="form-label">{{ __('sponsor_category.logo') }}</label>
    <input type="file" name="logo" class="form-control">
    @if($isEdit && $sponsorCategory->logo)
      <img src="{{ asset($sponsorCategory->logo) }}" class="img-thumbnail mt-2" width="100">
    @endif
  </div>
</div>
