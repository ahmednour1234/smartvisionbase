@php $isEdit = isset($event); @endphp



  <meta name="csrf-token" content="{{ csrf_token() }}">

  <div class="row g-3">
    <div class="col-md-6">
      <label class="form-label">{{ __('event.name_ar') }}</label>
      <input type="text" name="name_ar" class="form-control" value="{{ old('name_ar', $isEdit ? $event->name_ar : '') }}" required>
    </div>
    <div class="col-md-6">
      <label class="form-label">{{ __('event.name_en') }}</label>
      <input type="text" name="name_en" class="form-control" value="{{ old('name_en', $isEdit ? $event->name_en : '') }}" required>
    </div>

    <div class="col-md-6">
      <label class="form-label">{{ __('event.description_ar') }}</label>
      <textarea
        name="description_ar"
        class="form-control html-editor"
        rows="10"
        data-upload-url="{{ route('editor.upload') }}"
      >{!! old('description_ar', $isEdit ? ($event->description_ar ?? '') : '') !!}</textarea>
    </div>

    <div class="col-md-6">
      <label class="form-label">{{ __('event.description_en') }}</label>
      <textarea
        name="description_en"
        class="form-control html-editor"
        rows="10"
        data-upload-url="{{ route('editor.upload') }}"
      >{!! old('description_en', $isEdit ? ($event->description_en ?? '') : '') !!}</textarea>
    </div>

    <div class="col-md-6">
      <label class="form-label">{{ __('event.date') }}</label>
      <input type="datetime-local" name="event_date" class="form-control"
             value="{{ old('event_date', $isEdit && $event->event_date ? \Carbon\Carbon::parse($event->event_date)->format('Y-m-d\TH:i') : '') }}"
             required>
    </div>

    <div class="col-md-6">
      <label class="form-label">{{ __('event.end_date') }}</label>
      <input type="datetime-local" name="end_date" class="form-control"
             value="{{ old('end_date', $isEdit && $event->end_date ? \Carbon\Carbon::parse($event->end_date)->format('Y-m-d\TH:i') : '') }}"
             required>
    </div>

    <div class="col-md-6">
      <label class="form-label">{{ __('event.attendees_limit') }}</label>
      <input type="number" name="attendees_limit" min="0" class="form-control" value="{{ old('attendees_limit', $isEdit ? $event->attendees_limit : '') }}">
    </div>

    <div class="col-md-6">
      <label class="form-label">{{ __('event.address_ar') }}</label>
      <input type="text" name="address_ar" class="form-control" value="{{ old('address_ar', $isEdit ? $event->address_ar : '') }}" required>
    </div>
    <div class="col-md-6">
      <label class="form-label">{{ __('event.address_en') }}</label>
      <input type="text" name="address_en" class="form-control" value="{{ old('address_en', $isEdit ? $event->address_en : '') }}" required>
    </div>

    <div class="col-md-6">
      <label class="form-label">{{ __('event.location') }}</label>
      <input type="text" name="location" class="form-control" value="{{ old('location', $isEdit ? $event->location : '') }}">
    </div>

    <div class="col-md-6">
      <label class="form-label">{{ __('event.image') }}</label>
      <input type="file" name="main_image" class="form-control" accept="image/*">
      @if($isEdit && $event->main_image)
        <img src="{{ \Illuminate\Support\Facades\Storage::url($event->main_image) }}" class="img-thumbnail mt-2" width="120" alt="event image">
      @endif
    </div>

    <div class="col-md-6">
      <label for="text_email" class="form-label">{{ __('text_email') }}</label>
      <textarea
        name="text_email"
        id="text_email"
        class="form-control html-editor"
        rows="10"
        data-upload-url="{{ route('editor.upload') }}"
      >{!! old('text_email', $isEdit ? ($event->text_email ?? '') : '') !!}</textarea>
    </div>

    <div class="col-md-6">
      <label class="form-label">{{ __('event.active') }}</label>
      <select name="active" class="form-select">
        <option value="1" {{ old('active', $isEdit ? $event->active : 1) == 1 ? 'selected' : '' }}>{{ __('event.active') }}</option>
        <option value="0" {{ old('active', $isEdit ? $event->active : 1) == 0 ? 'selected' : '' }}>{{ __('event.inactive') }}</option>
      </select>
    </div>
  </div>



