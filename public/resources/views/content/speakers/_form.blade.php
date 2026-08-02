@php $isEdit = isset($speaker); @endphp
<div class="row g-3">
  <div class="col-md-6">
    <label class="form-label">{{ __('speaker.name_ar') }}</label>
    <input type="text" name="name_ar" class="form-control" value="{{ old('name_ar', $isEdit ? $speaker->name_ar : '') }}" required>
  </div>
  <div class="col-md-6">
    <label class="form-label">{{ __('speaker.name_en') }}</label>
    <input type="text" name="name_en" class="form-control" value="{{ old('name_en', $isEdit ? $speaker->name_en : '') }}" required>
  </div>

  <div class="col-md-6">
    <label class="form-label">{{ __('speaker.title_ar') }}</label>
    <input type="text" name="title_ar" class="form-control" value="{{ old('title_ar', $isEdit ? $speaker->title_ar : '') }}">
  </div>
  <div class="col-md-6">
    <label class="form-label">{{ __('speaker.title_en') }}</label>
    <input type="text" name="title_en" class="form-control" value="{{ old('title_en', $isEdit ? $speaker->title_en : '') }}">
  </div>

  <div class="col-md-6">
    <label class="form-label">{{ __('speaker.company_name_ar') }}</label>
    <input type="text" name="company_name_ar" class="form-control" value="{{ old('company_name_ar', $isEdit ? $speaker->company_name_ar : '') }}">
  </div>
  <div class="col-md-6">
    <label class="form-label">{{ __('speaker.company_name_en') }}</label>
    <input type="text" name="company_name_en" class="form-control" value="{{ old('company_name_en', $isEdit ? $speaker->company_name_en : '') }}">
  </div>

  <div class="col-md-6">
    <label class="form-label">{{ __('speaker.linkedin') }}</label>
    <input type="url" name="linkedin" class="form-control" value="{{ old('linkedin', $isEdit ? $speaker->linkedin : '') }}">
  </div>
  
  <div class="col-md-6">
    <label class="form-label">{{ __('speaker.facebook') }}</label>
    <input type="url" name="facebook" class="form-control" value="{{ old('facebook', $isEdit ? $speaker->facebook : '') }}">
  </div>
  
  <div class="col-md-6">
    <label class="form-label">{{ __('speaker.twitter') }}</label>
    <input type="url" name="tiktok" class="form-control" value="{{ old('tiktok', $isEdit ? $speaker->tiktok : '') }}">
  </div>
  
  <div class="col-md-6">
    <label class="form-label">{{ __('speaker.youtube') }}</label>
    <input type="url" name="youtube" class="form-control" value="{{ old('youtube', $isEdit ? $speaker->youtube : '') }}">
  </div>
  <div class="col-md-6">
    <label class="form-label">{{ __('speaker.social_links') }}</label>
    <textarea name="social_links" class="form-control">{{ old('social_links', $isEdit ? $speaker->social_links : '') }}</textarea>
  </div>

  <div class="col-md-6">
    <label class="form-label">{{ __('speaker.image') }}</label>
    <input type="file" name="image" class="form-control">
    @if($isEdit && $speaker->image)
      <img src="{{ asset($speaker->image) }}" class="img-thumbnail mt-2" width="100">
    @endif
  </div>
</div>
