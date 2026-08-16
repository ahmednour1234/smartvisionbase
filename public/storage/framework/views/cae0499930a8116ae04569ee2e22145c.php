

<?php $__env->startSection('content'); ?>
<?php
  $locale = app()->getLocale();

  // ==== إعدادات السيرفر: كشف الدولة من IP (torann/geoip) + افتراضيات ====
  // composer require torann/geoip
  // php artisan vendor:publish --provider="Torann\GeoIP\GeoIPServiceProvider" --tag=config

  // أسماء الدول (ISO2 => اسم)
  $countryNames = [
    // MENA & Africa
    'AE'=>'United Arab Emirates','EG'=>'Egypt','SA'=>'Saudi Arabia','KW'=>'Kuwait','QA'=>'Qatar','OM'=>'Oman','BH'=>'Bahrain','JO'=>'Jordan','IQ'=>'Iraq','SY'=>'Syria','LB'=>'Lebanon','PS'=>'Palestine','YE'=>'Yemen',
    'MA'=>'Morocco','DZ'=>'Algeria','TN'=>'Tunisia','LY'=>'Libya','MR'=>'Mauritania','SD'=>'Sudan','SN'=>'Senegal','NG'=>'Nigeria','GH'=>'Ghana','KE'=>'Kenya','TZ'=>'Tanzania','UG'=>'Uganda','ZA'=>'South Africa','ET'=>'Ethiopia',

    // Europe
    'GB'=>'United Kingdom','IE'=>'Ireland','FR'=>'France','DE'=>'Germany','IT'=>'Italy','ES'=>'Spain','PT'=>'Portugal','NL'=>'Netherlands','BE'=>'Belgium','CH'=>'Switzerland','AT'=>'Austria',
    'SE'=>'Sweden','NO'=>'Norway','DK'=>'Denmark','FI'=>'Finland','PL'=>'Poland','CZ'=>'Czechia','HU'=>'Hungary','RO'=>'Romania','BG'=>'Bulgaria','SK'=>'Slovakia','SI'=>'Slovenia','HR'=>'Croatia',
    'EE'=>'Estonia','LV'=>'Latvia','LT'=>'Lithuania','LU'=>'Luxembourg','MT'=>'Malta','CY'=>'Cyprus','GR'=>'Greece','IS'=>'Iceland',

    // Americas
    'US'=>'USA','CA'=>'Canada','MX'=>'Mexico','BR'=>'Brazil','AR'=>'Argentina','CL'=>'Chile','CO'=>'Colombia','PE'=>'Peru','VE'=>'Venezuela','UY'=>'Uruguay','PY'=>'Paraguay','BO'=>'Bolivia','EC'=>'Ecuador',
    'CR'=>'Costa Rica','PA'=>'Panama','GT'=>'Guatemala',

    // Asia-Pacific
    'TR'=>'Turkey','IR'=>'Iran','IL'=>'Israel','IN'=>'India','PK'=>'Pakistan','BD'=>'Bangladesh','LK'=>'Sri Lanka','NP'=>'Nepal',
    'CN'=>'China','HK'=>'Hong Kong','TW'=>'Taiwan','JP'=>'Japan','KR'=>'South Korea',
    'SG'=>'Singapore','MY'=>'Malaysia','TH'=>'Thailand','VN'=>'Vietnam','PH'=>'Philippines','ID'=>'Indonesia',
  ];

  // أكواد الاتصال (ISO2 => Dial)
  $dialMap = [
    // MENA & Africa
    'AE'=>'+971','EG'=>'+20','SA'=>'+966','KW'=>'+965','QA'=>'+974','OM'=>'+968','BH'=>'+973','JO'=>'+962','IQ'=>'+964','SY'=>'+963','LB'=>'+961','PS'=>'+970','YE'=>'+967',
    'MA'=>'+212','DZ'=>'+213','TN'=>'+216','LY'=>'+218','MR'=>'+222','SD'=>'+249','SN'=>'+221','NG'=>'+234','GH'=>'+233','KE'=>'+254','TZ'=>'+255','UG'=>'+256','ZA'=>'+27','ET'=>'+251',

    // Europe
    'GB'=>'+44','IE'=>'+353','FR'=>'+33','DE'=>'+49','IT'=>'+39','ES'=>'+34','PT'=>'+351','NL'=>'+31','BE'=>'+32','CH'=>'+41','AT'=>'+43',
    'SE'=>'+46','NO'=>'+47','DK'=>'+45','FI'=>'+358','PL'=>'+48','CZ'=>'+420','HU'=>'+36','RO'=>'+40','BG'=>'+359','SK'=>'+421','SI'=>'+386','HR'=>'+385',
    'EE'=>'+372','LV'=>'+371','LT'=>'+370','LU'=>'+352','MT'=>'+356','CY'=>'+357','GR'=>'+30','IS'=>'+354',

    // Americas
    'US'=>'+1','CA'=>'+1','MX'=>'+52','BR'=>'+55','AR'=>'+54','CL'=>'+56','CO'=>'+57','PE'=>'+51','VE'=>'+58','UY'=>'+598','PY'=>'+595','BO'=>'+591','EC'=>'+593',
    'CR'=>'+506','PA'=>'+507','GT'=>'+502',

    // Asia-Pacific
    'TR'=>'+90','IR'=>'+98','IL'=>'+972','IN'=>'+91','PK'=>'+92','BD'=>'+880','LK'=>'+94','NP'=>'+977',
    'CN'=>'+86','HK'=>'+852','TW'=>'+886','JP'=>'+81','KR'=>'+82',
    'SG'=>'+65','MY'=>'+60','TH'=>'+66','VN'=>'+84','PH'=>'+63','ID'=>'+62',
  ];

  // ترتيب العرض (قائمة ISO2 مرتبة)
  $orderedIso = [
    // Arab & MENA first
    'AE','EG','SA','KW','QA','OM','BH','JO','IQ','SY','LB','PS','YE','MA','DZ','TN','LY','MR','SD',
    // Africa
    'ZA','SN','NG','GH','KE','TZ','UG','ET',
    // Europe
    'GB','IE','FR','DE','IT','ES','PT','NL','BE','CH','AT','SE','NO','DK','FI','PL','CZ','HU','RO','BG','SK','SI','HR','EE','LV','LT','LU','MT','CY','GR','IS',
    // Americas
    'US','CA','MX','BR','AR','CL','CO','PE','VE','UY','PY','BO','EC','CR','PA','GT',
    // Asia-Pacific
    'TR','IR','IL','IN','PK','BD','LK','NP','CN','HK','TW','JP','KR','SG','MY','TH','VN','PH','ID',
  ];

  // مجموعات fallback لو ISO غير موجود في $dialMap
  $EU_ISOS = ['GB','IE','FR','DE','IT','ES','PT','NL','BE','CH','AT','SE','NO','DK','FI','PL','CZ','HU','RO','BG','SK','SI','HR','EE','LV','LT','LU','MT','CY','GR','IS'];
  $EAST_ASIA_ISOS = ['CN','HK','TW','JP','KR','SG','MY','TH','VN','PH','ID','BD','PK','IN','LK','NP'];

  // افتراضيات
  $defaultIso  = 'AE';
  $defaultDial = $dialMap[$defaultIso];

  // كشف الدولة من IP عبر geoip() إن وُجد
  try {
      if (function_exists('geoip')) {
          $geo = @geoip(request()->ip());
          $detectedIso = isset($geo->iso_code) ? strtoupper($geo->iso_code) : null;

          if ($detectedIso) {
              if (isset($dialMap[$detectedIso])) {
                  $defaultIso  = $detectedIso;
                  $defaultDial = $dialMap[$detectedIso];
              } elseif (in_array($detectedIso, $EU_ISOS, true)) {
                  $defaultIso  = 'GB';
                  $defaultDial = $dialMap['GB'];
              } elseif (in_array($detectedIso, $EAST_ASIA_ISOS, true)) {
                  $defaultIso  = 'CN';
                  $defaultDial = $dialMap['CN'];
              }
          }
      }
  } catch (\Throwable $e) {
      // إبقَ على الافتراضيات
  }

  // جلب القيم المختارة من الخطوة السابقة (محفوظة في الـ session)
  $selectedSection  = session('selected_section');
  $selectedCategory = session('selected_category');

