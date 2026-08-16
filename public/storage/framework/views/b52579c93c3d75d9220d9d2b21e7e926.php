

<?php $__env->startSection('title', 'Application Settings'); ?>

<?php
  $locale     = app()->getLocale();
  $GMAPS_KEY  = env('GOOGLE_MAPS_KEY', 'AIzaSyAQgTQ30_TriFBdJPKKOK4zZQ8rfHCUk6c'); // عدّل من .env
  $settings   = $settings ?? (object)[];
  $socials    = ['facebook','instagram','linkedin','x','youtube','flickr'];
?>

<?php $__env->startSection('vendor-style'); ?>
  <link rel="stylesheet" href="<?php echo e(asset('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css')); ?>">
  <link rel="stylesheet" href="<?php echo e(asset('assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css')); ?>">
  <link rel="stylesheet" href="<?php echo e(asset('assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css')); ?>">
  <link rel="stylesheet" href="<?php echo e(asset('assets/vendor/libs/select2/select2.css')); ?>">
  <link rel="stylesheet" href="<?php echo e(asset('assets/vendor/libs/@form-validation/umd/styles/index.min.css')); ?>">
  <style>
    .settings-card { border: 1px solid #eef0f3; border-radius: 14px; box-shadow: 0 6px 20px rgba(18, 38, 63, 0.05); }
    .settings-header { padding: 1.25rem 1.5rem; border-bottom: 1px solid #eef0f3; background: linear-gradient(135deg, #f7f9fc, #ffffff); border-radius: 14px 14px 0 0; }
    .settings-title { display:flex; align-items:center; gap:.6rem; font-weight:700; margin:0; }
    .settings-title i { color:#556ee6; }
    .nav-pills .nav-link { border-radius: 999px; }
    .nav-pills .nav-link.active { background: #556ee6; }
    .section-hint { font-size:.875rem; color:#6c757d; }
    .form-label .req { color:#e74c3c; }
    .thumb { width: 120px; height: 120px; object-fit: cover; border-radius: 12px; border:1px solid #e9ecef; }
    #map { height: 340px; width: 100%; border-radius: 12px; border: 1px solid #e9ecef; }
    .input-icon { position:relative; }
    .input-icon i { position:absolute; left: .75rem; top: 50%; transform: translateY(-50%); color:#9aa0a6; }
    .input-icon input { padding-left: 2.25rem; }
    .small-muted { font-size: .8rem; color:#8a94a6; }
    .divider { height:1px; background:#eef0f3; margin: 1rem 0; }
  </style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>

<?php if($errors->any()): ?>
  <div class="alert alert-danger alert-dismissible" role="alert">
    <strong><?php echo app('translator')->get('There are some errors'); ?></strong>
    <ul class="mb-0 ps-3">
      <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <li><?php echo e($error); ?></li>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </ul>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="<?php echo app('translator')->get('Close'); ?>"></button>
  </div>
<?php endif; ?>

<div class="card settings-card mb-4">
  <div class="settings-header">
    <h5 class="settings-title">
      <i class="ti ti-settings"></i> <?php echo app('translator')->get('Application Settings'); ?>
    </h5>
    <p class="mb-0 section-hint"><?php echo app('translator')->get('Update app info, branding, social links, features toggles, and location.'); ?></p>
  </div>

  <div class="card-body pt-2">
    <ul class="nav nav-pills mb-4 flex-wrap" id="settingsTabs" role="tablist">
      <li class="nav-item me-2 mb-2">
        <button class="nav-link active" id="tab-general" data-bs-toggle="pill" data-bs-target="#p-general" type="button" role="tab">
          <i class="ti ti-info-circle me-1"></i><?php echo app('translator')->get('General'); ?>
        </button>
      </li>
      <li class="nav-item me-2 mb-2">
        <button class="nav-link" id="tab-brand" data-bs-toggle="pill" data-bs-target="#p-brand" type="button" role="tab">
          <i class="ti ti-photo me-1"></i><?php echo app('translator')->get('Branding'); ?>
        </button>
      </li>
      <li class="nav-item me-2 mb-2">
        <button class="nav-link" id="tab-social" data-bs-toggle="pill" data-bs-target="#p-social" type="button" role="tab">
          <i class="ti ti-share me-1"></i><?php echo app('translator')->get('Social Links'); ?>
        </button>
      </li>
      <li class="nav-item me-2 mb-2">
        <button class="nav-link" id="tab-features" data-bs-toggle="pill" data-bs-target="#p-features" type="button" role="tab">
          <i class="ti ti-toggle-left me-1"></i><?php echo app('translator')->get('Features'); ?>
        </button>
      </li>
      <li class="nav-item mb-2">
        <button class="nav-link" id="tab-location" data-bs-toggle="pill" data-bs-target="#p-location" type="button" role="tab">
          <i class="ti ti-map-pin me-1"></i><?php echo app('translator')->get('Location'); ?>
        </button>
      </li>
    </ul>

    <form method="POST" action="<?php echo e(route('dashboard-setting-store')); ?>" enctype="multipart/form-data">
      <?php echo csrf_field(); ?>

      <div class="tab-content" id="settingsTabsContent">

        
        <div class="tab-pane fade show active" id="p-general" role="tabpanel" aria-labelledby="tab-general">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">
                <?php echo app('translator')->get('Application Name'); ?> <span class="req">*</span>
              </label>
              <div class="input-icon">
                <i class="ti ti-apps"></i>
                <input type="text" class="form-control" name="name" required
                       placeholder="<?php echo app('translator')->get('Application Name'); ?>"
                       value="<?php echo e(old('name', $settings->name ?? '')); ?>">
              </div>
            </div>

            <div class="col-md-6">
              <label class="form-label"><?php echo app('translator')->get('Phone'); ?></label>
              <div class="input-icon">
                <i class="ti ti-phone"></i>
                <input type="text" class="form-control" name="phone"
                       placeholder="<?php echo app('translator')->get('Phone'); ?>"
                       value="<?php echo e(old('phone', $settings->phone ?? '')); ?>">
              </div>
            </div>

            <div class="col-md-6">
              <label class="form-label"><?php echo app('translator')->get('Email'); ?></label>
              <div class="input-icon">
                <i class="ti ti-mail"></i>
                <input type="email" class="form-control" name="email"
                       placeholder="email@domain.com"
                       value="<?php echo e(old('email', $settings->email ?? '')); ?>">
              </div>
            </div>

            <div class="col-md-6">
              <label class="form-label"><?php echo app('translator')->get('Address'); ?></label>
              <div class="input-icon">
                <i class="ti ti-map"></i>
                <input type="text" class="form-control" name="address"
                       placeholder="<?php echo app('translator')->get('Address'); ?>"
                       value="<?php echo e(old('address', $settings->address ?? '')); ?>">
              </div>
            </div>

            <div class="divider"></div>

            <div class="col-md-4">
              <label class="form-label"><?php echo app('translator')->get('Privacy'); ?></label>
              <input type="text" class="form-control" name="privacy"
                     placeholder="<?php echo app('translator')->get('Privacy'); ?>"
                     value="<?php echo e(old('privacy', $settings->privacy ?? '')); ?>">
              <small class="small-muted"><?php echo app('translator')->get('You can put a short text or a URL.'); ?></small>
            </div>

            <div class="col-md-4">
              <label class="form-label"><?php echo app('translator')->get('About'); ?></label>
              <input type="text" class="form-control" name="about"
                     placeholder="<?php echo app('translator')->get('About'); ?>"
                     value="<?php echo e(old('about', $settings->about ?? '')); ?>">
            </div>

            <div class="col-md-4">
              <label class="form-label"><?php echo app('translator')->get('Terms'); ?></label>
              <input type="text" class="form-control" name="terms"
                     placeholder="<?php echo app('translator')->get('Terms'); ?>"
                     value="<?php echo e(old('terms', $settings->terms ?? '')); ?>">
            </div>
          </div>
        </div>

        
        <div class="tab-pane fade" id="p-brand" role="tabpanel" aria-labelledby="tab-brand">
          <div class="row g-4 align-items-start">
            <div class="col-md-6">
              <label class="form-label"><?php echo app('translator')->get('Logo Image'); ?></label>
              <input type="file" class="form-control" name="img" accept="image/*" id="logoInput">
              <small class="small-muted d-block mt-1"><?php echo app('translator')->get('Recommended: PNG, 512x512 or higher'); ?></small>
              <div class="mt-3 d-flex align-items-center gap-3">
                <img id="logoPreview" class="thumb d-none" alt="logo preview">
                <?php if(!empty($settings->img)): ?>
                  <div class="d-flex flex-column">
                    <span class="small-muted"><?php echo app('translator')->get('Current'); ?></span>
                    <img src="<?php echo e(asset('public/'.$settings->img)); ?>" class="thumb" alt="Logo">
                  </div>
                <?php endif; ?>
              </div>
            </div>

            <div class="col-md-6">
              <label class="form-label"><?php echo app('translator')->get('Floor Plan'); ?></label>
              <input type="file" class="form-control" name="floor_plan" accept="image/*" id="floorInput">
              <small class="small-muted d-block mt-1"><?php echo app('translator')->get('Optional image to show event/site floor map.'); ?></small>
              <div class="mt-3 d-flex align-items-center gap-3">
                <img id="floorPreview" class="thumb d-none" alt="floor preview">
                <?php if(!empty($settings->floor_plan)): ?>
                  <div class="d-flex flex-column">
                    <span class="small-muted"><?php echo app('translator')->get('Current'); ?></span>
                    <img src="<?php echo e(asset('public/'.$settings->floor_plan)); ?>" class="thumb" alt="Floor Plan">
                  </div>
                <?php endif; ?>
              </div>
            </div>
                   <div class="col-md-6">
              <label class="form-label"><?php echo app('translator')->get('img_mobile'); ?></label>
              <input type="file" class="form-control" name="img_mobile" accept="image/*" id="img_mobile">
              <small class="small-muted d-block mt-1"><?php echo app('translator')->get('Optional image to show event/site img_mobile map.'); ?></small>
              <div class="mt-3 d-flex align-items-center gap-3">
                <img id="img_mobile" class="thumb d-none" alt="img_mobile">
                <?php if(!empty($settings->img_mobile)): ?>
                  <div class="d-flex flex-column">
                    <span class="small-muted"><?php echo app('translator')->get('Current'); ?></span>
                    <img src="<?php echo e(asset('public/'.$settings->img_mobile)); ?>" class="thumb" alt="Floor Plan">
                  </div>
                <?php endif; ?>
              </div>
            </div>
          </div>
        </div>

        
        <div class="tab-pane fade" id="p-social" role="tabpanel" aria-labelledby="tab-social">
          <div class="row g-3">
            <?php $__currentLoopData = $socials; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $platform): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <div class="col-md-6">
                <label class="form-label text-capitalize"><?php echo e(ucfirst($platform)); ?></label>
                <div class="input-icon">
                  <i class="ti ti-world"></i>
                  <input type="url" class="form-control" name="<?php echo e($platform); ?>"
                         placeholder="https://<?php echo e($platform); ?>.com/..."
                         value="<?php echo e(old($platform, $settings->$platform ?? '')); ?>">
                </div>
              </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </div>
        </div>

        
        <div class="tab-pane fade" id="p-features" role="tabpanel" aria-labelledby="tab-features">
          <div class="row g-3">
            <div class="col-md-6 d-flex align-items-center">
              <div class="form-check form-switch">
                <input type="hidden" name="voting" value="0">
                <input class="form-check-input" type="checkbox" id="votingSwitch" name="voting" value="1"
                       <?php echo e(old('voting', $settings->voting ?? 0) ? 'checked' : ''); ?>>
                <label class="form-check-label ms-2" for="votingSwitch">
                  <i class="ti ti-hand-click me-1"></i><?php echo app('translator')->get('Enable Voting'); ?>
                </label>
              </div>
            </div>
          </div>
        </div>

        
        <div class="tab-pane fade" id="p-location" role="tabpanel" aria-labelledby="tab-location">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label"><?php echo app('translator')->get('Latitude'); ?></label>
              <div class="input-icon">
                <i class="ti ti-compass"></i>
                <input type="text" id="lat" name="lat" class="form-control"
                       value="<?php echo e(old('lat', $settings->lat ?? '')); ?>"
                       placeholder="e.g. 30.0444">
              </div>
            </div>

            
            <div class="col-md-6">
              <label class="form-label"><?php echo app('translator')->get('Longitude'); ?></label>
              <div class="input-icon">
                <i class="ti ti-compass"></i>
                <input type="text" id="lng" name="lang" class="form-control"
                       value="<?php echo e(old('lang', $settings->lang ?? '')); ?>"
                       placeholder="e.g. 31.2357">
              </div>
            </div>

            <div class="col-12">
              <div id="map"></div>
              <small class="small-muted d-block mt-2">
                <?php echo app('translator')->get('Drag the marker or click on the map to update coordinates.'); ?>
              </small>
            </div>
          </div>
        </div>

      </div>

      <div class="mt-4 d-flex justify-content-end gap-2">
        <a href="<?php echo e(url()->previous()); ?>" class="btn btn-outline-secondary">
          <i class="ti ti-arrow-left"></i> <?php echo app('translator')->get('Back'); ?>
        </a>
        <button type="submit" class="btn btn-primary">
          <i class="ti ti-device-floppy"></i> <?php echo app('translator')->get('Save Changes'); ?>
        </button>
      </div>
    </form>
  </div>
</div>
<?php $__env->stopSection(); ?>

<script>
  // ===== Image live preview =====
  const previewImage = (input, previewEl) => {
    const file = input?.files?.[0];
    if (!file) { previewEl.classList.add('d-none'); return; }
    const reader = new FileReader();
    reader.onload = e => {
      previewEl.src = e.target.result;
      previewEl.classList.remove('d-none');
    };
    reader.readAsDataURL(file);
  };

  document.addEventListener('DOMContentLoaded', () => {
    const logoInput   = document.getElementById('logoInput');
    const logoPreview = document.getElementById('logoPreview');
    const floorInput  = document.getElementById('floorInput');
    const floorPreview= document.getElementById('floorPreview');

    if (logoInput && logoPreview) {
      logoInput.addEventListener('change', () => previewImage(logoInput, logoPreview));
    }
    if (floorInput && floorPreview) {
      floorInput.addEventListener('change', () => previewImage(floorInput, floorPreview));
    }

    // Optional: Init Select2 if needed
    if (window.jQuery && jQuery.fn.select2) {
      jQuery('.select2').select2({ width: '100%' });
    }

    // Optional: DataTables if a table with id exists
    const table = document.getElementById('branches-table');
    if (table && window.jQuery && jQuery.fn.DataTable) {
      jQuery(table).DataTable({
        paging: false, searching: false, info: false, ordering: false
      });
    }
  });

  // ===== Google Map =====
  function initMap() {
    const mapEl = document.getElementById('map');
    const latEl = document.getElementById('lat');
    const lngEl = document.getElementById('lng'); // id=lng لكن name=lang (حسب قاعدة البيانات)

    if (!mapEl) return;

    const defaultLoc = {
      lat: parseFloat(latEl?.value) || 30.0444,  // Cairo default
      lng: parseFloat(lngEl?.value) || 31.2357
    };

    const map = new google.maps.Map(mapEl, { center: defaultLoc, zoom: 10 });
    const marker = new google.maps.Marker({ position: defaultLoc, map, draggable: true });

    const setInputs = (latLng) => {
      if (latEl) latEl.value = latLng.lat();
      if (lngEl) lngEl.value = latLng.lng();
    };

    marker.addListener('dragend', e => setInputs(e.latLng));
    map.addListener('click', e => { marker.setPosition(e.latLng); setInputs(e.latLng); });
  }
</script>

<script async defer
  src="https://maps.googleapis.com/maps/api/js?key=<?php echo e($GMAPS_KEY); ?>&callback=initMap">
</script>

<?php echo $__env->make('layouts.layoutMaster', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/u552947370/domains/affiliatesummitglobal.com/public_html/resources/views/content/setting/setting.blade.php ENDPATH**/ ?>