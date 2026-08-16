@php
    /** @var \App\Models\MultiMedia|null $media */
    $isEdit = isset($media);
@endphp

<div class="row gy-3">
  {{-- الاسم بالعربية --}}
  <div class="col-md-6">
    <label class="form-label">{{ __('multi_media.name_ar') }}</label>
    <input type="text" name="name_ar"
           class="form-control @error('name_ar') is-invalid @enderror"
           value="{{ old('name_ar', $isEdit ? $media->name_ar : '') }}">
    @error('name_ar') <div class="invalid-feedback">{{ $message }}</div> @enderror
  </div>

  {{-- الاسم بالإنجليزية --}}
  <div class="col-md-6">
    <label class="form-label">{{ __('multi_media.name_en') }}</label>
    <input type="text" name="name_en"
           class="form-control @error('name_en') is-invalid @enderror"
           value="{{ old('name_en', $isEdit ? $media->name_en : '') }}">
    @error('name_en') <div class="invalid-feedback">{{ $message }}</div> @enderror
  </div>

  {{-- التاريخ --}}
  <div class="col-md-6">
    <label class="form-label">{{ __('multi_media.date') }}</label>
    <input type="date" name="date"
           class="form-control @error('date') is-invalid @enderror"
           value="{{ old('date', $isEdit && $media->date ? $media->date->format('Y-m-d') : '') }}">
    @error('date') <div class="invalid-feedback">{{ $message }}</div> @enderror
  </div>

  {{-- التصنيف --}}
  <div class="col-md-6">
    <label class="form-label">{{ __('multi_media.category') }}</label>
    <select name="multi_media_category_id"
            class="form-select @error('multi_media_category_id') is-invalid @enderror">
      <option value="">{{ __('multi_media.select_category') }}</option>
      @foreach($categories as $id => $name)
        <option value="{{ $id }}"
          {{ old('multi_media_category_id', $isEdit ? $media->multi_media_category_id : '') == $id ? 'selected' : '' }}>
          {{ $name }}
        </option>
      @endforeach
    </select>
    @error('multi_media_category_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
  </div>

  {{-- الصور (رفع جديدة + عرض الحالية مع زر حذف لكل صورة) --}}
  <div class="col-md-6">
    <label class="form-label">{{ __('multi_media.images') }}</label>
    <input type="file" name="images[]"
           class="form-control @error('images') is-invalid @enderror"
           multiple accept="image/*">
    @error('images') <div class="invalid-feedback">{{ $message }}</div> @enderror
    @error('images.*') <div class="invalid-feedback">{{ $message }}</div> @enderror

    @if($isEdit && is_array($media->images) && count($media->images))
      <div class="mt-3 d-flex flex-wrap gap-2">
        @foreach($media->images as $imgIndex => $img)
          <div class="position-relative" style="width: 80px; height: 80px;">
            <img src="{{ asset('public/'.$img) }}"
                 alt="Image"
                 width="80" height="80"
                 style="object-fit: cover; border-radius: 5px;">

            <form method="POST"
                  action="{{ route('multi-medias.images.destroy', [$media->id, $imgIndex]) }}"
                  class="position-absolute top-0 end-0"
                  onsubmit="return confirm('{{ __('multi_media.confirm_delete_image') }}');">
              @csrf
              @method('DELETE')
              <button type="submit"
                      class="btn btn-sm btn-danger"
                      style="padding:0 4px; border-radius:50%;">
                &times;
              </button>
            </form>
          </div>
        @endforeach
      </div>
    @endif
  </div>

  {{-- روابط الفيديو (موجودة + زر حذف لكل لينك + حقل جديد) --}}
  <div class="col-md-6">
    <label class="form-label">{{ __('multi_media.links') }}</label>

    @php
      $links = old('links', $isEdit ? $media->links : ['']);
      if (!is_array($links)) {
          $links = [$links];
      }
    @endphp

    <div class="d-flex flex-column gap-2">
      {{-- روابط موجودة (في حالة edit) --}}
      @if($isEdit && is_array($media->links) && count($media->links))
        @foreach($media->links as $index => $link)
          <div class="input-group">
            <input type="url"
                   name="links[{{ $index }}]"
                   class="form-control @error("links.$index") is-invalid @enderror"
                   value="{{ old('links.'.$index, $link) }}"
                   placeholder="https://youtube.com/...">
            @error("links.$index") <div class="invalid-feedback d-block">{{ $message }}</div> @enderror

            <button type="submit"
                    form="delete-link-{{ $index }}"
                    class="btn btn-outline-danger"
                    onclick="return confirm('{{ __('multi_media.confirm_delete_link') }}');">
              <i class="bi bi-x"></i>
            </button>
          </div>

          <form id="delete-link-{{ $index }}"
                method="POST"
                action="{{ route('multi-medias.links.destroy', [$media->id, $index]) }}">
            @csrf
            @method('DELETE')
          </form>
        @endforeach
      @else
        {{-- لو مفيش لينكات محفوظة، هنستخدم old فقط --}}
        @foreach($links as $index => $link)
          <input type="url"
                 name="links[{{ $index }}]"
                 class="form-control @error("links.$index") is-invalid @enderror"
                 value="{{ $link }}"
                 placeholder="https://youtube.com/...">
          @error("links.$index") <div class="invalid-feedback">{{ $message }}</div> @enderror
        @endforeach
      @endif

      {{-- حقل فارغ إضافي للروابط الجديدة (دائمًا موجود) --}}
      <input type="url" name="links[]"
             class="form-control"
             placeholder="https://youtube.com/...">
    </div>
  </div>

  {{-- الحالة --}}
  <div class="col-md-6">
    <label class="form-label">{{ __('multi_media.active') }}</label>
    <select name="active"
            class="form-select @error('active') is-invalid @enderror">
      <option value="1" {{ old('active', $isEdit ? $media->active : 1) == 1 ? 'selected' : '' }}>
        {{ __('multi_media.status_active') }}
      </option>
      <option value="0" {{ old('active', $isEdit ? $media->active : 1) == 0 ? 'selected' : '' }}>
        {{ __('multi_media.status_inactive') }}
      </option>
    </select>
    @error('active') <div class="invalid-feedback">{{ $message }}</div> @enderror
  </div>

  {{-- زر الحفظ + الرجوع --}}
  <div class="col-12 text-end mt-4">
    <button type="submit" class="btn btn-primary px-4">
      <i class="bi bi-save me-1"></i> {{ $button }}
    </button>
    <a href="{{ route('dashboard.multi-medias.index') }}" class="btn btn-outline-secondary px-4">
      {{ __('multi_media.back') }}
    </a>
  </div>
</div>
