@php $locale = app()->getLocale(); @endphp

<div class="mb-3">
    <label for="name_ar" class="form-label">{{ __('Name (AR)') }}</label>
    <input type="text" class="form-control" name="name_ar" value="{{ old('name_ar', $member->name_ar ?? '') }}">
</div>

<div class="mb-3">
    <label for="name_en" class="form-label">{{ __('Name (EN)') }}</label>
    <input type="text" class="form-control" name="name_en" value="{{ old('name_en', $member->name_en ?? '') }}">
</div>

<div class="mb-3">
    <label for="title_ar" class="form-label">{{ __('Title (AR)') }}</label>
    <input type="text" class="form-control" name="title_ar" value="{{ old('title_ar', $member->title_ar ?? '') }}">
</div>

<div class="mb-3">
    <label for="title_en" class="form-label">{{ __('Title (EN)') }}</label>
    <input type="text" class="form-control" name="title_en" value="{{ old('title_en', $member->title_en ?? '') }}">
</div>

<div class="mb-3">
    <label for="company" class="form-label">{{ __('Company') }}</label>
    <input type="text" class="form-control" name="company" value="{{ old('company', $member->company ?? '') }}">
</div>

<div class="mb-3">
    <label for="email" class="form-label">{{ __('Email') }}</label>
    <input type="email" class="form-control" name="email" value="{{ old('email', $member->email ?? '') }}">
</div>

<div class="mb-3">
    <label for="phone" class="form-label">{{ __('Phone') }}</label>
    <input type="text" class="form-control" name="phone" value="{{ old('phone', $member->phone ?? '') }}">
</div>

<div class="mb-3">
    <label for="description" class="form-label">{{ __('Description') }}</label>
    <textarea name="description" class="form-control">{{ old('description', $member->description ?? '') }}</textarea>
</div>

<div class="mb-3">
    <label for="image" class="form-label">{{ __('Image') }}</label>
    <input type="file" class="form-control" name="image">
    @if(!empty($member->image))
        <img src="{{ asset('public/'.$member->image) }}" class="img-thumbnail mt-2" style="max-height: 150px;">
    @endif
</div>
