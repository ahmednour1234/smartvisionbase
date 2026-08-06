@php $isEdit = isset($ad); @endphp

<div class="row g-3">
  {{-- الاسم بالعربية --}}
  <div class="col-md-6">
    <label for="name_ar" class="form-label">{{ __('ads_name_ar') }}</label>
    <input type="text" name="name[ar]" id="name_ar" class="form-control"
           value="{{ old('name.ar', $isEdit ? ($ad->name['ar'] ?? '') : '') }}" required>
  </div>

  {{-- الاسم بالإنجليزية --}}
  <div class="col-md-6">
    <label for="name_en" class="form-label">{{ __('ads_name_en') }}</label>
    <input type="text" name="name[en]" id="name_en" class="form-control"
           value="{{ old('name.en', $isEdit ? ($ad->name['en'] ?? '') : '') }}" required>
  </div>

  {{-- الحالة --}}
  <div class="col-md-6">
    <label for="active" class="form-label">{{ __('ads_active') }}</label>
    <select name="active" class="form-select">
      <option value="1" {{ old('active', $isEdit ? $ad->active : 1) == 1 ? 'selected' : '' }}>{{ __('ads_active_yes') }}</option>
      <option value="0" {{ old('active', $isEdit ? $ad->active : 1) == 0 ? 'selected' : '' }}>{{ __('ads_active_no') }}</option>
    </select>
  </div>

  {{-- الرابط --}}
  <div class="col-md-6">
    <label for="link" class="form-label">{{ __('ads_link') }}</label>
    <input type="url" name="link" id="link" class="form-control"
           value="{{ old('link', $isEdit ? $ad->link : '') }}">
  </div>

  {{-- تاريخ البداية --}}
  <div class="col-md-6">
    <label for="start_date" class="form-label">{{ __('ads_start_date') }}</label>
    <input type="date" name="start_date" class="form-control"
           value="{{ old('start_date', $isEdit ? $ad->start_date : '') }}">
  </div>

  {{-- تاريخ النهاية --}}
  <div class="col-md-6">
    <label for="end_date" class="form-label">{{ __('ads_end_date') }}</label>
    <input type="date" name="end_date" class="form-control"
           value="{{ old('end_date', $isEdit ? $ad->end_date : '') }}">
  </div>

  {{-- الترتيب --}}
  <div class="col-md-6">
    <label for="sort" class="form-label">{{ __('ads_sort') }}</label>
    <input type="number" name="sort" class="form-control"
           value="{{ old('sort', $isEdit ? $ad->sort : 0) }}">
  </div>

  {{-- الوصف بالعربية --}}
  <div class="col-md-6">
    <label for="description_ar" class="form-label">{{ __('ads_description_ar') }}</label>
    <textarea name="description[ar]" id="description_ar" rows="4" class="form-control">{{ old('description.ar', $isEdit ? ($ad->description['ar'] ?? '') : '') }}</textarea>
  </div>

  {{-- الوصف بالإنجليزية --}}
  <div class="col-md-6">
    <label for="description_en" class="form-label">{{ __('ads_description_en') }}</label>
    <textarea name="description[en]" id="description_en" rows="4" class="form-control">{{ old('description.en', $isEdit ? ($ad->description['en'] ?? '') : '') }}</textarea>
  </div>

  {{-- الصورة --}}
  <div class="col-md-6">
    <label for="img" class="form-label">{{ __('ads_img') }}</label>
    <input type="file" name="img" class="form-control">
    @if($isEdit && $ad->img)
      <div class="mt-2">
        <img src="{{ asset($ad->img) }}" alt="Image" class="img-thumbnail" width="120">
      </div>
    @endif
  </div>
</div>