<script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>
<script>
  class MyUploadAdapter {
    constructor(loader, uploadUrl, csrf) {
      this.loader = loader;
      this.uploadUrl = uploadUrl;
      this.csrf = csrf;
      this.xhr = null;
    }

    upload() {
      return this.loader.file.then(file => new Promise((resolve, reject) => {
        this._initRequest();
        this._initListeners(resolve, reject);
        this._sendFile(file);
      }));
    }

    abort() { if (this.xhr) this.xhr.abort(); }

    _initRequest() {
      this.xhr = new XMLHttpRequest();
      this.xhr.open('POST', this.uploadUrl, true);
      this.xhr.responseType = 'json';
      this.xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
      if (this.csrf) this.xhr.setRequestHeader('X-CSRF-TOKEN', this.csrf);
    }

    _pickUrl(res) {
      let url = res?.url || res?.location || res?.default;
      if (!url) return null;

      // Fix mixed-content: لو الصفحة https والصورة http على نفس الدومين
      try {
        const pageIsHttps = location.protocol === 'https:';
        const u = new URL(url, location.origin);
        if (pageIsHttps && u.protocol === 'http:') {
          u.protocol = 'https:'; // جرّب الترقية لو نفس الهوست بيدعم https
          url = u.toString();
        }
      } catch (_) {}
      return url;
    }

    _initListeners(resolve, reject) {
      const xhr = this.xhr;

      xhr.addEventListener('error', () => reject('Upload failed.'));
      xhr.addEventListener('abort', () => reject('Upload aborted.'));
      xhr.addEventListener('load', () => {
        const res = xhr.response;
        if (!res) return reject('Empty response');

        // حاول التقاط مسج خطأ واضحة
        if (xhr.status !== 200 || res.error) {
          const msg = res?.error?.message || `Upload error (status ${xhr.status})`;
          console.error('CKEditor upload error:', res);
          return reject(msg);
        }

        const url = this._pickUrl(res);
        if (!url) {
          console.error('Unexpected upload response shape:', res);
          return reject('Invalid upload response.');
        }

        // CKEditor expects { default: 'URL' }
        resolve({ default: url });
      });

      if (xhr.upload) {
        xhr.upload.addEventListener('progress', evt => {
          if (evt.lengthComputable) {
            this.loader.uploadTotal = evt.total;
            this.loader.uploaded = evt.loaded;
          }
        });
      }
    }

    _sendFile(file) {
      const data = new FormData();
      data.append('upload', file);
      this.xhr.send(data);
    }
  }

  function MyUploadAdapterPlugin(editor) {
    const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    editor.plugins.get('FileRepository').createUploadAdapter = loader => {
      const el = editor.sourceElement;
      const uploadUrl = el.getAttribute('data-upload-url');
      return new MyUploadAdapter(loader, uploadUrl, csrf);
    };
  }

  document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.html-editor').forEach((el) => {
      ClassicEditor.create(el, {
        extraPlugins: [MyUploadAdapterPlugin],
        toolbar: {
          items: [
            'heading','|','bold','italic','underline','link','|',
            'bulletedList','numberedList','blockQuote','|',
            'imageUpload','insertImage','|',
            'undo','redo'
          ]
        },
        image: {
          toolbar: [
            'imageTextAlternative','toggleImageCaption',
            'imageStyle:inline','imageStyle:block','imageStyle:side'
          ]
        }
      }).catch(error => console.error('CKEditor init error:', error));
    });
  });
</script>
