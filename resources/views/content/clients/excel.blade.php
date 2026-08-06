@extends('layouts.layoutMaster')

@section('title', __('clients.excel_tools'))

@section('content')
<div class="card">
  <div class="card-header">
    <h4>{{ __('clients.excel_tools') }}</h4>
  </div>

  <div class="card-body">
    <ul class="nav nav-tabs mb-4" id="excelTabs" role="tablist">
      <li class="nav-item" role="presentation">
        <button class="nav-link active" id="download-tab" data-bs-toggle="tab" data-bs-target="#download" type="button">
          {{ __('clients.download_template') }}
        </button>
      </li>
      <li class="nav-item" role="presentation">
        <button class="nav-link" id="upload-tab" data-bs-toggle="tab" data-bs-target="#upload" type="button">
          {{ __('clients.upload_excel') }}
        </button>
      </li>
      <li class="nav-item" role="presentation">
        <button class="nav-link" id="export-tab" data-bs-toggle="tab" data-bs-target="#export" type="button">
          {{ __('clients.export_clients') }}
        </button>
      </li>
    </ul>

    <div class="tab-content" id="excelTabsContent">
      {{-- ✅ Tab 1: تحميل النموذج --}}
      <div class="tab-pane fade show active" id="download" role="tabpanel">
        <div class="alert alert-warning">
          <strong>{{ __('clients.download_alert') }}</strong>
        </div>

        <a href="{{ route('dashboard.clients.export.template') }}" class="btn btn-primary">
          {{ __('clients.download_template_btn') }}
        </a>
      </div>

      {{-- ✅ Tab 2: رفع ملف إكسل --}}
      <div class="tab-pane fade" id="upload" role="tabpanel">
        <div class="alert alert-info">
          {{ __('clients.upload_alert') }}
        </div>

        <form action="{{ route('dashboard.clients.import') }}" method="POST" enctype="multipart/form-data" class="mt-3">
          @csrf

          {{-- اختيار النموذج --}}
          <div class="mb-3">
            <label class="form-label">{{ __('clients.select_form') }}</label>
            <select name="form_id" class="form-select" required>
              <option value="">{{ __('clients.choose_form') }}</option>
              @foreach($forms as $form)
                <option value="{{ $form->id }}">{{ $form->number }}</option>
              @endforeach
            </select>
          </div>

          {{-- رفع الملف --}}
          <div class="mb-3">
            <label class="form-label">{{ __('clients.choose_excel_file') }}</label>
            <input type="file" name="file" class="form-control" accept=".xlsx,.csv" required>
          </div>

          <button type="submit" class="btn btn-success">{{ __('clients.import_btn') }}</button>
        </form>
      </div>

      {{-- ✅ Tab 3: تصدير العملاء --}}
      <div class="tab-pane fade" id="export" role="tabpanel">
        <form action="{{ route('dashboard.clients.export') }}" method="GET" class="mt-3">
          <div class="row g-3">
            <div class="col-md-4">
              <label>{{ __('clients.form') }}</label>
              <select name="form_id" class="form-select">
                <option value="">{{ __('clients.all_forms') }}</option>
                @foreach($forms as $form)
                  <option value="{{ $form->id }}">{{ $form->number }}</option>
                @endforeach
              </select>
            </div>

            <div class="col-md-4">
              <label>{{ __('clients.date_from') }}</label>
              <input type="date" name="date_from" class="form-control">
            </div>

            <div class="col-md-4">
              <label>{{ __('clients.date_to') }}</label>
              <input type="date" name="date_to" class="form-control">
            </div>
          </div>

          <div class="text-end mt-3">
            <button type="submit" class="btn btn-info">{{ __('clients.export_btn') }}</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
