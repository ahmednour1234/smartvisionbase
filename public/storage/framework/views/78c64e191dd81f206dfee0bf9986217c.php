
<?php
    /** @var \App\Models\MarketingRef|null $ref */
    $ref  = $ref ?? null;
    $oldAllowed = old('allowed_paths', $ref->allowed_paths ?? []);
    if (!is_array($oldAllowed)) { $oldAllowed = []; }

    // تنسيق datetime-local
    $expiresValue = old('expires_at',
        $ref && $ref->expires_at ? $ref->expires_at->format('Y-m-d\TH:i') : null
    );

    // خيارات معاينة المسار في رابط المشاركة
    $previewPaths = collect($oldAllowed)->filter()->values()->all();
    if (empty($previewPaths)) { $previewPaths = ['/register']; }
?>

<style>
/* ===== Theme & Layout ===== */
.mr { --brand:#ea580c; --brand-700:#c2410c; --brand-50:#fff7ed; --line:#edf2f7; --text:#1f2937; --muted:#6b7280; --ring:rgba(234,88,12,.15);}
.mr * { box-sizing:border-box; }
.mr-card{ background:#fff; border:1px solid var(--line); border-radius:16px; overflow:hidden; box-shadow:0 8px 28px rgba(16,24,40,.06);}
.mr-head{ padding:18px 20px; background:linear-gradient(180deg,var(--brand-50),#fff); border-bottom:1px solid var(--line); display:flex; align-items:center; justify-content:space-between; gap:12px;}
.mr-t h3{ margin:0; color:var(--text); font-weight:800; font-size:18px;}
.mr-t p{ margin:4px 0 0; color:var(--muted); font-size:13px;}
.mr-body{ padding:20px; display:grid; gap:24px;}
.mr-sec{ display:grid; gap:14px;}
.mr-sec-title{ font-weight:800; color:var(--text); font-size:14px;}
.mr-sec-note{ color:var(--muted); font-size:12px;}

.mr-grid{ display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:16px; }
@media (max-width: 768px){ .mr-grid{ grid-template-columns:1fr; } }

.mr-field{ display:grid; gap:6px; }
.mr-label{ font-size:13px; font-weight:600; color:var(--text); }
.mr-req{ color:#dc2626; }
.mr-input, .mr-text, .mr-select{
  width:100%; border:1px solid #d1d5db; border-radius:12px; padding:10px 12px; font-size:14px; color:#111827; background:#fff;
  transition:box-shadow .15s ease, border-color .15s ease, background .15s ease;
}
.mr-input:focus, .mr-text:focus, .mr-select:focus{ outline:none; border-color:var(--brand); box-shadow:0 0 0 3px var(--ring);}
.mr-text{ min-height:110px; resize:vertical; }
.mr-mono{ font-family:ui-monospace,SFMono-Regular,Menlo,Monaco,Consolas,"Liberation Mono",monospace; }
.is-invalid{ border-color:#ef4444!important; box-shadow:0 0 0 3px rgba(239,68,68,.1)!important; }
.mr-error{ color:#b91c1c; font-size:12px; }

.mr-inline{ display:flex; align-items:center; justify-content:space-between; gap:10px; }

.mr-actions{ display:flex; align-items:center; gap:8px; flex-wrap:wrap; }
.mr-btn{ border:1px solid transparent; background:#fff; color:var(--text); padding:9px 14px; border-radius:12px; font-weight:700; font-size:13px; cursor:pointer; transition:transform .05s ease, background .15s ease, color .15s ease, border-color .15s ease; }
.mr-btn:active{ transform:translateY(1px); }
.mr-btn--brand{ background:var(--brand); color:#fff; border-color:var(--brand); }
.mr-btn--brand:hover{ background:var(--brand-700); border-color:var(--brand-700); }
.mr-btn--ol{ color:var(--brand); border-color:var(--brand); }
.mr-btn--ol:hover{ background:var(--brand-50); }
.mr-btn--muted{ color:#111827; border-color:#d1d5db; }
.mr-btn--muted:hover{ background:#fafafa; }

.mr-switch{ display:flex; align-items:center; gap:8px; user-select:none;}
.mr-switch input[type="checkbox"]{ appearance:none; width:44px; height:26px; border-radius:999px; background:#d1d5db; position:relative; outline:none; cursor:pointer; transition:background .15s ease; }
.mr-switch input[type="checkbox"]::after{ content:''; position:absolute; top:3px; right:3px; width:20px; height:20px; background:#fff; border-radius:999px; box-shadow:0 1px 2px rgba(0,0,0,.15); transition: transform .15s ease;}
.mr-switch input[type="checkbox"]:checked{ background:var(--brand);}
.mr-switch input[type="checkbox"]:checked::after{ transform:translateX(-18px);}
.mr-switch label{ font-size:13px; color:var(--text); }

.mr-paths{ display:grid; gap:10px; }
.mr-path-row{ display:grid; grid-template-columns:1fr auto; gap:10px; }
.mr-chip{ display:inline-block; padding:6px 10px; background:#f8fafc; border:1px solid #d1d5db; border-radius:10px; color:#0f172a; }

.mr-share{ display:grid; gap:10px; background:var(--brand-50); border:1px solid #fde7d5; border-radius:12px; padding:12px; }
.mr-share-row{ display:flex; align-items:center; gap:10px; }
.mr-url{ flex:1; font-size:13px; color:#111827; word-break:break-all; }

.mr-foot{ border-top:1px solid var(--line); background:#fafafa; padding:12px 20px; display:flex; align-items:center; gap:10px; }
.mr-note{ font-size:12px; color:var(--muted); }
</style>

<div class="mr" dir="rtl">
  <div class="mr-card">
    
    <div class="mr-head">
      <div class="mr-t">
        <h3>بيانات المرجع التسويقي</h3>
        <p>نموذج منسّق لترتيب الحقول وتتبع التسجيلات بدقّة.</p>
      </div>
      <div class="mr-switch">
        <input type="hidden" name="active" value="0">
        <input id="mrActive" type="checkbox" name="active" value="1" <?php echo e(old('active', $ref->active ?? true) ? 'checked' : ''); ?>>
        <label for="mrActive" id="activeLabel"><?php echo e(old('active', $ref->active ?? true) ? 'نشط' : 'متوقف'); ?></label>
      </div>
    </div>

    
    <div class="mr-body">

      
      <div class="mr-sec">
        <div class="mr-sec-title">المعلومات الأساسية</div>
        <div class="mr-grid">
          <div class="mr-field">
            <label class="mr-label">اسم المرجع/الحملة <span class="mr-req">*</span></label>
            <input type="text" name="name" value="<?php echo e(old('name', $ref->name ?? '')); ?>"
                   class="mr-input <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
            <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="mr-error"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
          </div>

          <div class="mr-field">
            <div class="mr-inline">
              <label class="mr-label">الكود (سيظهر في الرابط)</label>
              <button type="button" id="gen-code" class="mr-btn mr-btn--ol" title="توليد تلقائي">توليد تلقائي</button>
            </div>
            <input type="text" name="code" value="<?php echo e(old('code', $ref->code ?? '')); ?>"
                   class="mr-input mr-mono <?php $__errorArgs = ['code'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" placeholder="اتركه فارغًا لتوليد تلقائي">
            <?php $__errorArgs = ['code'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="mr-error"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            <div class="mr-note">مثال للرابط: <span class="mr-chip mr-mono">/r/ABC123</span></div>
          </div>
        </div>
      </div>

      
      <div class="mr-sec">
        <div class="mr-sec-title">القيود</div>
        <div class="mr-grid">
          <div class="mr-field">
            <div class="mr-inline">
              <label class="mr-label">الحد الأقصى للاستخدام</label>
              <label class="mr-label" style="font-weight:500;">
                <input type="checkbox" id="unlimited-uses" <?php echo e(old('max_uses', $ref->max_uses ?? null) ? '' : 'checked'); ?>>
                <span style="margin-right:6px;">بدون حد أقصى</span>
              </label>
            </div>
            <input type="number" min="1" name="max_uses" id="max_uses_input"
                   value="<?php echo e(old('max_uses', $ref->max_uses ?? '')); ?>"
                   class="mr-input <?php $__errorArgs = ['max_uses'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                   placeholder="مثال: 100" <?php echo e(old('max_uses', $ref->max_uses ?? null) ? '' : 'disabled'); ?>>
            <?php $__errorArgs = ['max_uses'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="mr-error"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
          </div>

          <div class="mr-field">
            <label class="mr-label">تاريخ الانتهاء (اختياري)</label>
            <input type="datetime-local" name="expires_at" value="<?php echo e($expiresValue); ?>"
                   class="mr-input <?php $__errorArgs = ['expires_at'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
            <?php $__errorArgs = ['expires_at'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="mr-error"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
          </div>
        </div>
      </div>

      
      <div class="mr-sec">
        <div class="mr-sec-title">المسارات المسموح بها</div>
        <div class="mr-sec-note">اترك القائمة فارغة للسماح بجميع المسارات. أمثلة: <span class="mr-chip mr-mono">/register</span>،
          <span class="mr-chip mr-mono">/lead</span>.</div>

        <div class="mr-actions" style="margin-top:4px;">
          <button type="button" id="add-path-row" class="mr-btn mr-btn--ol">+ إضافة مسار</button>
          <button type="button" id="add-default-register" class="mr-btn mr-btn--muted">/register</button>
        </div>

        <div id="paths-wrapper" class="mr-paths" style="margin-top:10px;">
          <?php $paths = count($oldAllowed) ? $oldAllowed : ['']; ?>
          <?php $__currentLoopData = $paths; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="mr-path-row path-row">
              <input type="text" name="allowed_paths[]" value="<?php echo e($p); ?>"
                     class="mr-input" placeholder="/register">
              <button type="button" class="mr-btn mr-btn--muted remove-path" title="حذف">حذف</button>
            </div>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
        <?php $__errorArgs = ['allowed_paths'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="mr-error"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
      </div>

      
      <?php if(isset($ref)): ?>
        <div class="mr-sec">
          <div class="mr-sec-title">رابط المشاركة</div>
          <div class="mr-share">
            
            <?php $baseShare = route('ref.hit', $ref); ?>
            <div class="mr-share-row">
              <div class="mr-url mr-mono" id="mrBaseUrl"><?php echo e($baseShare); ?></div>
              <button type="button" class="mr-btn mr-btn--brand copy-share" data-copy="<?php echo e($baseShare); ?>">نسخ</button>
            </div>

            
            <div class="mr-share-row">
              <select id="mrPathSelect" class="mr-select" style="max-width:260px;">
                <?php $__currentLoopData = $previewPaths; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $opt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                  <option value="<?php echo e($opt); ?>"><?php echo e($opt); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              </select>
              <div class="mr-url mr-mono" id="mrPathUrl"><?php echo e(rtrim($baseShare,'/') . '/' . ltrim($previewPaths[0] ?? '/register', '/')); ?></div>
              <button type="button" class="mr-btn mr-btn--ol" id="copyPathUrl">نسخ</button>
            </div>

            <div class="mr-note">يتم وضع الكود في Cookie وتسجيل الزيارة، ثم إعادة التوجيه للهدف.</div>
          </div>
        </div>
      <?php endif; ?>

    </div>

    
    <div class="mr-foot">
      <button type="submit" class="mr-btn mr-btn--brand">حفظ</button>
      <a href="<?php echo e(route('marketing-refs.index')); ?>" class="mr-btn mr-btn--muted">إلغاء</a>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
  // تحديث نص حالة السويتش
  const activeInput = document.getElementById('mrActive');
  const activeLabel = document.getElementById('activeLabel');
  activeInput?.addEventListener('change', () => {
    if (activeLabel) activeLabel.textContent = activeInput.checked ? 'نشط' : 'متوقف';
  });

  // إضافة/حذف مسارات
  const wrapper = document.getElementById('paths-wrapper');
  document.getElementById('add-path-row')?.addEventListener('click', () => {
    const row = document.createElement('div');
    row.className = 'mr-path-row path-row';
    row.innerHTML = `
      <input type="text" name="allowed_paths[]" value="" class="mr-input" placeholder="/register">
      <button type="button" class="mr-btn mr-btn--muted remove-path" title="حذف">حذف</button>
    `;
    wrapper.appendChild(row);
  });
  document.getElementById('add-default-register')?.addEventListener('click', () => {
    const row = document.createElement('div');
    row.className = 'mr-path-row path-row';
    row.innerHTML = `
      <input type="text" name="allowed_paths[]" value="/register" class="mr-input" placeholder="/register">
      <button type="button" class="mr-btn mr-btn--muted remove-path" title="حذف">حذف</button>
    `;
    wrapper.appendChild(row);
  });
  wrapper?.addEventListener('click', (e) => {
    if (e.target.classList.contains('remove-path')) {
      const rows = wrapper.querySelectorAll('.path-row');
      if (rows.length > 1) e.target.closest('.path-row').remove();
      else e.target.closest('.path-row').querySelector('input').value = '';
    }
  });

  // توليد كود تلقائي
  document.getElementById('gen-code')?.addEventListener('click', () => {
    const el = document.querySelector('input[name="code"]');
    if (!el) return;
    const rand = () => Math.random().toString(36).slice(2, 8).toUpperCase();
    el.value = rand();
    el.focus();
    el.select?.();
  });

  // تعطيل/تمكين max_uses
  const unlimited = document.getElementById('unlimited-uses');
  const maxInput  = document.getElementById('max_uses_input');
  if (unlimited && maxInput) {
    const sync = () => {
      if (unlimited.checked) {
        maxInput.value = '';
        maxInput.setAttribute('disabled', 'disabled');
      } else {
        maxInput.removeAttribute('disabled');
        maxInput.focus();
      }
    };
    unlimited.addEventListener('change', sync);
    sync();
  }

  // نسخ الروابط (الأساسي)
  document.querySelector('.copy-share')?.addEventListener('click', (e) => {
    const val = e.currentTarget.getAttribute('data-copy');
    navigator.clipboard.writeText(val).then(() => {
      const btn = e.currentTarget;
      const prev = btn.textContent;
      btn.textContent = '✔ تم النسخ';
      setTimeout(()=> btn.textContent = prev, 1200);
    });
  });

  // رابط لمسار محدد
  const pathSelect = document.getElementById('mrPathSelect');
  const pathUrlEl  = document.getElementById('mrPathUrl');
  const baseUrl    = document.getElementById('mrBaseUrl')?.textContent?.trim() || '';
  const updatePathUrl = () => {
    if (!pathSelect || !pathUrlEl) return;
    const p = (pathSelect.value || '').replace(/^\//,''); // remove leading slash
    pathUrlEl.textContent = baseUrl.replace(/\/$/, '') + '/' + p;
  };
  pathSelect?.addEventListener('change', updatePathUrl);
  updatePathUrl();

  document.getElementById('copyPathUrl')?.addEventListener('click', () => {
    if (!pathUrlEl) return;
    const link = pathUrlEl.textContent.trim();
    navigator.clipboard.writeText(link).then(() => {
      const btn = document.getElementById('copyPathUrl');
      const prev = btn.textContent;
      btn.textContent = '✔ تم النسخ';
      setTimeout(()=> btn.textContent = prev, 1000);
    });
  });
});
</script>
<?php /**PATH /home/u552947370/domains/affiliatesummitglobal.com/public_html/resources/views/content/marketing_refs/_form.blade.php ENDPATH**/ ?>