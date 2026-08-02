@extends('web.layouts.app')

@section('content')
@php $locale = app()->getLocale(); @endphp

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
    background: linear-gradient(90deg, #000, #E73701); -webkit-background-clip: text; -webkit-text-fill-color: transparent;
  }
  .form-wrap { position: relative; margin-bottom: 20px; }
  .form-input, .form-select {
    width: 100%; padding: 15px 18px; padding-left: 45px; border: 2px solid #eee; border-radius: 12px;
    font-size: 16px; font-weight: 500; transition: 0.3s; background: #fff;
  }
  .select2, .select2-container .select2-selection--single { width: 200px !important; border-radius: 14px; }
  .form-input::placeholder { color: #999; }
  .form-input:focus, .form-select:focus { border-color: #E73701; box-shadow: 0 0 0 3px rgba(231,55,1,.15); outline: none; }
  .form-icon { position: absolute; top: 50%; left: 15px; transform: translateY(-50%); font-size: 18px; color: #E73701; }
  .btn-square {
    width: 100%; padding: 14px; background: linear-gradient(90deg, #E73701, #000); border: none; border-radius: 50px;
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
  @keyframes fadeInUp { from { opacity: 0; transform: translateY(30px); } to { opacity: 1; transform: translateY(0); } }
</style>

<section class="breadcrumbs-custom bg-image context-dark"
         style="background-image: url({{ asset('public/web/assets/images/bg-breadcrumbs-01-1894x424.jpg') }});">
  <div class="container text-center py-4">
    <h3 class="breadcrumbs-custom-title">Become Sponsor</h3>
  </div>
</section>

<section>
  <div class="container">
    <div class="form-card">
      <h4 class="form-title">Become Sponsor</h4>

      @if ($errors->any())
        <div class="alert alert-danger text-start">
          <ul class="mb-0">
            @foreach ($errors->all() as $error)
              <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
      @endif

      <form action="{{ route('web.register.store') }}" method="post" id="sponsor-form">
        @csrf

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
              {{-- MENA --}}
              <option value="+971" data-iso="AE">UAE (+971)</option>
              <option value="+20"  data-iso="EG">Egypt (+20)</option>
              <option value="+966" data-iso="SA">Saudi Arabia (+966)</option>
              <option value="+965" data-iso="KW">Kuwait (+965)</option>
              <option value="+974" data-iso="QA">Qatar (+974)</option>
              <option value="+968" data-iso="OM">Oman (+968)</option>
              <option value="+973" data-iso="BH">Bahrain (+973)</option>
              <option value="+962" data-iso="JO">Jordan (+962)</option>
              <option value="+964" data-iso="IQ">Iraq (+964)</option>
              <option value="+963" data-iso="SY">Syria (+963)</option>
              <option value="+961" data-iso="LB">Lebanon (+961)</option>
              <option value="+970" data-iso="PS">Palestine (+970)</option>
              <option value="+967" data-iso="YE">Yemen (+967)</option>
              <option value="+212" data-iso="MA">Morocco (+212)</option>
              <option value="+216" data-iso="TN">Tunisia (+216)</option>
              <option value="+218" data-iso="LY">Libya (+218)</option>
              <option value="+249" data-iso="SD">Sudan (+249)</option>
              {{-- Africa (extra) --}}
              <option value="+221" data-iso="SN">Senegal (+221)</option>
              <option value="+234" data-iso="NG">Nigeria (+234)</option>
              <option value="+233" data-iso="GH">Ghana (+233)</option>
              <option value="+254" data-iso="KE">Kenya (+254)</option>
              <option value="+255" data-iso="TZ">Tanzania (+255)</option>
              <option value="+256" data-iso="UG">Uganda (+256)</option>
              <option value="+27"  data-iso="ZA">South Africa (+27)</option>
              <option value="+251" data-iso="ET">Ethiopia (+251)</option>

              {{-- Europe --}}
              <option value="+44"  data-iso="GB">UK (+44)</option>
              <option value="+49"  data-iso="DE">Germany (+49)</option>
              <option value="+34"  data-iso="ES">Spain (+34)</option>
              <option value="+39"  data-iso="IT">Italy (+39)</option>
              <option value="+33"  data-iso="FR">France (+33)</option>
              <option value="+30"  data-iso="GR">Greece (+30)</option>
              <option value="+90"  data-iso="TR">Turkey (+90)</option>
              <option value="+31"  data-iso="NL">Netherlands (+31)</option>
              <option value="+32"  data-iso="BE">Belgium (+32)</option>
              <option value="+41"  data-iso="CH">Switzerland (+41)</option>
              <option value="+43"  data-iso="AT">Austria (+43)</option>
              <option value="+46"  data-iso="SE">Sweden (+46)</option>
              <option value="+47"  data-iso="NO">Norway (+47)</option>
              <option value="+45"  data-iso="DK">Denmark (+45)</option>
              <option value="+358" data-iso="FI">Finland (+358)</option>
              <option value="+48"  data-iso="PL">Poland (+48)</option>
              <option value="+420" data-iso="CZ">Czechia (+420)</option>
              <option value="+36"  data-iso="HU">Hungary (+36)</option>
              <option value="+40"  data-iso="RO">Romania (+40)</option>
              <option value="+359" data-iso="BG">Bulgaria (+359)</option>
              <option value="+421" data-iso="SK">Slovakia (+421)</option>
              <option value="+386" data-iso="SI">Slovenia (+386)</option>
              <option value="+385" data-iso="HR">Croatia (+385)</option>
              <option value="+372" data-iso="EE">Estonia (+372)</option>
              <option value="+371" data-iso="LV">Latvia (+371)</option>
              <option value="+370" data-iso="LT">Lithuania (+370)</option>
              <option value="+352" data-iso="LU">Luxembourg (+352)</option>
              <option value="+356" data-iso="MT">Malta (+356)</option>
              <option value="+357" data-iso="CY">Cyprus (+357)</option>
              <option value="+353" data-iso="IE">Ireland (+353)</option>
              <option value="+354" data-iso="IS">Iceland (+354)</option>
              
              {{-- Americas --}}
              <option value="+1"   data-iso="US">USA (+1)</option>
              <option value="+1"   data-iso="CA">Canada (+1)</option>
              <option value="+52"  data-iso="MX">Mexico (+52)</option>
              <option value="+55"  data-iso="BR">Brazil (+55)</option>
              <option value="+54"  data-iso="AR">Argentina (+54)</option>
              <option value="+56"  data-iso="CL">Chile (+56)</option>
              <option value="+57"  data-iso="CO">Colombia (+57)</option>
              <option value="+51"  data-iso="PE">Peru (+51)</option>
              <option value="+58"  data-iso="VE">Venezuela (+58)</option>
              <option value="+598" data-iso="UY">Uruguay (+598)</option>
              <option value="+595" data-iso="PY">Paraguay (+595)</option>
              <option value="+591" data-iso="BO">Bolivia (+591)</option>
              <option value="+593" data-iso="EC">Ecuador (+593)</option>
              <option value="+506" data-iso="CR">Costa Rica (+506)</option>
              <option value="+507" data-iso="PA">Panama (+507)</option>
              <option value="+502" data-iso="GT">Guatemala (+502)</option>

              {{-- Asia-Pacific --}}
              <option value="+91"  data-iso="IN">India (+91)</option>
              <option value="+86"  data-iso="CN">China (+86)</option>
              <option value="+92"  data-iso="PK">Pakistan (+92)</option>
              <option value="+81"  data-iso="JP">Japan (+81)</option>
              <option value="+82"  data-iso="KR">South Korea (+82)</option>
              <option value="+65"  data-iso="SG">Singapore (+65)</option>
              <option value="+60"  data-iso="MY">Malaysia (+60)</option>
              <option value="+66"  data-iso="TH">Thailand (+66)</option>
              <option value="+84"  data-iso="VN">Vietnam (+84)</option>
              <option value="+63"  data-iso="PH">Philippines (+63)</option>
              <option value="+62"  data-iso="ID">Indonesia (+62)</option>
              <option value="+880" data-iso="BD">Bangladesh (+880)</option>
              <option value="+94"  data-iso="LK">Sri Lanka (+94)</option>
              <option value="+977" data-iso="NP">Nepal (+977)</option>
              <option value="+852" data-iso="HK">Hong Kong (+852)</option>
              <option value="+886" data-iso="TW">Taiwan (+886)</option>
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

{{-- 1) libphonenumber for robust validation --}}
<script src="https://cdn.jsdelivr.net/npm/libphonenumber-js@1.10.53/bundle/libphonenumber-max.js"></script>

<script>
/* =========================================================
   A) Robust phone validation using libphonenumber-js
   ========================================================= */
document.getElementById('sponsor-form').addEventListener('submit', function (e) {
  const select    = document.getElementById('country_code');
  const phoneEl   = document.getElementById('phone');
  const errorText = document.getElementById('phone-error');

  const iso  = (select.options[select.selectedIndex]?.dataset?.iso || '').toUpperCase();
  const dial = select.value;

  const raw   = phoneEl.value.trim();
  const clean = raw.replace(/[^\d+]/g, '');

  let valid = false;

  try {
    // إن كان المستخدم كتب رقم بدون +، ضيف كود الدولة
    const candidate = clean.startsWith('+') ? clean : (dial + clean).replace('++','+');
    const parsed = (window.libphonenumber || libphonenumber).parsePhoneNumberFromString(candidate, iso || undefined);
    if (parsed && parsed.isValid()) {
      valid = true;
    }
  } catch (_) {}

  // Fallback عملي لبعض الدول الشائعة (اختياري)
 


});

/* =========================================================
   B) Auto-select country code by IP (ipapi) with fallbacks
   - Tries ipapi.co first (country_calling_code / country)
   - Falls back to Timezone → Locale → Geolocation boxes
   - Stops overriding once user starts typing a phone number
   ========================================================= */
const selectEl  = document.getElementById('country_code');
const phoneEl   = document.getElementById('phone');

let autoActive  = true;     // طالما المستخدم لسه ما كتبش رقم
let geoWatchId  = null;     // geolocation watcher id
let intervalId  = null;     // periodic fallback timer

// Helper: does select have this dial code?
function hasDial(dial){ return [...selectEl.options].some(o => o.value === dial); }
// Helper: set select to dial if possible
function setDial(dial){ if (autoActive && hasDial(dial)) selectEl.value = dial; }
// Build ISO → Dial map from the options (no hardcoding)
function buildIsoToDial(){
  const map = {};
  [...selectEl.options].forEach(o => {
    const iso = (o.dataset.iso || '').toUpperCase();
    if (iso) map[iso] = o.value;
  });
  return map;
}
const isoToDial = buildIsoToDial();

phoneEl.addEventListener('input', () => {
  if (phoneEl.value.trim().length > 0) stopAutoDetect();
});
document.getElementById('sponsor-form').addEventListener('submit', stopAutoDetect);

// Try IP first
initIPThenFallback();

async function initIPThenFallback(){
  try {
    const res = await fetch('https://ipapi.co/json/');
    if (res.ok) {
      const data = await res.json();
      const ipDial = (data.country_calling_code || '').trim(); // e.g. +20
      const iso    = (data.country || '').toUpperCase();       // e.g. EG
      // First try dial directly, then ISO
      if (ipDial && hasDial(ipDial)) {
        setDial(ipDial);
      } else if (iso && isoToDial[iso]) {
        setDial(isoToDial[iso]);
      }
    }
  } catch(_) { /* ignore & fallback */ }

  // Start passive fallbacks as well
  startFallbackDetectors();
}

function startFallbackDetectors(){
  // 1) Quick: Timezone → Locale
  applyQuickDetect();

  // 2) Geolocation watch (approx bounding boxes)
  if ('geolocation' in navigator) {
    geoWatchId = navigator.geolocation.watchPosition(
      pos => {
        if (!autoActive) return;
        const iso = approxISOFromLatLon(pos.coords.latitude, pos.coords.longitude);
        if (iso && isoToDial[iso]) setDial(isoToDial[iso]);
      },
      _err => {},
      { enableHighAccuracy:false, timeout:2000, maximumAge:600000 }
    );
  }

  // 3) Periodic retry of quick detect (in case timezone/locale becomes available)
  intervalId = setInterval(() => { if (autoActive) applyQuickDetect(); }, 8000);
}

function stopAutoDetect(){
  autoActive = false;
  if (geoWatchId !== null) { navigator.geolocation.clearWatch(geoWatchId); geoWatchId = null; }
  if (intervalId !== null) { clearInterval(intervalId); intervalId = null; }
}

function applyQuickDetect(){
  const iso = detectISOByTimezone() || detectISOByLocale();
  if (iso && isoToDial[iso]) setDial(isoToDial[iso]);
}

/* ====== Timezone & Locale detection ====== */
function detectISOByLocale(){
  const arr = navigator.languages && navigator.languages.length
    ? navigator.languages
    : [navigator.language, navigator.userLanguage, navigator.browserLanguage].filter(Boolean);
  for (const lang of arr){
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
    'Asia/Beirut':'LB','Asia/Gaza':'PS','Asia/Hebron':'PS','Africa/Casablanca':'MA','Africa/Algiers':'DZ','Africa/Tunis':'TN','Africa/Tripoli':'LY','Africa/Khartoum':'SD',
    'Africa/Dakar':'SN','Africa/Lagos':'NG','Africa/Accra':'GH','Africa/Nairobi':'KE','Africa/Dar_es_Salaam':'TZ','Africa/Kampala':'UG','Africa/Johannesburg':'ZA','Africa/Addis_Ababa':'ET',
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

/* ====== Approx geolocation (country bounding boxes) ====== */
const countryBoxes = {
  // MENA
  EG:[22,31.7,24.7,36.9], SA:[16,32.2,34,56.5], AE:[22.6,26.5,51.3,56.7], KW:[28.5,30.2,46.4,48.6], QA:[24.3,26.4,50.6,52.3], OM:[16.5,26.8,52,60],
  JO:[29,33.6,34.9,39.5], IQ:[29,37.6,38.8,48.8], SY:[32,37.5,35,42.4], LB:[33,34.8,35,36.7], PS:[31,32.7,34.2,35.7], YE:[12,19.5,42.5,54],
  MA:[27.5,35.9,-13.5,-1], DZ:[19,37,-9,12], TN:[30,37.6,7.5,11.8], LY:[19.5,33.4,9.4,25.2], SD:[8.5,22.3,21.8,38.6],
  // Africa
  ZA:[-35,-22,16,33], SN:[12,16.9,-17.7,-11], NG:[4,14.2,2.7,14.7], GH:[4.5,11.2,-3.3,1.2], KE:[-4.8,5.2,33.9,41.9], TZ:[-11.8,-0.8,29.3,40.5], UG:[-1.5,4.3,29.5,35],
  ET:[3.3,14.9,32.9,48.0],
  // Europe
  GB:[49.9,59,-8.6,1.8], IE:[51.3,55.4,-10.7,-5.4], FR:[41,51.3,-5.5,9.8], DE:[47.2,55.2,5.8,15.1], IT:[36.6,47.2,6.5,18.8], ES:[36,43.9,-9.5,3.5], PT:[36.8,42.2,-9.6,-6.2],
  NL:[50.7,53.7,3.3,7.2], BE:[49.5,51.6,2.5,6.4], CH:[45.8,47.8,5.9,10.5], AT:[46.4,49.1,9.5,17.2], SE:[55,69,11,24.2], NO:[58,71,5,31.3], DK:[54.5,57.8,8,15.5],
  FI:[59.7,70.1,20.6,31.6], PL:[49,55,14,24.2], CZ:[48.5,51.1,12,18.9], HU:[45.7,48.6,16.1,22.9], RO:[43.6,48.3,20.2,29.7], BG:[41.2,44.3,22.3,28.6], SK:[47.7,49.7,16.8,22.6],
  SI:[45.4,46.9,13.4,16.6], HR:[42,46.8,13,19.5], EE:[57.5,59.7,21.7,28.2], LV:[55.7,58.1,20.9,28.3], LT:[53.9,56.5,20.9,26.8], LU:[49.4,50.3,5.7,6.5], MT:[35.7,36.3,14.1,14.7],
  CY:[34.5,35.8,32.2,34.6], GR:[34.7,41.9,19.2,28.6], IS:[63,66.6,-24.5,-13.5],
  // Americas
  US:[24.5,49.6,-125,-66.5], CA:[42,83,-141,-52], MX:[14.5,32.8,-118.5,-86], BR:[-33.9,5.5,-74,-34], AR:[-55.2,-21.5,-73.8,-53.5], CL:[-55,-17.5,-75.6,-66.4],
  CO:[-4.3,12.4,-79,-66.9], PE:[-18.3,-0.1,-81.4,-68.6], VE:[0.6,12.6,-73.4,-59.8], UY:[-35.2,-30.1,-58.5,-53], PY:[-27.6,-19.3,-62.7,-54.2], BO:[-22.9,-9.7,-69.6,-57.5], EC:[-5,1.7,-81,-75.2],
  CR:[8.0,11.3,-85.9,-82.5], PA:[7.2,9.7,-82.9,-77.1], GT:[13.7,17.8,-92.2,-88.2],
  // Asia-Pacific
  TR:[36,42.3,26,45], IR:[25.1,39.8,44,63.3], IL:[29.5,33.4,34.2,35.9], IN:[6.5,35.7,68,97.5], PK:[23.5,37.3,60.8,77.9], BD:[20.5,26.7,88,92.7], LK:[5.7,10.1,79.5,82.1], NP:[26.3,30.5,80,88.2],
  CN:[18,53.7,73.5,134.9], HK:[22.1,22.6,113.7,114.4], TW:[21.7,25.4,119.3,122.1], JP:[24,45.7,123.6,145.9], KR:[33,38.8,124.5,132],
  SG:[1.18,1.49,103.6,104.1], MY:[0.8,6.7,99.6,104.6], TH:[5.6,20.5,97.4,105.7], VN:[8.2,23.4,102.1,109.5], PH:[4.6,21.3,116.9,126.6], ID:[-11,6.3,95,141.3],
};

function approxISOFromLatLon(lat, lon){
  // Priority for Egypt (requested)
  if (inBox(lat, lon, countryBoxes.EG)) return 'EG';
  for (const [iso, box] of Object.entries(countryBoxes)){
    if (inBox(lat, lon, box)) return iso;
  }
  return null;
}
function inBox(lat, lon, box){
  if (!box) return false;
  const [latMin, latMax, lonMin, lonMax] = box;
  return lat >= latMin && lat <= latMax && lon >= lonMin && lon <= lonMax;
}
</script>
@endsection
