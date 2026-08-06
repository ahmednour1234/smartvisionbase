<div class="row g-4">
  {{-- العنوان والوصف (يُخفى إذا كان id = 2) --}}
  @if(empty($homeSection->id) || $homeSection->id != 2)
    <div class="col-md-6">
      <label class="form-label">{{ __('home_sections.title_ar') }}</label>
      <input type="text" name="title[ar]" class="form-control"
             value="{{ old('title.ar', $homeSection->title['ar'] ?? '') }}" required>
    </div>
    <div class="col-md-6">
      <label class="form-label">{{ __('home_sections.title_en') }}</label>
      <input type="text" name="title[en]" class="form-control"
             value="{{ old('title.en', $homeSection->title['en'] ?? '') }}" required>
    </div>

    <div class="col-md-6">
      <label class="form-label">{{ __('home_sections.description_ar') }}</label>
      <textarea name="description[ar]" class="form-control" rows="3">{{ old('description.ar', $homeSection->description['ar'] ?? '') }}</textarea>
    </div>
    <div class="col-md-6">
      <label class="form-label">{{ __('home_sections.description_en') }}</label>
      <textarea name="description[en]" class="form-control" rows="3">{{ old('description.en', $homeSection->description['en'] ?? '') }}</textarea>
    </div>
  @endif

  {{-- نوع الوسائط (يُخفى إذا كان id = 5 أو 8) --}}
  @if(empty($homeSection->id) || !in_array($homeSection->id, [5, 8]))
    <div class="col-md-4">
      <label class="form-label">{{ __('home_sections.media_type') }}</label>
      <select name="media_type" class="form-select" id="media_type_select" required>
        <option value="image" {{ old('media_type', $homeSection->media_type ?? '') == 'image' ? 'selected' : '' }}>
          {{ __('home_sections.image') }}
        </option>
        <option value="video" {{ old('media_type', $homeSection->media_type ?? '') == 'video' ? 'selected' : '' }}>
          {{ __('home_sections.video') }}
        </option>
        <option value="link" {{ old('media_type', $homeSection->media_type ?? '') == 'link' ? 'selected' : '' }}>
          {{ __('home_sections.link') }}
        </option>
      </select>
    </div>

    {{-- media input (file) --}}
    <div class="col-md-8 media-upload-section">
      <label class="form-label">{{ __('home_sections.media_path') }}</label>
      <input type="file" name="media" class="form-control" {{ isset($homeSection) ? '' : 'required' }}>
    </div>

    {{-- media link (text) --}}
    <div class="col-md-8 media-link-section d-none">
      <label class="form-label">{{ __('home_sections.media_link') }}</label>
      <input type="text" name="media_path" class="form-control"
             value="{{ old('media_path', $homeSection->media_path ?? '') }}" placeholder="https://example.com/video.mp4">
    </div>

    {{-- thumbnail upload --}}
    <div class="col-md-8 thumbnail-upload-section d-none">
      <label class="form-label">{{ __('home_sections.thumbnail') }}</label>
      <input type="file" name="thumbnail" class="form-control" accept="image/*">
    </div>

    {{-- عرض الملف الحالي --}}
    @if(isset($homeSection) && $homeSection->media_path)
      <div class="col-md-12">
        <div class="mt-3">
          {{ __('home_sections.current_file') }}:
          @if($homeSection->media_type === 'image')
            <img src="{{ asset($homeSection->media_path) }}" class="img-fluid rounded mt-2" style="max-height: 200px;" alt="Image">
          @elseif($homeSection->media_type === 'video')
            <div class="mt-3">
              <label class="form-label d-block">{{ __('home_sections.preview') }}</label>
              <div class="rounded overflow-hidden shadow-sm" style="max-height: 250px;">
                <video autoplay muted loop playsinline style="width: 100%; height: auto; object-fit: cover; display: block;">
                  <source src="{{ asset($homeSection->media_path) }}" type="video/mp4">
                  {{ __('home_sections.your_browser_does_not_support_video') }}
                </video>
              </div>
            </div>
          @elseif($homeSection->media_type === 'link')
            <div class="mt-2">
              <a href="{{ $homeSection->media_path }}" target="_blank">{{ $homeSection->media_path }}</a>
              @if($homeSection->thumbnail ?? false)
                <div class="mt-2">
                  <label>{{ __('home_sections.thumbnail') }}</label><br>
                  <img src="{{ asset($homeSection->thumbnail) }}" class="img-fluid rounded" style="max-height: 150px;">
                </div>
              @endif
            </div>
          @endif
        </div>
      </div>
    @endif
  @endif

  {{-- الترتيب --}}
  @if(empty($homeSection->id) || $homeSection->id != 2)
    <div class="col-md-4">
      <label class="form-label">{{ __('home_sections.section_order') }}</label>
      <input type="number" name="section_order" class="form-control"
             value="{{ old('section_order', $homeSection->section_order ?? 1) }}" required>
    </div>
  @endif

  {{-- حالة التفعيل --}}
  <div class="col-md-4">
    <label class="form-label d-block">{{ __('home_sections.is_active') }}</label>
    <input type="hidden" name="is_active" value="0">
    <div class="form-check form-switch">
      <input class="form-check-input" type="checkbox" name="is_active" value="1"
             {{ old('is_active', $homeSection->is_active ?? true) ? 'checked' : '' }}>
      <label class="form-check-label">{{ __('home_sections.active') }}</label>
    </div>
  </div>
</div>

{{-- سكريبت لتبديل عرض الحقول حسب نوع الوسائط --}}
<script>
  function toggleMediaFields() {
    const type = document.getElementById('media_type_select').value;
    const uploadSection = document.querySelector('.media-upload-section');
    const linkSection = document.querySelector('.media-link-section');
    const thumbSection = document.querySelector('.thumbnail-upload-section');

    if (type === 'link') {
      uploadSection.classList.add('d-none');
      linkSection.classList.remove('d-none');
      thumbSection.classList.remove('d-none');
    } else {
      uploadSection.classList.remove('d-none');
      linkSection.classList.add('d-none');
      thumbSection.classList.add('d-none');
    }
  }

  document.getElementById('media_type_select').addEventListener('change', toggleMediaFields);

  // تنفيذ عند التحميل لتفعيل الحالة الصحيحة
  document.addEventListener('DOMContentLoaded', toggleMediaFields);
</script>
