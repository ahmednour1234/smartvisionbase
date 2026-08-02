@extends('layouts.layoutMaster')

@section('title', __('becomesponsors.excel_tools'))

@section('content')
<div class="card">
  <div class="card-header">
    <h4>{{ __('becomesponsors.excel_tools') }}</h4>
  </div>

  <div class="card-body">
    <ul class="nav nav-tabs mb-4" id="excelTabs" role="tablist">
      <li class="nav-item" role="presentation">
        <button class="nav-link active" id="download-tab" data-bs-toggle="tab" data-bs-target="#download" type="button">
          {{ __('becomesponsors.download_template') }}
        </button>
      </li>
      <li class="nav-item" role="presentation">
        <button class="nav-link" id="upload-tab" data-bs-toggle="tab" data-bs-target="#upload" type="button">
          {{ __('becomesponsors.upload_excel') }}
        </button>
      </li>
      <li class="nav-item" role="presentation">
        <button class="nav-link" id="export-tab" data-bs-toggle="tab" data-bs-target="#export" type="button">
          {{ __('becomesponsors.export_data') }}
        </button>
      </li>
    </ul>

    <div class="tab-content" id="excelTabsContent">
      {{-- ✅ Tab 1: Download Template --}}
      <div class="tab-pane fade show active" id="download" role="tabpanel">
        <div class="alert alert-warning">
          <strong>{{ __('becomesponsors.download_alert') }}</strong>
        </div>

        <a href="{{ route('dashboard.becomesponsor.export.template') }}" class="btn btn-primary">
          {{ __('becomesponsors.download_template_btn') }}
        </a>
      </div>

      {{-- ✅ Tab 2: Upload Excel --}}
      <div class="tab-pane fade" id="upload" role="tabpanel">
        <div class="alert alert-info">
          {{ __('becomesponsors.upload_alert') }}
        </div>

        <form action="{{ route('dashboard.becomesponsor.import') }}" method="POST" enctype="multipart/form-data" class="mt-3">
          @csrf

          {{-- Select Form --}}
          <div class="mb-3">
            <label class="form-label">{{ __('becomesponsors.select_form') }}</label>
            <select name="form_id" class="form-select" required>
              <option value="">{{ __('becomesponsors.choose_form') }}</option>
              @foreach($forms as $form)
                <option value="{{ $form->id }}">{{ $form->number }}</option>
              @endforeach
            </select>
          </div>

          {{-- Upload File --}}
          <div class="mb-3">
            <label class="form-label">{{ __('becomesponsors.choose_excel_file') }}</label>
            <input type="file" name="file" class="form-control" accept=".xlsx,.csv" required>
          </div>

          <button type="submit" class="btn btn-success">{{ __('becomesponsors.import_btn') }}</button>
        </form>
      </div>

      {{-- ✅ Tab 3: Export Sponsors --}}
      <div class="tab-pane fade" id="export" role="tabpanel">
        <form action="{{ route('dashboard.becomesponsor.export') }}" method="GET" class="mt-3">
          <div class="row g-3">
            <div class="col-md-4">
              <label>{{ __('becomesponsors.form') }}</label>
              <select name="form_id" class="form-select">
                <option value="">{{ __('becomesponsors.all_forms') }}</option>
                @foreach($forms as $form)
                  <option value="{{ $form->id }}">{{ $form->number }}</option>
                @endforeach
              </select>
            </div>

            <div class="col-md-4">
              <label>{{ __('becomesponsors.date_from') }}</label>
              <input type="date" name="date_from" class="form-control">
            </div>

            <div class="col-md-4">
              <label>{{ __('becomesponsors.date_to') }}</label>
              <input type="date" name="date_to" class="form-control">
            </div>
          </div>

          <div class="text-end mt-3">
            <button type="submit" class="btn btn-info">{{ __('becomesponsors.export_btn') }}</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
