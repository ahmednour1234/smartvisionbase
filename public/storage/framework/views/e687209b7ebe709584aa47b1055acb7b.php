

<?php $__env->startSection('content'); ?>
<?php $locale = app()->getLocale(); ?>

<style>
    /* Breadcrumbs */
    @media (max-width: 991.98px) {
        .breadcrumbs-custom { height: 350px !important; background-size: cover; background-position: center; }
        .breadcrumbs-custom-title { font-size: 28px; padding-top: 150px; }
    }
    .breadcrumbs-custom { background-size: cover; background-position: center; }
    body { background: #f2f2f2; }
    .form-card {
        max-width: 1000px; margin: 50px auto; background: #fff; border-radius: 20px; padding: 40px;
        box-shadow: 0 8px 30px rgba(0,0,0,0.08); animation: fadeInUp 0.6s ease;
    }
    .form-title {
        font-size: 30px; font-weight: 800; text-align: center; margin-bottom: 25px;
        background: linear-gradient(90deg, #000, #cc252e); -webkit-background-clip: text; -webkit-text-fill-color: transparent;
    }
    .form-wrap { position: relative; margin-bottom: 20px; }
    .form-input, .form-select {
        width: 100%; padding: 15px 18px; padding-left: 45px; border: 2px solid #eee; border-radius: 12px;
        font-size: 16px; font-weight: 500; transition: 0.3s; background: #fff;
    }
    .select2, .select2-container .select2-selection--single  { width: 200px !important; border-radius: 14px; }
    .form-input::placeholder { color: #999; }
    .form-input:focus, .form-select:focus { border-color: #cc252e; box-shadow: 0 0 0 3px rgba(231,55,1,.15); outline: none; }
    .form-icon { position: absolute; top: 50%; left: 15px; transform: translateY(-50%); font-size: 18px; color: #cc252e; }
    .btn-square {
        width: 100%; padding: 14px; background: linear-gradient(90deg, #cc252e, #000); border: none; border-radius: 50px;
        color: #fff; font-size: 17px; font-weight: 700; cursor: pointer; transition: transform .2s ease, opacity .3s ease;
    }
    .btn-square:hover { transform: scale(1.03); opacity: .95; }
    .alert-danger { font-size: 14px; border-radius: 8px; padding: 12px; background-color: #ff3b3b; color: #fff; }
    .phone-flex { display: flex; gap: 10px; align-items: center; }

    @media (max-width: 767px) {
        .form-card { max-width: 95%; padding: 30px 20px; }
        .form-select { width: 120px; font-size: 16px; padding: 14px 10px; }
        .form-input { min-height: 50px; max-height: 55px; font-size: 16px; padding: 14px 45px; }
        .select2, .select2-container .select2-selection--single { width: 120px !important; height: 55px; padding: 0 !important; }
        .select2-selection__rendered { padding: 17px !important; font-size: 15px !important; }
        .select2-selection__arrow { right: 0 !important; }
    }

    @keyframes fadeInUp { from {opacity:0; transform: translateY(30px);} to {opacity:1; transform: translateY(0);} }
</style>

<section class="breadcrumbs-custom bg-image context-dark"
         style="background-image: url(<?php echo e(asset('public/web/assets/images/bg-breadcrumbs-01-1894x424.jpg')); ?>);">
    <div class="container text-center py-4">
        <h3 class="breadcrumbs-custom-title">Become Sponsor</h3>
    </div>
</section>

<section>
    <div class="container">
        <div class="form-card">
            <h4 class="form-title">Become Sponsor</h4>

            <?php if($errors->any()): ?>
                <div class="alert alert-danger text-start">
                    <ul class="mb-0">
                        <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?> <li><?php echo e($error); ?></li> <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form action="<?php echo e(route('web.register.store')); ?>" method="post">
                <?php echo csrf_field(); ?>

                <div class="form-wrap">
                    <span class="form-icon"><i class="fas fa-user"></i></span>
                    <input class="form-input" id="name" type="text" name="name" placeholder="Full Name" required>
                </div>

                <div class="form-wrap">
                    <span class="form-icon"><i class="fas fa-envelope"></i></span>
                    <input class="form-input" id="email" type="email" name="email" placeholder="Email" required>
                </div>

                <div class="form-wrap">
                    <span class="form-icon"><i class="fas fa-phone-alt"></i></span>
                    <div class="phone-flex">
                        <select class="form-select" name="country_code" id="country_code" required>
                            
                            <option value="+971">UAE (+971)</option>
                            <option value="+20">Egypt (+20)</option>
                            <option value="+966">Saudi Arabia (+966)</option>
                            <option value="+965">Kuwait (+965)</option>
                            <option value="+964">Iraq (+964)</option>
                            <option value="+963">Syria (+963)</option>
                            <option value="+962">Jordan (+962)</option>
                            <option value="+968">Oman (+968)</option>
                            <option value="+973">Bahrain (+973)</option>
                            <option value="+974">Qatar (+974)</option>
                            <option value="+212">Morocco (+212)</option>
                            <option value="+967">Yemen (+967)</option>
                            <option value="+216">Tunisia (+216)</option>
                            <option value="+218">Libya (+218)</option>
                            
                            <option value="+221">Senegal (+221)</option>
                            <option value="+234">Nigeria (+234)</option>
                            
                            <option value="+1">USA (+1)</option>
                            <option value="+1">Canada (+1)</option>
                            <option value="+52">Mexico (+52)</option>
                            <option value="+55">Brazil (+55)</option>
                            <option value="+54">Argentina (+54)</option>
                            <option value="+44">UK (+44)</option>
                            <option value="+49">Germany (+49)</option>
                            <option value="+34">Spain (+34)</option>
                            <option value="+39">Italy (+39)</option>
                            <option value="+33">France (+33)</option>
                            <option value="+30">Greece (+30)</option>
                            <option value="+90">Turkey (+90)</option>
                            
                            <option value="+91">India (+91)</option>
                            <option value="+86">China (+86)</option>
                            <option value="+92">Pakistan (+92)</option>
                            <option value="+81">Japan (+81)</option>
                            <option value="+82">South Korea (+82)</option>
                        </select>
                        <input class="form-input" id="phone" type="tel" name="phone" placeholder="Phone" required>
                    </div>
                    <small id="phone-error" class="text-danger d-none">Invalid phone number for selected country.</small>
                </div>

                <div class="form-wrap">
                    <span class="form-icon"><i class="fas fa-briefcase"></i></span>
                    <input class="form-input" id="job" type="text" name="job" placeholder="Job" required>
                </div>

                <div class="form-wrap">
                    <span class="form-icon"><i class="fas fa-building"></i></span>
                    <input class="form-input" id="company_name" type="text" name="company_name" placeholder="Company Name" required>
                </div>

                <input type="hidden" name="type" value="2">

                <button class="btn-square" type="submit">Submit</button>
            </form>
        </div>
    </div>
</section>

<script>
/* =========================
   Phone validation (simplified)
   ========================= */
const phoneRules = {
  '+20':  { lengths:[10], prefixes:[/^10/,/^11/,/^12/,/^15/] }, // EG
  '+966': { lengths:[9,10], prefixes:[/^5/] },                   // SA
  '+971': { lengths:[9], prefixes:[/^50|^52|^54|^55|^56/] },     // AE
  '+965': { lengths:[8], prefixes:[/^5|^6|^9/] },                // KW
  '+964': { lengths:[10], prefixes:[/^7/] },                     // IQ
  '+963': { lengths:[9], prefixes:[/^9/] },                      // SY
  '+962': { lengths:[9], prefixes:[/^7/] },                      // JO
  '+968': { lengths:[8], prefixes:[/^9/] },                      // OM
  '+973': { lengths:[8], prefixes:[/^3/] },                      // BH
  '+974': { lengths:[8], prefixes:[/^3|^5|^6|^7/] },             // QA
  '+212': { lengths:[9], prefixes:[/^6|^7/] },                   // MA
  '+967': { lengths:[9], prefixes:[/^7/] },                      // YE
  '+216': { lengths:[8] },                                       // TN
  '+218': { lengths:[9] },                                       // LY
  '+221': { lengths:[9] },                                       // SN
  '+234': { lengths:[10] },                                      // NG

  '+44':  { lengths:[10], prefixes:[/^7/] },                     // UK
  '+49':  { lengths:[10,11,12,13] },                             // DE
  '+34':  { lengths:[9] },                                       // ES
  '+39':  { lengths:[9,10,11] },                                 // IT
  '+33':  { lengths:[9], prefixes:[/^6|^7/] },                   // FR
  '+30':  { lengths:[10] },                                      // GR
  '+90':  { lengths:[10] },                                      // TR

  '+1':   { lengths:[10] },                                      // US/CA
  '+52':  { lengths:[10] },                                      // MX
  '+55':  { lengths:[10,11] },                                   // BR
  '+54':  { lengths:[10] },                                      // AR

  '+91':  { lengths:[10], prefixes:[/^[6-9]/] },                 // IN
  '+86':  { lengths:[11] },                                      // CN
  '+92':  { lengths:[10], prefixes:[/^3/] },                     // PK
  '+81':  { lengths:[9,10] },                                    // JP
  '+82':  { lengths:[9,10] },                                    // KR
};

function normalizeNationalNumber(raw, code){
  const digits = (raw || '').replace(/\D/g,'');
  const codeDigits = code.replace('+','');
  let n = digits.startsWith(codeDigits) ? digits.slice(codeDigits.length) : digits;
  return n.replace(/^0+/, '');
}
function validatePhone(code, national){
  const rule = phoneRules[code];
  if(!rule) return true;
  const okLen = rule.lengths ? rule.lengths.includes(national.length) : true;
  const okPrefix = rule.prefixes ? rule.prefixes.some(rx => rx.test(national)) : true;
  return okLen && okPrefix;
}

/* =========================
   Continuous auto-detect for country code
   Runs UNTIL user types any digit in #phone
   Priority: Geolocation (watch) → Timezone → Language
   ========================= */
const isoToDial = {
  US:'+1', CA:'+1', MX:'+52', BR:'+55', AR:'+54',
  GB:'+44', DE:'+49', ES:'+34', IT:'+39', FR:'+33', GR:'+30', TR:'+90',
  AE:'+971', EG:'+20', SA:'+966', KW:'+965', IQ:'+964', SY:'+963', JO:'+962', OM:'+968', BH:'+973', QA:'+974', MA:'+212', YE:'+967', TN:'+216', LY:'+218', SN:'+221', NG:'+234',
  IN:'+91', CN:'+86', PK:'+92', JP:'+81', KR:'+82'
};

// rough country bounding boxes
const countryBoxes = {
  EG:[22.0,31.7,24.7,36.9], SA:[16.0,32.2,34.0,56.5], AE:[22.6,26.5,51.3,56.7],
  KW:[28.5,30.2,46.4,48.6], IQ:[29.0,37.6,38.8,48.8], SY:[32.0,37.5,35.0,42.4],
  JO:[29.0,33.6,34.9,39.5], OM:[16.5,26.8,52.0,60.0], BH:[25.4,26.5,50.2,50.9],
  QA:[24.3,26.4,50.6,52.3], MA:[27.5,35.9,-13.5,-1.0], YE:[12.0,19.5,42.5,54.0],
  TN:[30.0,37.6,7.5,11.8], LY:[19.5,33.4,9.4,25.2], SN:[12.0,16.9,-17.7,-11.0],
  NG:[4.0,14.2,2.7,14.7], US:[24.5,49.6,-125.0,-66.5], CA:[42.0,83.0,-141.0,-52.0],
  MX:[14.5,32.8,-118.5,-86.0], BR:[-33.9,5.5,-74.0,-34.0], AR:[-55.2,-21.5,-73.8,-53.5],
  GB:[49.9,59.0,-8.6,1.8], DE:[47.2,55.2,5.8,15.1], ES:[36.0,43.9,-9.5,3.5],
  IT:[36.6,47.2,6.5,18.8], FR:[41.0,51.3,-5.5,9.8], GR:[34.7,41.9,19.2,28.6],
  TR:[36.0,42.3,26.0,45.0], IN:[6.5,35.7,68.0,97.5], CN:[18.0,53.7,73.5,134.9],
  PK:[23.5,37.3,60.8,77.9], JP:[24.0,45.7,123.6,145.9], KR:[33.0,38.8,124.5,132.0]
};

let autoActive = true;
let geoWatchId = null;
let intervalId = null;

const selectEl = document.getElementById('country_code');
const phoneEl  = document.getElementById('phone');

// lock when user starts typing phone
phoneEl.addEventListener('input', () => {
  if (phoneEl.value.trim().length > 0) stopAutoDetect();
});

// optional: also stop if form submitted
document.querySelector('form').addEventListener('submit', stopAutoDetect);

// resume check when tab becomes visible (if still autoActive)
document.addEventListener('visibilitychange', () => {
  if (!document.hidden && autoActive) runDetectionOnce();
});

startAutoDetect();

function startAutoDetect(){
  runDetectionOnce(); // first shot
  // continuous: geolocation watch + periodic fallback
  if ('geolocation' in navigator) {
    geoWatchId = navigator.geolocation.watchPosition(
      pos => { if (autoActive) updateDialFromCoords(pos.coords.latitude, pos.coords.longitude); },
      _err => {},
      { enableHighAccuracy:false, timeout:2000, maximumAge:600000 }
    );
  }
  intervalId = setInterval(() => { if (autoActive) runDetectionOnce(); }, 8000); // every 8s
}

function stopAutoDetect(){
  autoActive = false;
  if (geoWatchId !== null) { navigator.geolocation.clearWatch(geoWatchId); geoWatchId = null; }
  if (intervalId !== null) { clearInterval(intervalId); intervalId = null; }
}

function runDetectionOnce(){
  // try timezone first (fast), then language fallback
  const iso = detectISOByTimezone() || detectISOByLocale();
  if (iso) applyDial(isoToDial[iso]);
}

function updateDialFromCoords(lat, lon){
  // quick win: if inside Egypt, force EG (your case)
  if (inBox(lat, lon, countryBoxes.EG)) { applyDial(isoToDial.EG); return; }
  for (const [iso, box] of Object.entries(countryBoxes)){
    if (inBox(lat, lon, box)) { applyDial(isoToDial[iso]); return; }
  }
  // if nothing matched, leave as-is; interval will try timezone/language
}

function applyDial(dial){
  if (!autoActive || !dial || !selectEl) return;
  if (phoneEl.value.trim().length > 0) { stopAutoDetect(); return; } // user started typing
  if (selectEl.value === dial) return; // no change needed
  const exists = [...selectEl.options].some(o => o.value === dial);
  if (exists) selectEl.value = dial;
}

function inBox(lat, lon, box){
  if (!box) return false;
  const [latMin, latMax, lonMin, lonMax] = box;
  return lat >= latMin && lat <= latMax && lon >= lonMin && lon <= lonMax;
}

function detectISOByLocale(){
  const list = navigator.languages && navigator.languages.length ? navigator.languages : [navigator.language, navigator.userLanguage, navigator.browserLanguage].filter(Boolean);
  for (const lang of list){
    const parts = String(lang || '').replace('_','-').split('-');
    if (parts.length >= 2 && parts[1].length === 2) return parts[1].toUpperCase();
  }
  return null;
}

function detectISOByTimezone(){
  const tz = (Intl.DateTimeFormat().resolvedOptions().timeZone || '').toString();
  const tzMap = {
    'Africa/Cairo':'EG','Asia/Riyadh':'SA','Asia/Dubai':'AE','Asia/Kuwait':'KW','Asia/Baghdad':'IQ','Asia/Damascus':'SY','Asia/Amman':'JO','Asia/Muscat':'OM','Asia/Bahrain':'BH','Asia/Qatar':'QA',
    'Africa/Casablanca':'MA','Asia/Aden':'YE','Africa/Tunis':'TN','Africa/Tripoli':'LY','Africa/Dakar':'SN','Africa/Lagos':'NG',
    'Europe/London':'GB','Europe/Berlin':'DE','Europe/Madrid':'ES','Europe/Rome':'IT','Europe/Paris':'FR','Europe/Athens':'GR','Europe/Istanbul':'TR',
    'America/New_York':'US','America/Chicago':'US','America/Denver':'US','America/Los_Angeles':'US',
    'America/Toronto':'CA','America/Vancouver':'CA','America/Mexico_City':'MX','America/Sao_Paulo':'BR','America/Argentina/Buenos_Aires':'AR',
    'Asia/Kolkata':'IN','Asia/Shanghai':'CN','Asia/Karachi':'PK','Asia/Tokyo':'JP','Asia/Seoul':'KR'
  };
  return tzMap[tz] || null;
}

/* =========================
   Final validation on submit
   ========================= */
document.querySelector('form').addEventListener('submit', function (e) {
  const code = document.getElementById("country_code").value;
  const phoneInput = document.getElementById("phone");
  const errorText = document.getElementById("phone-error");

  const national = normalizeNationalNumber(phoneInput.value, code);
  const isValid = validatePhone(code, national);

  if (!isValid) {
    e.preventDefault();
    errorText.classList.remove('d-none');
    phoneInput.classList.add('is-invalid');
  } else {
    errorText.classList.add('d-none');
    phoneInput.classList.remove('is-invalid');
  }
});
</script>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('web.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/u552947370/domains/affiliatesummitglobal.com/public_html/resources/views/web/content/becomesponsor.blade.php ENDPATH**/ ?>