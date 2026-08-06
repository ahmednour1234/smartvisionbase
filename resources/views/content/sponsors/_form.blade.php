
@php
  $isEdit = isset($sponsor);
@endphp

<div class="row g-3">
  <div class="col-md-6">
    <label class="form-label">{{ __('sponsor.name_ar') }}</label>
    <input type="text" name="name_ar" class="form-control" value="{{ old('name_ar', $isEdit ? $sponsor->name_ar : '') }}" required>
  </div>
  <div class="col-md-6">
    <label class="form-label">{{ __('sponsor.name_en') }}</label>
    <input type="text" name="name_en" class="form-control" value="{{ old('name_en', $isEdit ? $sponsor->name_en : '') }}" required>
  </div>

  <div class="col-md-6">
    <label class="form-label">{{ __('sponsor.title_ar') }}</label>
    <input type="text" name="title_ar" class="form-control" value="{{ old('title_ar', $isEdit ? $sponsor->title_ar : '') }}">
  </div>
  <div class="col-md-6">
    <label class="form-label">{{ __('sponsor.title_en') }}</label>
    <input type="text" name="title_en" class="form-control" value="{{ old('title_en', $isEdit ? $sponsor->title_en : '') }}">
  </div>

  <div class="col-md-6">
    <label class="form-label">{{ __('sponsor.company_name_ar') }}</label>
    <input type="text" name="company_name_ar" class="form-control" value="{{ old('company_name_ar', $isEdit ? $sponsor->company_name_ar : '') }}">
  </div>
  <div class="col-md-6">
    <label class="form-label">{{ __('sponsor.company_name_en') }}</label>
    <input type="text" name="company_name_en" class="form-control" value="{{ old('company_name_en', $isEdit ? $sponsor->company_name_en : '') }}">
  </div>

  <div class="col-md-6">
    <label class="form-label">{{ __('sponsor.phone') }}</label>
    <input type="text" name="phone" class="form-control" value="{{ old('phone', $isEdit ? $sponsor->phone : '') }}">
  </div>

  <div class="col-md-6">
    <label class="form-label">{{ __('sponsor.category') }}</label>
    <select name="category_sponsor_id" class="form-select" required>
      <option value="">-- {{ __('sponsor.category') }} --</option>
      @foreach($categories as $cat)
        <option value="{{ $cat->id }}" {{ old('category_sponsor_id', $isEdit ? $sponsor->category_sponsor_id : '') == $cat->id ? 'selected' : '' }}>
          {{ app()->getLocale() == 'ar' ? $cat->name : $cat->name_en }}
        </option>
      @endforeach
    </select>
  </div>

  <div class="col-md-6">
    <label class="form-label">{{ __('sponsor.image') }}</label>
    <input type="file" name="image" class="form-control">
    @if($isEdit && $sponsor->image)
      <img src="{{ asset($sponsor->image) }}" alt="image" class="mt-2" width="80">
    @endif
  </div>

  <div class="col-md-6">
    <label class="form-label">{{ __('sponsor.status') }}</label>
    <select name="active" class="form-select">
      <option value="1" {{ old('active', $isEdit ? $sponsor->active : 1) == 1 ? 'selected' : '' }}>{{ __('sponsor.active') }}</option>
      <option value="0" {{ old('active', $isEdit ? $sponsor->active : 1) == 0 ? 'selected' : '' }}>{{ __('sponsor.inactive') }}</option>
    </select>
  </div>
</div>
