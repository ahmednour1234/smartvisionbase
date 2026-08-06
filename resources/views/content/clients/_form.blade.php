@php $isEdit = isset($client); @endphp

<div class="row g-4">

  {{-- Full Name --}}
  <div class="col-md-6">
    <label for="name" class="form-label">{{ __('registrations.name') }}</label>
    <input type="text" name="name" class="form-control" placeholder="{{ __('registrations.name') }}"
           value="{{ old('name', $isEdit ? $client->name : '') }}" required>
  </div>

  {{-- Email --}}
  <div class="col-md-6">
    <label for="email" class="form-label">{{ __('registrations.email') }}</label>
    <input type="email" name="email" class="form-control" placeholder="{{ __('registrations.email') }}"
           value="{{ old('email', $isEdit ? $client->email : '') }}" required>
  </div>

  {{-- Phone --}}
  <div class="col-md-6">
    <label for="phone" class="form-label">{{ __('registrations.phone') }}</label>
    <input type="text" name="phone" class="form-control" placeholder="{{ __('registrations.phone') }}"
           value="{{ old('phone', $isEdit ? $client->phone : '') }}">
  </div>

  {{-- Country Code --}}
  <div class="col-md-6">
    <label for="country_code" class="form-label">{{ __('registrations.country_code') }}</label>
    <select class="form-select" name="country_code" id="country_code" required>
      <option value="">{{ __('registrations.select_country_code') }}</option>
      @php
        $codes = [
          '+20' => 'Egypt', '+966' => 'Saudi Arabia', '+971' => 'UAE', '+965' => 'Kuwait',
          '+964' => 'Iraq', '+963' => 'Syria', '+962' => 'Jordan', '+968' => 'Oman',
          '+973' => 'Bahrain', '+974' => 'Qatar', '+212' => 'Morocco', '+1' => 'USA', '+44' => 'UK'
        ];
      @endphp
      @foreach ($codes as $code => $label)
        <option value="{{ $code }}" {{ old('country_code', $isEdit ? $client->country_code : '') == $code ? 'selected' : '' }}>
          {{ $label }} ({{ $code }})
        </option>
      @endforeach
    </select>
  </div>

  {{-- Job Title --}}
  <div class="col-md-6">
    <label for="job" class="form-label">{{ __('registrations.job') }}</label>
    <input type="text" name="job" class="form-control" placeholder="{{ __('registrations.job') }}"
           value="{{ old('job', $isEdit ? $client->job : '') }}">
  </div>

  {{-- Status --}}
  <div class="col-md-6">
    <label for="active" class="form-label">{{ __('registrations.active') }}</label>
    <select name="active" class="form-select">
      <option value="1" {{ old('active', $isEdit ? $client->active : 1) == 1 ? 'selected' : '' }}>
        {{ __('registrations.active_yes') }}
      </option>
      <option value="0" {{ old('active', $isEdit ? $client->active : 1) == 0 ? 'selected' : '' }}>
        {{ __('registrations.active_no') }}
      </option>
    </select>
  </div>

  {{-- Form ID --}}
  <div class="col-md-6">
    <label for="form_id" class="form-label">{{ __('registrations.form') }}</label>
    <select name="form_id" class="form-select" required>
      <option value="">{{ __('registrations.select_form') }}</option>
      @foreach($forms as $form)
        <option value="{{ $form->id }}" {{ old('form_id', $isEdit ? $client->form_id : '') == $form->id ? 'selected' : '' }}>
          {{ $form->number }}
        </option>
      @endforeach
    </select>
  </div>
                    <input type="hidden" name="type" value="1">

  {{-- Image --}}
  <div class="col-md-6">
    <label for="img" class="form-label">{{ __('registrations.image') }}</label>
    <input type="file" name="img" class="form-control">
    @if ($isEdit && $client->img)
      <div class="mt-2">
        <img src="{{ asset($client->img) }}" class="img-thumbnail shadow-sm border border-primary" width="120">
      </div>
    @endif
  </div>

</div>
