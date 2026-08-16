<div class="row gy-3">
  <div class="col-md-6">
    <label class="form-label">{{ __('blog.name_ar') }}</label>
    <input type="text" name="name_ar" class="form-control @error('name_ar') is-invalid @enderror" value="{{ old('name_ar', $blog->name_ar ?? '') }}">
    @error('name_ar') <div class="invalid-feedback">{{ $message }}</div> @enderror
  </div>

  <div class="col-md-6">
    <label class="form-label">{{ __('blog.name_en') }}</label>
    <input type="text" name="name_en" class="form-control @error('name_en') is-invalid @enderror" value="{{ old('name_en', $blog->name_en ?? '') }}">
    @error('name_en') <div class="invalid-feedback">{{ $message }}</div> @enderror
  </div>

  <div class="col-md-6">
    <label class="form-label">{{ __('blog.title_ar') }}</label>
    <input type="text" name="title_ar" class="form-control @error('title_ar') is-invalid @enderror" value="{{ old('title_ar', $blog->title_ar ?? '') }}">
    @error('title_ar') <div class="invalid-feedback">{{ $message }}</div> @enderror
  </div>

  <div class="col-md-6">
    <label class="form-label">{{ __('blog.title_en') }}</label>
    <input type="text" name="title_en" class="form-control @error('title_en') is-invalid @enderror" value="{{ old('title_en', $blog->title_en ?? '') }}">
    @error('title_en') <div class="invalid-feedback">{{ $message }}</div> @enderror
  </div>

  <div class="col-md-12">
    <label class="form-label">{{ __('blog.description_ar') }}</label>
    <textarea name="description_ar" class="form-control @error('description_ar') is-invalid @enderror" rows="3">{{ old('description_ar', $blog->description_ar ?? '') }}</textarea>
    @error('description_ar') <div class="invalid-feedback">{{ $message }}</div> @enderror
  </div>

  <div class="col-md-12">
    <label class="form-label">{{ __('blog.description_en') }}</label>
    <textarea name="description_en" class="form-control @error('description_en') is-invalid @enderror" rows="3">{{ old('description_en', $blog->description_en ?? '') }}</textarea>
    @error('description_en') <div class="invalid-feedback">{{ $message }}</div> @enderror
  </div>

  <div class="col-md-6">
    <label class="form-label">{{ __('blog.sort') }}</label>
    <input type="number" name="sort" class="form-control @error('sort') is-invalid @enderror" value="{{ old('sort', $blog->sort ?? 0) }}">
    @error('sort') <div class="invalid-feedback">{{ $message }}</div> @enderror
  </div>

  <div class="col-md-6">
    <label class="form-label">{{ __('blog.date') }}</label>
    <input type="date" name="date" class="form-control @error('date') is-invalid @enderror" value="{{ old('date', $blog->date ?? '') }}">
    @error('date') <div class="invalid-feedback">{{ $message }}</div> @enderror
  </div>
<div class="col-md-6">
  <label class="form-label">{{ __('blog.link') }}</label>
  <input type="url" name="link" class="form-control @error('link') is-invalid @enderror" value="{{ old('link', $blog->link ?? '') }}">
  @error('link')
    <div class="invalid-feedback">{{ $message }}</div>
  @enderror
</div>



  <div class="col-md-6">
    <label class="form-label">{{ __('blog.active') }}</label>
    <select name="active" class="form-select @error('active') is-invalid @enderror">
      <option value="1" {{ old('active', $blog->active ?? 1) == 1 ? 'selected' : '' }}>{{ __('blog.status_active') }}</option>
      <option value="0" {{ old('active', $blog->active ?? 1) == 0 ? 'selected' : '' }}>{{ __('blog.status_inactive') }}</option>
    </select>
    @error('active') <div class="invalid-feedback">{{ $message }}</div> @enderror
  </div>
    <div class="col-md-6">
    <label class="form-label">{{ __('blog.image') }}</label>
    <input type="file" name="image" class="form-control @error('image') is-invalid @enderror" accept="image/*">
    @error('image') <div class="invalid-feedback">{{ $message }}</div> @enderror

    {{-- عرض الصورة الحالية إن وُجدت --}}
    @if(!empty($blog->image))
      <div class="mt-2">
        <img src="{{ asset($blog->image) }}" alt="Blog Image" style="width: 100px; height: auto;" class="img-thumbnail rounded">
      </div>
    @endif
  </div>

  <div class="col-12 text-end">
    <button type="submit" class="btn btn-primary">
      <i class="bi bi-save me-1"></i> {{ $button }}
    </button>
  </div>
</div>
