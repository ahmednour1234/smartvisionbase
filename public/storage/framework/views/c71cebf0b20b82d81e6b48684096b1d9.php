<?php
  /** @var \App\Models\Speaker|null $speaker */
  $isEdit = isset($speaker);
  $selectedCountry = old('country_code', $isEdit ? ($speaker->country_code ?? '') : '');
  $vipValue = (int) old('vip', $isEdit ? ($speaker->vip ?? 0) : 0); // 0 or 1
?>

<?php if (! $__env->hasRenderedOnce('aaffb618-2a8e-4ca4-bfe7-05313a9093c5')): $__env->markAsRenderedOnce('aaffb618-2a8e-4ca4-bfe7-05313a9093c5'); ?>
  <?php $__env->startPush('styles'); ?>
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
  <?php $__env->stopPush(); ?>
<?php endif; ?>

<style>
  .card-neo { border: 1px solid #eef0f4; border-radius: 16px; overflow: hidden; }
  .card-neo .card-header { background: linear-gradient(135deg, #f7f9fc, #ffffff); border-bottom: 1px solid #eef0f4; }
  .section-title { font-weight: 700; font-size: 1.05rem; color: #2c4470; margin-bottom: .25rem; }
  .hint { color: #6c757d; font-size: .86rem; }
  .badge-soft { background: #fff3cd; color: #b58100; border: 1px solid #ffe08a; }
  .vip-btn.btn-on { background:#ffd166; border-color:#ffd166; color:#2c2c2c; }
  .vip-btn.btn-off { background:#f1f5f9; border-color:#e2e8f0; color:#475569; }
  .avatar-preview { width: 84px; height: 84px; border-radius: 12px; object-fit: cover; border: 1px solid #e6eaef; background:#fafafa; }
  .select2-container--default .select2-selection--single { border-radius: .5rem; border-color: #dee2e6; height: 42px; }
  .select2-container--default .select2-selection__rendered { line-height: 42px; }
  .select2-container--default .select2-selection__arrow { height: 42px; }
</style>

<div class="card card-neo shadow-sm mb-4">
  <div class="card-header d-flex align-items-center justify-content-between">
    <div>
      <div class="section-title"><?php echo e($isEdit ? __('speaker.edit_speaker') : __('speaker.create_speaker')); ?></div>
      <div class="hint"><?php echo e(__('speaker.basic_info_hint')); ?></div>
    </div>

    
    <div class="d-flex align-items-center gap-2">
      <span class="badge <?php echo e($vipValue ? 'badge-soft' : 'text-muted border rounded px-2 py-1'); ?>" id="vip-badge">
        VIP: <strong id="vip-badge-state"><?php echo e($vipValue ? 'ON' : 'OFF'); ?></strong>
      </span>
      <button type="button"
              class="btn vip-btn <?php echo e($vipValue ? 'btn-on' : 'btn-off'); ?> btn-sm"
              id="vip-toggle"
              aria-pressed="<?php echo e($vipValue ? 'true' : 'false'); ?>">
        <span class="me-1" id="vip-toggle-icon"><?php echo e($vipValue ? '★' : '☆'); ?></span>
        <span id="vip-toggle-text"><?php echo e($vipValue ? __('vip_on') : __('vip_off')); ?></span>
      </button>
      <input type="hidden" name="vip" id="is_vip" value="<?php echo e($vipValue); ?>">
    </div>
  </div>

  <div class="card-body">
    <div class="row g-3">

      
      <div class="col-md-6">
        <label class="form-label"><?php echo e(__('speaker.name_ar')); ?></label>
        <input type="text" name="name_ar" class="form-control" value="<?php echo e(old('name_ar', $isEdit ? $speaker->name_ar : '')); ?>" required>
      </div>
      <div class="col-md-6">
        <label class="form-label"><?php echo e(__('speaker.name_en')); ?></label>
        <input type="text" name="name_en" class="form-control" value="<?php echo e(old('name_en', $isEdit ? $speaker->name_en : '')); ?>" required>
      </div>

      
  
      
      <div class="col-md-6">
        <label class="form-label"><?php echo e(__('speaker.title_ar')); ?></label>
        <input type="text" name="title_ar" class="form-control" value="<?php echo e(old('title_ar', $isEdit ? $speaker->title_ar : '')); ?>">
      </div>
      <div class="col-md-6">
        <label class="form-label"><?php echo e(__('speaker.title_en')); ?></label>
        <input type="text" name="title_en" class="form-control" value="<?php echo e(old('title_en', $isEdit ? $speaker->title_en : '')); ?>">
      </div>

      
      <div class="col-md-6">
        <label class="form-label"><?php echo e(__('speaker.company_name_ar')); ?></label>
        <input type="text" name="company_name_ar" class="form-control" value="<?php echo e(old('company_name_ar', $isEdit ? $speaker->company_name_ar : '')); ?>">
      </div>
      <div class="col-md-6">
        <label class="form-label"><?php echo e(__('speaker.company_name_en')); ?></label>
        <input type="text" name="company_name_en" class="form-control" value="<?php echo e(old('company_name_en', $isEdit ? $speaker->company_name_en : '')); ?>">
      </div>

      
      <div class="col-md-6">
        <label class="form-label"><?php echo e(__('speaker.linkedin')); ?></label>
        <input type="url" name="linkedin" class="form-control" value="<?php echo e(old('linkedin', $isEdit ? $speaker->linkedin : '')); ?>">
      </div>
      <div class="col-md-6">
        <label class="form-label"><?php echo e(__('speaker.facebook')); ?></label>
        <input type="url" name="facebook" class="form-control" value="<?php echo e(old('facebook', $isEdit ? $speaker->facebook : '')); ?>">
      </div>

      <div class="col-md-6">
        <label class="form-label"><?php echo e(__('speaker.twitter')); ?></label>
        <input type="url" name="twitter" class="form-control" value="<?php echo e(old('twitter', $isEdit ? ($speaker->twitter ?? '') : '')); ?>">
      </div>
      <div class="col-md-6">
        <label class="form-label"><?php echo e(__('speaker.tiktok')); ?></label>
        <input type="url" name="tiktok" class="form-control" value="<?php echo e(old('tiktok', $isEdit ? ($speaker->tiktok ?? '') : '')); ?>">
      </div>

      <div class="col-md-6">
        <label class="form-label"><?php echo e(__('speaker.youtube')); ?></label>
        <input type="url" name="youtube" class="form-control" value="<?php echo e(old('youtube', $isEdit ? $speaker->youtube : '')); ?>">
      </div>
      <div class="col-md-6">
        <label class="form-label"><?php echo e(__('speaker.instagram')); ?></label>
        <input type="url" name="instagram" class="form-control" value="<?php echo e(old('instagram', $isEdit ? ($speaker->instagram ?? $speaker->instgram ?? '') : '')); ?>">
      </div>

      <div class="col-12">
        <label class="form-label"><?php echo e(__('speaker.social_links')); ?></label>
        <textarea name="social_links" class="form-control" rows="3" placeholder='{"website":"https://..."}'><?php echo e(old('social_links', $isEdit ? $speaker->social_links : '')); ?></textarea>
        <small class="hint"><?php echo e(__('speaker.social_links_hint')); ?></small>
      </div>

      
      <?php if(isset($type) && (int)$type === 2): ?>
        <div class="col-md-6">
          <label class="form-label"><?php echo e(__('speaker.number_of_followers')); ?></label>
          <input type="text" name="number_of_followers" class="form-control" value="<?php echo e(old('number_of_followers', $isEdit ? $speaker->number_of_followers : '')); ?>">
        </div>
      <?php endif; ?>
      <?php if(isset($type) && (int)$type === 2): ?>
        <div class="col-md-6">
          <label class="form-label"><?php echo e(__('speaker.followers_ticktock')); ?></label>
          <input type="text" name="followers_ticktock" class="form-control" value="<?php echo e(old('followers_ticktock', $isEdit ? $speaker->followers_ticktock : '')); ?>">
        </div>
      <?php endif; ?>
            <?php if(isset($type) && (int)$type === 2): ?>

          <div class="col-md-6">
        <label class="form-label"><?php echo e(__('speaker.section_type')); ?></label>
        <select name="section" class="form-select">
          <option value="special" ><?php echo e(__('speaker.special')); ?></option>
          <option value="aps"><?php echo e(__('speaker.aps')); ?></option>
        </select>
      </div>
            <?php endif; ?>

      <div class="col-md-6">
        <label class="form-label"><?php echo e(__('speaker.orders')); ?></label>
        <input type="number" name="orders" class="form-control" value="<?php echo e(old('orders', $isEdit ? $speaker->orders : '')); ?>">
      </div>

      
      <input type="hidden" name="type" value="<?php echo e($type ?? ($isEdit ? $speaker->type : '')); ?>">

      
      <div class="col-md-6">
        <label class="form-label"><?php echo e(__('speaker.image')); ?></label>
        <input type="file" name="image" class="form-control" id="image-input" accept="image/*">
        <div class="d-flex align-items-center gap-3 mt-2">
          <img id="image-preview" src="<?php echo e($isEdit && $speaker->image ? asset($speaker->image) : ''); ?>" class="avatar-preview <?php echo e($isEdit && $speaker->image ? '' : 'd-none'); ?>" alt="preview">
          <?php if($isEdit && $speaker->image): ?>
            <a href="<?php echo e(asset($speaker->image)); ?>" target="_blank" class="btn btn-sm btn-outline-secondary"><?php echo e(__('speaker.view_current_image')); ?></a>
          <?php endif; ?>
        </div>
      </div>

      
      <div class="col-md-6">
        <label class="form-label"><?php echo e(__('speaker.country')); ?></label>
        <select id="country" name="country_code" class="form-select">
          <option value=""><?php echo e(__('speaker.choose_country')); ?></option>
          <?php $__currentLoopData = $countries; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option
              value="<?php echo e($c['code']); ?>"
              data-flag="<?php echo e($c['flag_svg']); ?>"
              <?php echo e(strcasecmp($selectedCountry, $c['code']) === 0 ? 'selected' : ''); ?>

            >
              <?php echo e($c['name_ar'] ?? $c['name_en']); ?>

            </option>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
      </div>

    </div>
  </div>
</div>

<?php if (! $__env->hasRenderedOnce('28942039-715b-4818-9138-aae4adfd981b')): $__env->markAsRenderedOnce('28942039-715b-4818-9138-aae4adfd981b'); ?>
    
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
    <script>
      // إذا كان القالب عندك يحمّل jQuery مسبقًا، لا مشكلة – CDN هنا سيتجاوزه بالcache. المهم أن تكون نسخة كاملة.
      window.jQuery || console.error('jQuery not loaded');
    </script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.full.min.js"></script>
    <script>
      (function(){
        function initCountrySelect(){
          if (!window.jQuery) { console.error('jQuery missing'); return; }
          if (!$.fn || typeof $.fn.select2 !== 'function') {
            console.error('Select2 not loaded or jQuery conflict'); return;
          }
          function tpl(state){
            if(!state.id) return state.text;
            const flag = $(state.element).data('flag');
            return $(`
              <span style="display:flex;align-items:center;gap:8px">
                ${flag ? `<img src="${flag}" width="18" height="12" style="object-fit:cover;border:1px solid #ddd;border-radius:2px">` : ''}
                <span>${state.text}</span>
              </span>
            `);
          }
          $('#country').select2({
            width: '100%',
            placeholder: '<?php echo e(__('speaker.choose_country')); ?>',
            templateResult: tpl,
            templateSelection: tpl
          });
        }

        function initVip(){
          const $vipBtn = $('#vip-toggle');
          const $vipVal = $('#is_vip');
          const $vipText = $('#vip-toggle-text');
          const $vipIcon = $('#vip-toggle-icon');
          const $vipBadgeState = $('#vip-badge-state');

          function setVipUI(val){
            const isOn = Number(val) === 1;
            $vipBtn.toggleClass('btn-on', isOn).toggleClass('btn-off', !isOn)
                  .attr('aria-pressed', isOn ? 'true' : 'false');
            $vipText.text(isOn ? '<?php echo e(__("speaker.vip_on")); ?>' : '<?php echo e(__("speaker.vip_off")); ?>');
            $vipIcon.text(isOn ? '★' : '☆');
            $vipBadgeState.text(isOn ? 'ON' : 'OFF');
          }

          $vipBtn.on('click', function(){
            const next = Number($vipVal.val()) === 1 ? 0 : 1;
            $vipVal.val(next);
            setVipUI(next);
          });

          setVipUI($vipVal.val());
        }

        function initPassword(){
          $('#togglePass').on('click', function(){
            const $pass = $('#password');
            const type = $pass.attr('type') === 'password' ? 'text' : 'password';
            $pass.attr('type', type);
          });
        }

        function initImagePreview(){
          $('#image-input').on('change', function(e){
            const file = e.target.files?.[0];
            if(!file) return;
            const url = URL.createObjectURL(file);
            $('#image-preview').attr('src', url).removeClass('d-none');
          });
        }

        // DOM جاهز
        $(function(){
          initCountrySelect();
          initVip();
          initPassword();
          initImagePreview();
        });

        // لو عندك PJAX/Turbo/Livewire… استدعِ التهيئة بعد كل تحديث DOM
        document.addEventListener('livewire:load', function(){ initCountrySelect(); });
      })();
    </script>
<?php endif; ?>
<?php /**PATH /home/u552947370/domains/affiliatesummitglobal.com/public_html/resources/views/content/speakers/_form.blade.php ENDPATH**/ ?>