?>

<style>
@media (max-width:991.98px){
  .breadcrumbs-custom{height:350px!important;background-size:cover;background-position:center}
  .breadcrumbs-custom-title{font-size:28px;padding-top:150px}
}
.breadcrumbs-custom{background-size:cover;background-position:center}
body{background:#f2f2f2}
.form-card{max-width:1000px;margin:50px auto;background:#fff;border-radius:20px;padding:50px 40px;box-shadow:0 8px 30px rgba(0,0,0,.08);animation:fadeInUp .6s ease}
.form-title{font-size:32px;font-weight:800;text-align:center;margin-bottom:30px;background:linear-gradient(90deg,#000,#E73701);-webkit-background-clip:text;-webkit-text-fill-color:transparent}
.form-wrap{position:relative;margin-bottom:25px}
.form-input,.form-select{font-size:18px;font-weight:500;border:2px solid #eee;border-radius:14px;background:#fff;box-sizing:border-box;transition:.3s}
.form-input{width:100%;padding:16px 20px;padding-left:50px}
.select2,.select2-container .select2-selection--single{width:200px!important;border-radius:14px}
.form-input:focus,.form-select:focus{border-color:#E73701;box-shadow:0 0 0 3px rgba(231,55,1,.15);outline:none}
.form-icon{position:absolute;top:50%;left:18px;transform:translateY(-50%);font-size:20px;color:#cc252e;margin-right:12px}
.btn-square{width:100%;padding:16px;background:linear-gradient(90deg,#cc252e,#000);border:none;border-radius:50px;color:#fff;font-size:18px;font-weight:700;cursor:pointer;transition:transform .2s ease,opacity .3s ease}
.btn-square:hover{transform:scale(1.03);opacity:.95}
.alert-danger{font-size:14px;border-radius:8px;padding:12px;background-color:#ff3b3b;color:#fff}
@media (max-width:767px){
  .form-card{max-width:95%;padding:30px 20px}
  .form-select{width:120px;font-size:16px;padding:14px 10px}
  .form-input{min-height:50px;max-height:55px;font-size:16px;padding:14px 45px}
  .select2,.select2-container .select2-selection--single{width:120px!important;height:55px;padding:0!important}
  .select2-selection__rendered{padding:17px!important;font-size:15px!important}
  .select2-selection__arrow{right:0!important}
}
.phone-flex{display:flex;gap:15px;align-items:center}
.phone-flex input{flex-grow:1;border-radius:14px;border:2px solid #eee;padding-left:20px;font-size:18px;height:65px;box-sizing:border-box}
.phone-flex .form-input{padding-left:20px}
@keyframes fadeInUp{from{opacity:0;transform:translateY(30px)}to{opacity:1;transform:translateY(0)}}
.form-title{text-align:center}
.form-subtitle{text-align:center;margin-top:0;font-size:18px;margin-bottom:5px;font-weight:bold;color:#cc252e}
</style>

<section class="breadcrumbs-custom bg-image context-dark"
         style="background-image:url(<?php echo e(asset('public/web/assets/images/bg-breadcrumbs-01-1894x424.jpg')); ?>);">
  <div class="container text-center py-4">
    <h3 class="breadcrumbs-custom-title">Register Now</h3>
  </div>
</section>

<section>
  <div class="container">
    <div class="form-card">
      <h4 class="form-title">Register Now</h4>
      <p class="form-subtitle pb-4">
        Please Register With Your Official Email To Ensure Faster Processing And Quicker Invitation Delivery
      </p>

      <?php if($errors->any()): ?>
        <div class="alert alert-danger text-start">
          <ul class="mb-0">
            <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <li><?php echo e($error); ?></li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </ul>
        </div>
      <?php endif; ?>

      <form action="<?php echo e(route('web.register.store')); ?>" method="post" id="reg-form">
        <?php echo csrf_field(); ?>
        <input type="hidden" name="section"  value="<?php echo e($selectedSection); ?>">
        <input type="hidden" name="category" value="<?php echo e($selectedCategory); ?>">

        <div class="form-wrap">
          <span class="form-icon"><i class="fas fa-user"></i></span>
          <input class="form-input" id="full_name_en" type="text" name="name" placeholder="Full Name" required>
        </div>

        <div class="form-wrap">
          <span class="form-icon"><i class="fas fa-envelope"></i></span>
          <input class="form-input" id="email" type="email" placeholder="Email" name="email" required>
        </div>

        <div class="form-wrap">
          <span class="form-icon"><i class="fas fa-phone-alt"></i></span>
          <div class="phone-flex">
            <select class="form-select" name="country_code" id="country_code" required>
              <?php $__currentLoopData = $orderedIso; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $iso): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php
                  if (!isset($dialMap[$iso])) continue;
                  $dial     = $dialMap[$iso];
                  $label    = ($countryNames[$iso] ?? $iso) . ' (' . $dial . ')';
                  $selected = ($iso === $defaultIso);
                ?>
                <option value="<?php echo e($dial); ?>" data-iso="<?php echo e($iso); ?>" <?php echo e($selected ? 'selected' : ''); ?>>
                  <?php echo e($label); ?>

                </option>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
            <input class="form-input" id="phone" type="tel" name="phone" placeholder="Phone Number" required>
          </div>
          <small id="phone-error" class="text-danger d-none">Invalid phone number for selected country.</small>
        </div>

        <div class="form-wrap">
          <span class="form-icon"><i class="fas fa-briefcase"></i></span>
          <input class="form-input" id="job_title" type="text" name="job" placeholder="Job Title" required>
        </div>

        <input type="hidden" name="type" value="1">
        <button class="btn-square" type="submit">Submit</button>
      </form>
    </div>
  </div>
</section>


<script src="https://cdn.jsdelivr.net/npm/libphonenumber-js@1.10.53/bundle/libphonenumber-max.js"></script>

<script>
// ======================= Phone validation (libphonenumber + fallback) =======================
document.getElementById('reg-form').addEventListener('submit', function (e) {
  const select    = document.getElementById('country_code');
  const phoneEl   = document.getElementById('phone');
  const errorText = document.getElementById('phone-error');

  const iso  = (select.options[select.selectedIndex]?.dataset?.iso || 'AE').toUpperCase();
  const dial = select.value;

  const raw   = phoneEl.value.trim();
  const clean = raw.replace(/[^\d+]/g, '');

  let valid = false;

  try {
    const candidate = clean.startsWith('+') ? clean : (dial + clean).replace('++','+');
    const parsed = libphonenumber.parsePhoneNumberFromString(candidate, iso);
    if (parsed && parsed.isValid() && (!parsed.country || parsed.country === iso)) {
      valid = true;
    }
  } catch (_) {}

  // Fallback سريع لبعض الدول الشائعة
  if (!valid) {
    const fb = {
      'EG': /^0?(10|11|12|15)\d{8}$/,
      'SA': /^0?5\d{8}$/,
      'AE': /^0?5\d{8}$/,
      'OM': /^0?9\d{7}$/,
      'US': /^\d{10}$/,
      'GB': /^0?7\d{9}$/,
      'IN': /^[6-9]\d{9}$/,
    };
    const nat = clean.replace(/^\+?\d{1,3}/, '');
    if (fb[iso] && fb[iso].test(nat)) valid = true;
  }

  if (!valid) {
    e.preventDefault();
    errorText.classList.remove('d-none');
    phoneEl.classList.add('is-invalid');
  } else {
    errorText.classList.add('d-none');
    phoneEl.classList.remove('is-invalid');
  }
});

// ======================= Continuous auto-detect for country code (no external API) =======================
// يستمر لحدّ ما المستخدم يكتب أي رقم في الهاتف، ثم يتوقف
const isoToDial = <?php echo json_encode($dialMap, 15, 512) ?>;
const selectEl  = document.getElementById('country_code');
const phoneEl   = document.getElementById('phone');

let autoActive   = true;
let geoWatchId   = null;
let intervalId   = null;

phoneEl.addEventListener('input', () => { if (phoneEl.value.trim().length > 0) stopAutoDetect(); });
document.querySelector('form').addEventListener('submit', stopAutoDetect);
selectEl.addEventListener('change', () => { /* لا نوقف الأوتودتكت بمجرد تغيير الكود يدويًا، فقط عند كتابة رقم الهاتف */ });

startAutoDetect();

function startAutoDetect(){
  runDetectionOnce(); // محاولة أولى
  if ('geolocation' in navigator) {
    geoWatchId = navigator.geolocation.watchPosition(
      pos => { if (autoActive) updateDialFromCoords(pos.coords.latitude, pos.coords.longitude); },
      _err => {},
      { enableHighAccuracy:false, timeout:2000, maximumAge:600000 }
    );
  }
  intervalId = setInterval(() => { if (autoActive) runDetectionOnce(); }, 8000); // كل 8 ثواني
}
function stopAutoDetect(){
  autoActive = false;
  if (geoWatchId !== null) { navigator.geolocation.clearWatch(geoWatchId); geoWatchId = null; }
  if (intervalId !== null) { clearInterval(intervalId); intervalId = null; }
}
function applyDial(dial){
  if (!autoActive || !dial || !selectEl) return;
  if (phoneEl.value.trim().length > 0) { stopAutoDetect(); return; }
  if (selectEl.value === dial) return;
  const exists = [...selectEl.options].some(o => o.value === dial);
  if (exists) selectEl.value = dial;
}

// Timezone & Language كشف سريع
function runDetectionOnce(){
  const iso = detectISOByTimezone() || detectISOByLocale();
  if (iso && isoToDial[iso]) applyDial(isoToDial[iso]);
}

// جغرافيا تقريبية لبعض الدول (latMin, latMax, lonMin, lonMax) – كفاية العملية
const countryBoxes = {
  // MENA
  EG:[22,31.7,24.7,36.9], SA:[16,32.2,34,56.5], AE:[22.6,26.5,51.3,56.7], KW:[28.5,30.2,46.4,48.6], QA:[24.3,26.4,50.6,52.3], OM:[16.5,26.8,52,60],
  JO:[29,33.6,34.9,39.5], IQ:[29,37.6,38.8,48.8], SY:[32,37.5,35,42.4], LB:[33,34.8,35,36.7], PS:[31,32.7,34.2,35.7], YE:[12,19.5,42.5,54],
  MA:[27.5,35.9,-13.5,-1], DZ:[19,37, -9, 12], TN:[30,37.6,7.5,11.8], LY:[19.5,33.4,9.4,25.2], SD:[8.5,22.3,21.8,38.6],
  // Africa
  ZA:[-35, -22, 16, 33], SN:[12,16.9,-17.7,-11], NG:[4,14.2,2.7,14.7], GH:[4.5,11.2,-3.3,1.2], KE:[-4.8,5.2,33.9,41.9], TZ:[-11.8,-0.8,29.3,40.5], UG:[-1.5,4.3,29.5,35],
  ET:[3.3,14.9,32.9,48.0],
  // Europe
  GB:[49.9,59,-8.6,1.8], IE:[51.3,55.4,-10.7,-5.4], FR:[41,51.3,-5.5,9.8], DE:[47.2,55.2,5.8,15.1], IT:[36.6,47.2,6.5,18.8], ES:[36,43.9,-9.5,3.5], PT:[36.8,42.2,-9.6,-6.2],
  NL:[50.7,53.7,3.3,7.2], BE:[49.5,51.6,2.5,6.4], CH:[45.8,47.8,5.9,10.5], AT:[46.4,49.1,9.5,17.2], SE:[55,69,11,24.2], NO:[58,71,5,31.3], DK:[54.5,57.8,8,15.5],
  FI:[59.7,70.1,20.6,31.6], PL:[49,55,14,24.2], CZ:[48.5,51.1,12,18.9], HU:[45.7,48.6,16.1,22.9], RO:[43.6,48.3,20.2,29.7], BG:[41.2,44.3,22.3,28.6], SK:[47.7,49.7,16.8,22.6],
  SI:[45.4,46.9,13.4,16.6], HR:[42,46.8,13,19.5], EE:[57.5,59.7,21.7,28.2], LV:[55.7,58.1,20.9,28.3], LT:[53.9,56.5,20.9,26.8], LU:[49.4,50.3,5.7,6.5], MT:[35.7,36.3,14.1,14.7],
  CY:[34.5,35.8,32.2,34.6], GR:[34.7,41.9,19.2,28.6], IS:[63,66.6,-24.5,-13.5],
  // Americas
  US:[24.5,49.6,-125,-66.5], CA:[42,83,-141,-52], MX:[14.5,32.8,-118.5,-86], BR:[-33.9,5.5,-74,-34], AR:[-55.2,-21.5,-73.8,-53.5], CL:[-55,-17.5,-75.6,-66.4],
  CO:[-4.3,12.4,-79, -66.9], PE:[-18.3,-0.1,-81.4,-68.6], VE:[0.6,12.6,-73.4,-59.8], UY:[-35.2,-30.1,-58.5,-53], PY:[-27.6,-19.3,-62.7,-54.2], BO:[-22.9,-9.7,-69.6,-57.5], EC:[-5,1.7,-81,-75.2],
  CR:[8.0,11.3,-85.9,-82.5], PA:[7.2,9.7,-82.9,-77.1], GT:[13.7,17.8,-92.2,-88.2],
  // Asia-Pacific
  TR:[36,42.3,26,45], IR:[25.1,39.8,44,63.3], IL:[29.5,33.4,34.2,35.9], IN:[6.5,35.7,68,97.5], PK:[23.5,37.3,60.8,77.9], BD:[20.5,26.7,88,92.7], LK:[5.7,10.1,79.5,82.1], NP:[26.3,30.5,80,88.2],
  CN:[18,53.7,73.5,134.9], HK:[22.1,22.6,113.7,114.4], TW:[21.7,25.4,119.3,122.1], JP:[24,45.7,123.6,145.9], KR:[33,38.8,124.5,132],
  SG:[1.18,1.49,103.6,104.1], MY:[0.8,6.7,99.6,104.6], TH:[5.6,20.5,97.4,105.7], VN:[8.2,23.4,102.1,109.5], PH:[4.6,21.3,116.9,126.6], ID:[-11,6.3,95,141.3],
};

function updateDialFromCoords(lat, lon){
  // أولوية خاصة لمصر
  if (inBox(lat, lon, countryBoxes.EG)) { applyDial(isoToDial['EG']); return; }
  for (const [iso, box] of Object.entries(countryBoxes)){
    if (inBox(lat, lon, box)) { if (isoToDial[iso]) applyDial(isoToDial[iso]); return; }
  }
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
    // MENA & Africa
    'Africa/Cairo':'EG','Asia/Riyadh':'SA','Asia/Dubai':'AE','Asia/Kuwait':'KW','Asia/Qatar':'QA','Asia/Muscat':'OM','Asia/Bahrain':'BH','Asia/Amman':'JO','Asia/Baghdad':'IQ','Asia/Damascus':'SY',
    'Asia/Beirut':'LB','Asia/Gaza':'PS','Asia/Hebron':'PS','Africa/Casablanca':'MA','Africa/Algiers':'DZ','Africa/Tunis':'TN','Africa/Tripoli':'LY','Africa/Nouakchott':'MR',
    'Africa/Khartoum':'SD','Africa/Dakar':'SN','Africa/Lagos':'NG','Africa/Accra':'GH','Africa/Nairobi':'KE','Africa/Dar_es_Salaam':'TZ','Africa/Kampala':'UG','Africa/Johannesburg':'ZA','Africa/Addis_Ababa':'ET',
    // Europe
    'Europe/London':'GB','Europe/Dublin':'IE','Europe/Paris':'FR','Europe/Berlin':'DE','Europe/Rome':'IT','Europe/Madrid':'ES','Europe/Lisbon':'PT','Europe/Amsterdam':'NL','Europe/Brussels':'BE','Europe/Zurich':'CH','Europe/Vienna':'AT',
    'Europe/Stockholm':'SE','Europe/Oslo':'NO','Europe/Copenhagen':'DK','Europe/Helsinki':'FI','Europe/Warsaw':'PL','Europe/Prague':'CZ','Europe/Budapest':'HU','Europe/Bucharest':'RO','Europe/Sofia':'BG',
    'Europe/Bratislava':'SK','Europe/Ljubljana':'SI','Europe/Zagreb':'HR','Europe/Tallinn':'EE','Europe/Riga':'LV','Europe/Vilnius':'LT','Europe/Luxembourg':'LU','Europe/Malta':'MT','Asia/Nicosia':'CY','Europe/Athens':'GR','Atlantic/Reykjavik':'IS',
    // Americas
    'America/New_York':'US','America/Chicago':'US','America/Denver':'US','America/Los_Angeles':'US','America/Toronto':'CA','America/Vancouver':'CA','America/Mexico_City':'MX',
    'America/Sao_Paulo':'BR','America/Argentina/Buenos_Aires':'AR','America/Santiago':'CL','America/Bogota':'CO','America/Lima':'PE','America/Caracas':'VE','America/Montevideo':'UY','America/Asuncion':'PY','America/La_Paz':'BO','America/Guayaquil':'EC','America/Costa_Rica':'CR','America/Panama':'PA','America/Guatemala':'GT',
    // Asia-Pacific
    'Europe/Istanbul':'TR','Asia/Tehran':'IR','Asia/Jerusalem':'IL','Asia/Kolkata':'IN','Asia/Karachi':'PK','Asia/Dhaka':'BD','Asia/Colombo':'LK','Asia/Kathmandu':'NP',
    'Asia/Shanghai':'CN','Asia/Hong_Kong':'HK','Asia/Taipei':'TW','Asia/Tokyo':'JP','Asia/Seoul':'KR',
    'Asia/Singapore':'SG','Asia/Kuala_Lumpur':'MY','Asia/Bangkok':'TH','Asia/Ho_Chi_Minh':'VN','Asia/Manila':'PH','Asia/Jakarta':'ID'
  };
  return tzMap[tz] || null;
}
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('web.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/u552947370/domains/affiliatesummitglobal.com/public_html/resources/views/web/content/register_form.blade.php ENDPATH**/ ?>