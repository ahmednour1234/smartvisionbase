<div class="row gy-3">
    {{-- الاسم بالعربي --}}
    <div class="col-md-6">
        <label for="name_ar" class="form-label">{{ __('multi_media.name_ar') }} <span class="text-danger">*</span></label>
        <input type="text" name="name_ar" id="name_ar" value="{{ old('name_ar', $category->name_ar ?? '') }}"
            class="form-control @error('name_ar') is-invalid @enderror" placeholder="{{ __('multi_media.name_ar') }}">
        @error('name_ar')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    {{-- الاسم بالإنجليزي --}}
    <div class="col-md-6">
        <label for="name_en" class="form-label">{{ __('multi_media.name_en') }} <span
                class="text-danger">*</span></label>
        <input type="text" name="name_en" id="name_en" value="{{ old('name_en', $category->name_en ?? '') }}"
            class="form-control @error('name_en') is-invalid @enderror" placeholder="{{ __('multi_media.name_en') }}">
        @error('name_en')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>


    {{-- رابط الفيديو البرومو --}}
    <div class="col-md-6">
        <label for="promo" class="form-label">{{ __('multi_media.promo_url') }}</label>
        <input type="url" name="promo" id="promo" value="{{ old('promo', $category->promo ?? '') }}"
            class="form-control @error('promo') is-invalid @enderror" placeholder="https://youtube.com/...">
        @error('promo')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    {{-- الشعار --}}
    <div class="col-md-6">
        <label for="logo" class="form-label">{{ __('multi_media.logo') }}</label>
        <input type="file" name="logo" id="logo" class="form-control @error('logo') is-invalid @enderror">
        @error('logo')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror

        @if (!empty($category->logo))
            <div class="mt-2">
                <img src="{{ asset($category->logo) }}" alt="logo" class="img-thumbnail"
                    style="width: 80px; height: auto;">
            </div>
        @endif
    </div>

    {{-- الحالة --}}
    <div class="col-md-6">
        <label for="active" class="form-label">{{ __('multi_media.active') }}</label>
        <select name="active" id="active" class="form-select">
            <option value="1" {{ old('active', $category->active ?? 1) == 1 ? 'selected' : '' }}>
                {{ __('multi_media.status_active') }}
            </option>
            <option value="0" {{ old('active', $category->active ?? 1) == 0 ? 'selected' : '' }}>
                {{ __('multi_media.status_inactive') }}
            </option>
        </select>
    </div>

    {{-- أزرار الحفظ والرجوع --}}
    <div class="col-12 mt-4 text-end">
        <button type="submit" class="btn btn-primary px-4">
            <i class="bi bi-save me-1"></i> {{ $button }}
        </button>
        <a href="{{ route('dashboard.multimedia-categories.index') }}" class="btn btn-outline-secondary ms-2 px-4">
            <i class="bi bi-arrow-left"></i> {{ __('multi_media.back') }}
        </a>
    </div>
</div>
