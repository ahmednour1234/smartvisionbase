@php $isEdit = isset($package); @endphp

<div class="row gy-3">
  <div class="col-md-6">
    <label class="form-label">{{ __('package.name_ar') }}</label>
    <input type="text" name="name_ar" class="form-control @error('name_ar') is-invalid @enderror"
           value="{{ old('name_ar', $isEdit ? $package->name_ar : '') }}">
    @error('name_ar') <div class="invalid-feedback">{{ $message }}</div> @enderror
  </div>

  <div class="col-md-6">
    <label class="form-label">{{ __('package.name_en') }}</label>
    <input type="text" name="name_en" class="form-control @error('name_en') is-invalid @enderror"
           value="{{ old('name_en', $isEdit ? $package->name_en : '') }}">
    @error('name_en') <div class="invalid-feedback">{{ $message }}</div> @enderror
  </div>

  <div class="col-md-6">
    <label class="form-label">{{ __('package.title_ar') }}</label>
    <input type="text" name="title_ar" class="form-control @error('title_ar') is-invalid @enderror"
           value="{{ old('title_ar', $isEdit ? $package->title_ar : '') }}">
    @error('title_ar') <div class="invalid-feedback">{{ $message }}</div> @enderror
  </div>

  <div class="col-md-6">
    <label class="form-label">{{ __('package.title_en') }}</label>
    <input type="text" name="title_en" class="form-control @error('title_en') is-invalid @enderror"
           value="{{ old('title_en', $isEdit ? $package->title_en : '') }}">
    @error('title_en') <div class="invalid-feedback">{{ $message }}</div> @enderror
  </div>


  <div class="col-md-6">
    <label class="form-label">{{ __('package.sort') }}</label>
    <input type="number" name="sort" class="form-control @error('sort') is-invalid @enderror"
           value="{{ old('sort', $isEdit ? $package->sort : 0) }}">
    @error('sort') <div class="invalid-feedback">{{ $message }}</div> @enderror
  </div>

  <div class="col-md-6">
    <label class="form-label">{{ __('package.active') }}</label>
    <select name="active" class="form-select @error('active') is-invalid @enderror">
      <option value="1" {{ old('active', $isEdit ? $package->active : 1) == 1 ? 'selected' : '' }}>
        {{ __('package.status_active') }}
      </option>
      <option value="0" {{ old('active', $isEdit ? $package->active : 1) == 0 ? 'selected' : '' }}>
        {{ __('package.status_inactive') }}
      </option>
    </select>
    @error('active') <div class="invalid-feedback">{{ $message }}</div> @enderror
  </div>

  <div class="col-md-6">
    <label class="form-label">{{ __('package.description_ar') }}</label>
    <textarea name="description_ar" class="form-control @error('description_ar') is-invalid @enderror">{{ old('description_ar', $isEdit ? $package->description_ar : '') }}</textarea>
    @error('description_ar') <div class="invalid-feedback">{{ $message }}</div> @enderror
  </div>

  <div class="col-md-6">
    <label class="form-label">{{ __('package.description_en') }}</label>
    <textarea name="description_en" class="form-control @error('description_en') is-invalid @enderror">{{ old('description_en', $isEdit ? $package->description_en : '') }}</textarea>
    @error('description_en') <div class="invalid-feedback">{{ $message }}</div> @enderror
  </div>

  <div class="col-12 text-end mt-3">
    <button type="submit" class="btn btn-primary px-4">
      <i class="bi bi-save me-1"></i> {{ $button }}
    </button>
    <a href="{{ route('dashboard.packages.index') }}" class="btn btn-outline-secondary px-4">
      {{ __('package.back') }}
    </a>
  </div>
</div>
