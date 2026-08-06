@php
  $isEdit = isset($form);
@endphp

<div class="row g-3">
  {{-- الاسم بالعربية --}}
  <div class="col-md-6">
    <label for="name_ar" class="form-label">{{ __('form_name_ar') }} <span class="text-danger">*</span></label>
    <input type="text" name="name[ar]" id="name_ar" class="form-control"
           value="{{ old('name.ar', $isEdit ? ($form->name['ar'] ?? '') : '') }}" required>
  </div>

  {{-- الاسم بالإنجليزية --}}
  <div class="col-md-6">
    <label for="name_en" class="form-label">{{ __('form_name_en') }} <span class="text-danger">*</span></label>
    <input type="text" name="name[en]" id="name_en" class="form-control"
           value="{{ old('name.en', $isEdit ? ($form->name['en'] ?? '') : '') }}" required>
  </div>

  {{-- الوصف بالعربية --}}
  <div class="col-md-6">
    <label for="description_ar" class="form-label">{{ __('form_description_ar') }}</label>
    <textarea name="description[ar]" id="description_ar" class="form-control html-editor" rows="5">{!! old('description.ar', $isEdit ? ($form->description['ar'] ?? '') : '') !!}</textarea>
  </div>

  {{-- الوصف بالإنجليزية --}}
  <div class="col-md-6">
    <label for="description_en" class="form-label">{{ __('form_description_en') }}</label>
    <textarea name="description[en]" id="description_en" class="form-control html-editor" rows="5">{!! old('description.en', $isEdit ? ($form->description['en'] ?? '') : '') !!}</textarea>
  </div>

  {{-- الرقم --}}
  <div class="col-md-6">
    <label for="number" class="form-label">{{ __('form_number') }}</label>
    <input type="text" name="number" id="number" class="form-control"
           value="{{ old('number', $isEdit ? $form->number : '') }}">
  </div>

  {{-- الحالة --}}
  <div class="col-md-6 d-flex align-items-center">
    <div class="form-check form-switch mt-4">
      <input class="form-check-input" type="checkbox" name="active" id="active" value="1"
             {{ old('active', $isEdit ? $form->active : true) ? 'checked' : '' }}>
      <label class="form-check-label" for="active">{{ __('form_active') }}</label>
    </div>
  </div>

  {{-- الصورة --}}
  <div class="col-12">
    <label for="img" class="form-label">{{ __('form_image') }}</label>
    <input type="file" name="img" id="img" class="form-control">
    @if($isEdit && $form->img)
      <img src="{{ asset($form->img) }}" class="img-thumbnail mt-2 d-block" width="150" alt="Uploaded Image">
    @endif
  </div>
</div>
