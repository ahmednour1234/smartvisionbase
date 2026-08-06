@extends('web.layouts.app')

@section('content')
@php
  // ====== أسماء الدول وأكواد الاتصال (موسّعة) ======
  if (!function_exists('iso_flag')) {
      function iso_flag($iso) {
          $iso = strtoupper($iso ?? '');
          if (strlen($iso) !== 2) return '';
          $offset = 127397;
          $f = fn($c) => mb_convert_encoding('&#' . ($offset + ord($c)) . ';', 'UTF-8', 'HTML-ENTITIES');
          return $f($iso[0]) . $f($iso[1]);
      }
  }

  $countryNames = [
    // MENA & Africa
    'AE'=>'United Arab Emirates','EG'=>'Egypt','SA'=>'Saudi Arabia','KW'=>'Kuwait','QA'=>'Qatar','OM'=>'Oman','BH'=>'Bahrain','JO'=>'Jordan','IQ'=>'Iraq','SY'=>'Syria','LB'=>'Lebanon','PS'=>'Palestine','YE'=>'Yemen',
    'MA'=>'Morocco','DZ'=>'Algeria','TN'=>'Tunisia','LY'=>'Libya','MR'=>'Mauritania','SD'=>'Sudan','SS'=>'South Sudan','SO'=>'Somalia','DJ'=>'Djibouti',
    'SN'=>'Senegal','GM'=>'Gambia','GN'=>'Guinea','GW'=>'Guinea-Bissau','SL'=>'Sierra Leone','LR'=>'Liberia','GH'=>'Ghana','CI'=>'Côte d’Ivoire','BF'=>'Burkina Faso','NE'=>'Niger','ML'=>'Mali','TG'=>'Togo','BJ'=>'Benin',
    'NG'=>'Nigeria','CM'=>'Cameroon','TD'=>'Chad','CF'=>'Central African Republic','GQ'=>'Equatorial Guinea','GA'=>'Gabon','CG'=>'Congo','CD'=>'DR Congo','AO'=>'Angola','ST'=>'São Tomé and Príncipe',
    'ET'=>'Ethiopia','ER'=>'Eritrea','KE'=>'Kenya','UG'=>'Uganda','RW'=>'Rwanda','BI'=>'Burundi','TZ'=>'Tanzania','MW'=>'Malawi','MZ'=>'Mozambique','ZM'=>'Zambia','ZW'=>'Zimbabwe',
    'BW'=>'Botswana','NA'=>'Namibia','ZA'=>'South Africa','LS'=>'Lesotho','SZ'=>'Eswatini','MG'=>'Madagascar','MU'=>'Mauritius','SC'=>'Seychelles','KM'=>'Comoros',

    // Europe
    'GB'=>'United Kingdom','IE'=>'Ireland','FR'=>'France','DE'=>'Germany','IT'=>'Italy','ES'=>'Spain','PT'=>'Portugal','NL'=>'Netherlands','BE'=>'Belgium','CH'=>'Switzerland','AT'=>'Austria',
    'SE'=>'Sweden','NO'=>'Norway','DK'=>'Denmark','FI'=>'Finland','IS'=>'Iceland',
    'PL'=>'Poland','CZ'=>'Czechia','SK'=>'Slovakia','HU'=>'Hungary','RO'=>'Romania','BG'=>'Bulgaria','GR'=>'Greece','CY'=>'Cyprus','MT'=>'Malta','LU'=>'Luxembourg','LI'=>'Liechtenstein',
    'SI'=>'Slovenia','HR'=>'Croatia','BA'=>'Bosnia & Herzegovina','RS'=>'Serbia','ME'=>'Montenegro','MK'=>'North Macedonia','AL'=>'Albania','XK'=>'Kosovo',
    'EE'=>'Estonia','LV'=>'Latvia','LT'=>'Lithuania','UA'=>'Ukraine','MD'=>'Moldova','BY'=>'Belarus','RU'=>'Russia',

    // Americas
    'US'=>'United States','CA'=>'Canada','MX'=>'Mexico','GT'=>'Guatemala','HN'=>'Honduras','SV'=>'El Salvador','NI'=>'Nicaragua','CR'=>'Costa Rica','PA'=>'Panama','BZ'=>'Belize',
    'CU'=>'Cuba','DO'=>'Dominican Republic','HT'=>'Haiti','JM'=>'Jamaica','TT'=>'Trinidad & Tobago','BS'=>'Bahamas','BB'=>'Barbados','AG'=>'Antigua & Barbuda','DM'=>'Dominica','GD'=>'Grenada','LC'=>'Saint Lucia','VC'=>'Saint Vincent & the Grenadines',
    'AR'=>'Argentina','BR'=>'Brazil','CL'=>'Chile','UY'=>'Uruguay','PY'=>'Paraguay','BO'=>'Bolivia','PE'=>'Peru','EC'=>'Ecuador','CO'=>'Colombia','VE'=>'Venezuela','GY'=>'Guyana','SR'=>'Suriname',

    // Asia & Pacific
    'TR'=>'Turkey','IR'=>'Iran','IL'=>'Israel','IN'=>'India','PK'=>'Pakistan','BD'=>'Bangladesh','LK'=>'Sri Lanka','NP'=>'Nepal','AF'=>'Afghanistan',
    'CN'=>'China','HK'=>'Hong Kong','MO'=>'Macau','TW'=>'Taiwan','JP'=>'Japan','KR'=>'South Korea','MN'=>'Mongolia',
    'KZ'=>'Kazakhstan','KG'=>'Kyrgyzstan','TJ'=>'Tajikistan','TM'=>'Turkmenistan','UZ'=>'Uzbekistan','AZ'=>'Azerbaijan','AM'=>'Armenia','GE'=>'Georgia',
    'SG'=>'Singapore','MY'=>'Malaysia','TH'=>'Thailand','VN'=>'Vietnam','PH'=>'Philippines','ID'=>'Indonesia','KH'=>'Cambodia','LA'=>'Laos','MM'=>'Myanmar','BN'=>'Brunei',
    'AU'=>'Australia','NZ'=>'New Zealand','PG'=>'Papua New Guinea','FJ'=>'Fiji','WS'=>'Samoa','TO'=>'Tonga','TV'=>'Tuvalu','VU'=>'Vanuatu','SB'=>'Solomon Islands','KI'=>'Kiribati'
  ];

  $dialMap = [
    // MENA & Africa
    'AE'=>'+971','EG'=>'+20','SA'=>'+966','KW'=>'+965','QA'=>'+974','OM'=>'+968','BH'=>'+973','JO'=>'+962','IQ'=>'+964','SY'=>'+963','LB'=>'+961','PS'=>'+970','YE'=>'+967',
    'MA'=>'+212','DZ'=>'+213','TN'=>'+216','LY'=>'+218','MR'=>'+222','SD'=>'+249','SS'=>'+211','SO'=>'+252','DJ'=>'+253',
    'SN'=>'+221','GM'=>'+220','GN'=>'+224','GW'=>'+245','SL'=>'+232','LR'=>'+231','GH'=>'+233','CI'=>'+225','BF'=>'+226','NE'=>'+227','ML'=>'+223','TG'=>'+228','BJ'=>'+229',
    'NG'=>'+234','CM'=>'+237','TD'=>'+235','CF'=>'+236','GQ'=>'+240','GA'=>'+241','CG'=>'+242','CD'=>'+243','AO'=>'+244','ST'=>'+239',
    'ET'=>'+251','ER'=>'+291','KE'=>'+254','UG'=>'+256','RW'=>'+250','BI'=>'+257','TZ'=>'+255','MW'=>'+265','MZ'=>'+258','ZM'=>'+260','ZW'=>'+263',
    'BW'=>'+267','NA'=>'+264','ZA'=>'+27','LS'=>'+266','SZ'=>'+268','MG'=>'+261','MU'=>'+230','SC'=>'+248','KM'=>'+269',

    // Europe
    'GB'=>'+44','IE'=>'+353','FR'=>'+33','DE'=>'+49','IT'=>'+39','ES'=>'+34','PT'=>'+351','NL'=>'+31','BE'=>'+32','CH'=>'+41','AT'=>'+43',
    'SE'=>'+46','NO'=>'+47','DK'=>'+45','FI'=>'+358','IS'=>'+354',
    'PL'=>'+48','CZ'=>'+420','SK'=>'+421','HU'=>'+36','RO'=>'+40','BG'=>'+359','GR'=>'+30','CY'=>'+357','MT'=>'+356','LU'=>'+352','LI'=>'+423',
    'SI'=>'+386','HR'=>'+385','BA'=>'+387','RS'=>'+381','ME'=>'+382','MK'=>'+389','AL'=>'+355','XK'=>'+383',
    'EE'=>'+372','LV'=>'+371','LT'=>'+370','UA'=>'+380','MD'=>'+373','BY'=>'+375','RU'=>'+7',

    // Americas
    'US'=>'+1','CA'=>'+1','MX'=>'+52','GT'=>'+502','HN'=>'+504','SV'=>'+503','NI'=>'+505','CR'=>'+506','PA'=>'+507','BZ'=>'+501',
    'CU'=>'+53','DO'=>'+1','HT'=>'+509','JM'=>'+1','TT'=>'+1','BS'=>'+1','BB'=>'+1','AG'=>'+1','DM'=>'+1','GD'=>'+1','LC'=>'+1','VC'=>'+1',
    'AR'=>'+54','BR'=>'+55','CL'=>'+56','UY'=>'+598','PY'=>'+595','BO'=>'+591','PE'=>'+51','EC'=>'+593','CO'=>'+57','VE'=>'+58','GY'=>'+592','SR'=>'+597',

    // Asia & Pacific
    'TR'=>'+90','IR'=>'+98','IL'=>'+972','IN'=>'+91','PK'=>'+92','BD'=>'+880','LK'=>'+94','NP'=>'+977','AF'=>'+93',
    'CN'=>'+86','HK'=>'+852','MO'=>'+853','TW'=>'+886','JP'=>'+81','KR'=>'+82','MN'=>'+976',
    'KZ'=>'+7','KG'=>'+996','TJ'=>'+992','TM'=>'+993','UZ'=>'+998','AZ'=>'+994','AM'=>'+374','GE'=>'+995',
    'SG'=>'+65','MY'=>'+60','TH'=>'+66','VN'=>'+84','PH'=>'+63','ID'=>'+62','KH'=>'+855','LA'=>'+856','MM'=>'+95','BN'=>'+673',
    'AU'=>'+61','NZ'=>'+64','PG'=>'+675','FJ'=>'+679','WS'=>'+685','TO'=>'+676','TV'=>'+688','VU'=>'+678','SB'=>'+677','KI'=>'+686',
  ];

  $orderedIso = [
    // MENA أولًا
    'AE','EG','SA','KW','QA','OM','BH','JO','IQ','SY','LB','PS','YE','MA','DZ','TN','LY','MR','SD','SS','SO','DJ',
    // إفريقيا
    'SN','GM','GN','GW','SL','LR','GH','CI','BF','NE','ML','TG','BJ','NG','CM','TD','CF','GQ','GA','CG','CD','AO','ST','ET','ER','KE','UG','RW','BI','TZ','MW','MZ','ZM','ZW','BW','NA','ZA','LS','SZ','MG','MU','SC','KM',
    // أوروبا
    'GB','IE','FR','DE','IT','ES','PT','NL','BE','CH','AT','SE','NO','DK','FI','IS','PL','CZ','SK','HU','RO','BG','GR','CY','MT','LU','LI','SI','HR','BA','RS','ME','MK','AL','XK','EE','LV','LT','UA','MD','BY','RU',
    // الأمريكتان
    'US','CA','MX','GT','HN','SV','NI','CR','PA','BZ','CU','DO','HT','JM','TT','BS','BB','AG','DM','GD','LC','VC','AR','BR','CL','UY','PY','BO','PE','EC','CO','VE','GY','SR',
    // آسيا والمحيط الهادئ
    'TR','IR','IL','IN','PK','BD','LK','NP','AF','CN','HK','MO','TW','JP','KR','MN','KZ','KG','TJ','TM','UZ','AZ','AM','GE','SG','MY','TH','VN','PH','ID','KH','LA','MM','BN','AU','NZ','PG','FJ','WS','TO','TV','VU','SB','KI',
  ];

  // ====== الافتراضي + كشف الدولة من IP ======
  $defaultIso  = 'AE';
  $defaultDial = $dialMap[$defaultIso];

  try {
      if (function_exists('geoip')) {
          $geo = @geoip(request()->ip());
          $detectedIso = isset($geo->iso_code) ? strtoupper($geo->iso_code) : null;
          if ($detectedIso && isset($dialMap[$detectedIso])) {
              $defaultIso  = $detectedIso;
              $defaultDial = $dialMap[$detectedIso];
          }
      }
  } catch (\Throwable $e) {
      // ابق على الافتراضيات
  }
@endphp

<style>
/* ============ تنسيقاتك الأساسية (مختصرة) ============ */
@import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700;800&display=swap');
:root{--primary-gold:#FFE986;--secondary-gold:#C48127;--dark-bg:#0a0a0a;--card-bg:rgba(20,20,20,.95);--input-bg:rgba(255,255,255,.05);--input-border:rgba(255,233,134,.3);--input-focus:rgba(255,233,134,.6);--text-primary:#fff;--text-secondary:rgba(255,255,255,.7);--text-muted:rgba(255,255,255,.5);--error-color:#ff4757;--success-color:#2ed573;}
.page-header__inner{text-align:center;padding:95px 0 60px}
#typed-title{display:inline-block;overflow:hidden;white-space:nowrap;border-right:3px solid var(--primary-gold);font-family:'Montserrat',sans-serif;font-size:60px;background:linear-gradient(90deg,var(--primary-gold),var(--secondary-gold));-webkit-background-clip:text;-webkit-text-fill-color:transparent;color:transparent;animation:blink-caret .75s step-end infinite}
#typed-title.finished{border-right:none;animation:none}
@keyframes blink-caret{0%,100%{border-color:transparent}50%{border-color:var(--primary-gold)}}
#subtitle{background:linear-gradient(90deg,var(--primary-gold),var(--secondary-gold));-webkit-background-clip:text;-webkit-text-fill-color:transparent;color:transparent;font-size:40px;opacity:0;transform:translateY(20px);transition:opacity .5s,transform .5s}
#subtitle.visible{opacity:1;transform:translateY(0)}
@media (max-width:767.98px){#typed-title{font-size:32px}#subtitle{font-size:24px}}

.sponsor-section{padding:40px 0 80px}
.form-container{background:linear-gradient(90deg,rgb(60,42,30) 0%,rgb(26,18,11) 70%);backdrop-filter:blur(20px);border-radius:24px;padding:30px;box-shadow:0 32px 64px rgba(0,0,0,.4),0 0 0 1px rgba(255,233,134,.1),inset 0 1px 0 rgba(255,255,255,.1);position:relative;overflow:hidden}
.form-container::before{content:'';position:absolute;top:0;left:0;right:0;height:2px;background:linear-gradient(90deg,var(--primary-gold),var(--secondary-gold))}
.form-container.dropdown-open{overflow:visible;z-index:999} /* مهم للـ scroll */

.form-group{margin-bottom:24px;position:relative}
.form-label{position:absolute;top:50%;left:20px;transform:translateY(-50%);font-size:14px;font-weight:500;color:var(--text-muted);pointer-events:none;transition:all .3s;z-index:2;padding:0 8px}
.form-input{width:100%;height:52px;padding:18px;background:var(--input-bg);border:2px solid var(--input-border);border-radius:12px;font-size:14px;font-weight:500;color:var(--text-primary);transition:all .3s;outline:none}
.form-input:focus{border-color:var(--input-focus);background:rgba(255,233,134,.08);box-shadow:0 0 0 4px rgba(255,233,134,.1),0 8px 24px rgba(255,233,134,.15);transform:translateY(-2px)}
.form-input:focus + .form-label,.form-input:not(:placeholder-shown) + .form-label{top:0;transform:translateY(-50%);font-size:12px;font-weight:600;color:var(--primary-gold);background:var(--card-bg);text-transform:uppercase;letter-spacing:.5px}

.phone-group{display:flex;gap:12px;align-items:flex-start}
.country-select-container{position:relative;flex:0 0 180px}
.country-select{width:100%;height:52px;padding:0 18px;background:var(--input-bg);border:2px solid var(--input-border);border-radius:12px;font-size:13px;font-weight:600;color:var(--text-primary);appearance:none;cursor:pointer;transition:all .3s;background-image:url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%23FFE986' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='m6 8 4 4 4-4'/%3e%3c/svg%3e");background-position:right 12px center;background-repeat:no-repeat;background-size:14px;padding-right:40px;z-index:2;position:relative}
.country-select:focus{border-color:var(--input-focus);background:rgba(255,233,134,.08);box-shadow:0 0 0 4px rgba(255,233,134,.1),0 8px 24px rgba(255,233,134,.15);transform:translateY(-2px)}
/* ===== وضع الـ Scroll الحقيقي عند الفتح ===== */
.country-select.expanded{position:absolute;left:0;right:0;z-index:100;max-height:320px;overflow-y:auto;background:var(--input-bg);border-radius:12px}
.country-select.expanded{scrollbar-width:thin}
.country-select.expanded::-webkit-scrollbar{width:10px}
.country-select.expanded::-webkit-scrollbar-track{background:rgba(255,255,255,.08);border-radius:10px}
.country-select.expanded::-webkit-scrollbar-thumb{background:rgba(255,233,134,.5);border-radius:10px}
.country-select.expanded::-webkit-scrollbar-thumb:hover{background:rgba(255,233,134,.8)}

.phone-input{flex:1;margin-bottom:0}
.file-input-wrapper{position:relative;overflow:hidden;border:2px dashed var(--input-border);border-radius:12px;background:var(--input-bg);transition:all .3s;cursor:pointer;padding:20px;text-align:center}
.file-input-wrapper:hover{border-color:var(--input-focus);background:rgba(255,233,134,.05);transform:translateY(-2px)}
.file-input-wrapper input[type="file"]{position:absolute;left:-9999px;opacity:0}
.file-input-content{color:var(--text-secondary)}
.file-input-icon{font-size:24px;color:var(--primary-gold);margin-bottom:8px;font-weight:bold}
.file-input-icon::before{content:"+"}
.file-input-text{font-size:13px;font-weight:500;margin-bottom:6px}
.file-input-subtext{font-size:11px;color:var(--text-muted);opacity:.8}

.submit-btn{width:100%;height:52px;background:linear-gradient(135deg,var(--primary-gold) 0%,var(--secondary-gold) 100%);color:var(--dark-bg);border:none;border-radius:12px;font-size:16px;font-weight:700;text-transform:uppercase;letter-spacing:1px;cursor:pointer;transition:all .3s;position:relative;overflow:hidden;box-shadow:0 8px 24px rgba(255,233,134,.3)}
.submit-btn::before{content:'';position:absolute;top:0;left:-100%;width:100%;height:100%;background:linear-gradient(135deg,rgba(255,255,255,.2) 0%,transparent 100%);transition:left .5s}
.submit-btn:hover{transform:translateY(-4px);box-shadow:0 12px 32px rgba(255,233,134,.4)}
.submit-btn:hover::before{left:100%}
.submit-btn:active{transform:translateY(-2px)}

.alert-danger{background:rgba(255,71,87,.1);border:1px solid rgba(255,71,87,.3);color:var(--error-color);padding:16px;border-radius:12px;margin-bottom:24px}
.text-danger{color:var(--error-color)!important;font-size:11px;font-weight:500;margin-top:6px}
.is-invalid{border-color:var(--error-color)!important;box-shadow:0 0 0 4px rgba(255,71,87,.1)!important}

@media (max-width:991px){.phone-group{flex-direction:column;gap:16px}.country-select-container{flex:1}}
@media (max-width:768px){.page-header{padding:50px 0 20px}.form-container{padding:24px 16px;border-radius:20px}.file-input-wrapper{padding:16px}#typed-title{font-size:2rem}}
@media (max-width:576px){.sponsor-section{padding:30px 0 60px}.form-container{padding:20px 16px;border-radius:16px}.form-input,.country-select{height:48px;padding:16px}.submit-btn{height:48px;font-size:14px}#typed-title{font-size:1.8rem}.phone-group{flex-direction:row;gap:8px}.country-select-container{flex:0 0 140px}.country-select{padding:0 12px;font-size:12px;background-position:right 8px center}.phone-input .form-input{padding:16px 12px;font-size:14px}}
</style>

<!-- Header -->
<section class="page-header">
  <div class="container">
    <div class="page-header__inner">
      <h2 id="typed-title"></h2>
      <h3 id="subtitle">Be a part of our luxury FX community!</h3>
    </div>
  </div>
</section>

<!-- Form -->
<section class="sponsor-section">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-lg-8 col-xl-6">
        <div class="form-container fade-in" id="formContainer">
          @if ($errors->any())
            <div class="alert alert-danger">
              <ul class="mb-0">
                @foreach ($errors->all() as $error)
                  <li>{{ $error }}</li>
                @endforeach
              </ul>
            </div>
          @endif

          <form action="{{ route('web.luxury-members.store') }}" method="post" id="sponsor-form" enctype="multipart/form-data" novalidate>
            @csrf

            <div class="form-group">
              <input class="form-input" id="name" type="text" name="name" placeholder=" " required>
              <label class="form-label" for="name">Full Name</label>
            </div>

            <div class="form-group">
              <input class="form-input" id="title" type="text" name="title" placeholder=" " required>
              <label class="form-label" for="title">Job Title</label>
            </div>

            <div class="form-group">
              <input class="form-input" id="company" type="text" name="company" placeholder=" " required>
              <label class="form-label" for="company">Company Name</label>
            </div>

            <div class="form-group">
              <input class="form-input" id="email" type="email" name="email" placeholder=" " required>
              <label class="form-label" for="email">Email Address</label>
            </div>

            <!-- Phone + Country -->
            <div class="form-group">
              <div class="phone-group">
                <div class="country-select-container">
                  <input type="hidden" name="country_iso" id="country_iso" value="{{ $defaultIso }}">
                  <select class="country-select" name="country_code" id="country_code" data-initial-iso="{{ $defaultIso }}" required>
                    @foreach($orderedIso as $iso)
                      @php
                        if (!isset($dialMap[$iso])) continue;
                        $dial  = $dialMap[$iso];
                        $name  = $countryNames[$iso] ?? $iso;
                        $flag  = iso_flag($iso);
                        $sel   = $iso === $defaultIso ? 'selected' : '';
                      @endphp
                      <option value="{{ $dial }}" data-iso="{{ $iso }}" {!! $sel ? 'selected' : '' !!}>
                        {!! $flag !!} {{ $name }} ({{ $dial }})
                      </option>
                    @endforeach
                  </select>
                </div>
                <div class="form-group phone-input">
                  <input class="form-input" id="phone" type="tel" name="phone" placeholder=" " inputmode="tel" required>
                  <label class="form-label" for="phone">Phone Number</label>
                </div>
              </div>
              <small id="phone-error" class="text-danger d-none">Invalid phone number for selected country</small>
            </div>

            <div class="form-group">
              <label class="form-label" for="image" style="position:static;transform:none;color:var(--text-secondary);font-weight:600;text-transform:uppercase;letter-spacing:.5px;margin-bottom:10px">Profile Image</label>
              <div class="file-input-wrapper">
                <input id="image" type="file" name="image" accept="image/*">
                <div class="file-input-content">
                  <div class="file-input-icon"></div>
                  <div class="file-input-text">Click to upload image</div>
                  <div class="file-input-subtext">PNG, JPG, JPEG up to 2MB</div>
                </div>
              </div>
            </div>

            <input type="hidden" name="type" value="2">
            <button class="submit-btn" type="submit">Join Community</button>
          </form>

        </div>
      </div>
    </div>
  </div>
</section>

{{-- libphonenumber --}}
<script src="https://cdn.jsdelivr.net/npm/libphonenumber-js@1.10.53/bundle/libphonenumber-max.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
  // ===== Typewriter =====
  const titleEl = document.getElementById("typed-title");
  const subtitleEl = document.getElementById("subtitle");
  const text = "Join Community"; let i=0, speed=120;
  (function type(){ if(i<text.length){ titleEl.innerHTML += text.charAt(i++); setTimeout(type,speed);} else { titleEl.classList.add('finished'); setTimeout(()=>subtitleEl.classList.add('visible'),300);} })();

  // ===== File input nicer =====
  const fileInput = document.getElementById('image');
  const fileWrapper = document.querySelector('.file-input-wrapper');
  const fileContent = fileWrapper?.querySelector('.file-input-content');
  fileWrapper?.addEventListener('click', (e)=>{ if (e.target===fileWrapper || e.target.closest('.file-input-content')) fileInput?.click(); });
  fileInput?.addEventListener('change', (e)=>{
    if (e.target.files.length>0){
      const name = e.target.files[0].name;
      fileContent.innerHTML = `<div class="file-input-icon">✓</div><div class="file-input-text">${name}</div><div class="file-input-subtext">Click to change image</div>`;
      fileWrapper.style.borderColor='var(--success-color)'; fileWrapper.style.background='rgba(46,213,115,0.1)';
    }
  });

  // ===== Country select scroll (حقيقي فوق وتحت) =====
  const select = document.getElementById('country_code');
  const isoHidden = document.getElementById('country_iso');
  const formContainer = document.getElementById('formContainer');

  function expand(){
    formContainer.classList.add('dropdown-open');     // يسمح بخروج القائمة خارج الكارت
    select.classList.add('expanded');
    select.size = Math.min(10, select.options.length);
    select.setAttribute('aria-expanded','true');
    // ضمان ظهور Scroll للأعلى والأسفل
    select.scrollTop = select.selectedIndex * 32;     // تقريب ارتفاع العنصر
  }
  function collapse(){
    select.classList.remove('expanded');
    select.size = 0; // يرجع dropdown طبيعي
    select.removeAttribute('aria-expanded');
    formContainer.classList.remove('dropdown-open');
  }

  // فتح عند التركيز/النقر
  select.addEventListener('focus', expand);
  select.addEventListener('mousedown', (e)=>{ e.preventDefault(); if(!select.classList.contains('expanded')) expand(); else collapse(); });

  // اختيار عنصر
  select.addEventListener('change', ()=>{
    const iso = select.options[select.selectedIndex]?.dataset?.iso || '';
    if (isoHidden) isoHidden.value = iso;
    // إبقاء العنصر المختار في المنتصف عند إعادة الفتح
    requestAnimationFrame(()=>{ select.scrollTop = Math.max(0, (select.selectedIndex - 3) * 32); });
    collapse();
    select.blur();
  });

  // إغلاق بالنقر خارج
  document.addEventListener('click', (e)=>{ if (!select.contains(e.target)) collapse(); });

  // دعم السحب بعجلة الماوس حتى لو صغير
  select.addEventListener('wheel', (e)=>{ if (select.classList.contains('expanded')) return; expand(); });

  // ===== Location fallback (لو IP ماشتغل) =====
  const isoToDial = @json($dialMap);
  function setCountryByISO(iso){
    iso = (iso||'').toUpperCase();
    if (!iso || !isoToDial[iso]) return false;
    const dial = isoToDial[iso];
    const match = [...select.options].find(o => o.value === dial);
    if (match){ select.value = dial; isoHidden.value = iso; return true; }
    return false;
  }

  // Bounding boxes مختصرة لأكثر المناطق
  const boxes = {
    EG:[22,31.7,24.7,36.9], SA:[16,32.2,34,56.5], AE:[22.6,26.5,51.3,56.7], KW:[28.5,30.2,46.4,48.6], QA:[24.3,26.4,50.6,52.3], OM:[16.5,26.8,52,60],
    JO:[29,33.6,34.9,39.5], IQ:[29,37.6,38.8,48.8], SY:[32,37.5,35,42.4], LB:[33,34.8,35,36.7], PS:[31,32.7,34.2,35.7], YE:[12,19.5,42.5,54],
    MA:[27.5,35.9,-13.5,-1], DZ:[19,37,-9,12], TN:[30,37.6,7.5,11.8], LY:[19.5,33.4,9.4,25.2], SD:[8.5,22.3,21.8,38.6],
    US:[24.5,49.6,-125,-66.5], CA:[42,83,-141,-52], GB:[49.9,59,-8.6,1.8], FR:[41,51.3,-5.5,9.8], DE:[47.2,55.2,5.8,15.1],
    IN:[6.5,35.7,68,97.5], PK:[23.5,37.3,60.8,77.9], BD:[20.5,26.7,88,92.7], CN:[18,53.7,73.5,134.9], JP:[24,45.7,123.6,145.9], KR:[33,38.8,124.5,132],
  };
  const inBox = (lat,lon,box)=>{ if(!box) return false; const [a,b,c,d]=box; return lat>=a && lat<=b && lon>=c && lon<=d; }

  function isoByTimezone(){
    const tz = (Intl.DateTimeFormat().resolvedOptions().timeZone || '');
    const map = { 'Africa/Cairo':'EG','Asia/Riyadh':'SA','Asia/Dubai':'AE','Europe/London':'GB','Europe/Paris':'FR','Europe/Berlin':'DE','Asia/Kolkata':'IN','Asia/Karachi':'PK','Asia/Dhaka':'BD','Asia/Shanghai':'CN','Asia/Tokyo':'JP','Asia/Seoul':'KR','America/New_York':'US','America/Los_Angeles':'US','America/Toronto':'CA' };
    return map[tz] || null;
  }
  function isoByLocale(){
    const langs = navigator.languages && navigator.languages.length ? navigator.languages : [navigator.language].filter(Boolean);
    for (const l of langs){ const p = String(l).replace('_','-').split('-'); if (p.length>=2 && p[1].length===2) return p[1].toUpperCase(); }
    return null;
  }

  // ثبّت الافتراضي القادم من السيرفر
  setCountryByISO((select.dataset.initialIso || '').toUpperCase());

  // Geolocation أولًا
  if ('geolocation' in navigator) {
    navigator.geolocation.getCurrentPosition(
      pos => {
        const { latitude:lat, longitude:lon } = pos.coords || {};
        // أولوية لمصر ثم أي صندوق مطابق
        if (inBox(lat,lon,boxes.EG)) { setCountryByISO('EG'); return; }
        for (const [k,box] of Object.entries(boxes)){ if (inBox(lat,lon,box)) { setCountryByISO(k); return; } }
        // Timezone/Locale
        const tz = isoByTimezone(); if (tz && setCountryByISO(tz)) return;
        const lc = isoByLocale();  if (lc) setCountryByISO(lc);
      },
      _ => { const tz = isoByTimezone(); if (tz && setCountryByISO(tz)) return; const lc = isoByLocale(); if (lc) setCountryByISO(lc); },
      { enableHighAccuracy:false, timeout:3000, maximumAge:600000 }
    );
  }

  // ===== Phone validation =====
  const form = document.getElementById('sponsor-form');
  const phone = document.getElementById('phone');
  const phoneErr = document.getElementById('phone-error');

  form.addEventListener('submit', function(e){
    const iso = (select.options[select.selectedIndex]?.dataset?.iso || 'AE').toUpperCase();
    const dial = select.value;
    const raw = phone.value.trim();
    const clean = raw.replace(/[^\d+]/g,'');
    let ok = false;
    try {
      const candidate = clean.startsWith('+') ? clean : (dial + clean).replace('++','+');
      const parsed = libphonenumber.parsePhoneNumberFromString(candidate, iso);
      if (parsed && parsed.isValid() && (!parsed.country || parsed.country===iso)) ok = true;
    } catch(_) {}

    if (!ok){
      const fb = {'EG':/^0?(10|11|12|15)\d{8}$/,'SA':/^0?5\d{8}$/,'AE':/^0?5\d{8}$/,'OM':/^0?9\d{7}$/,'US':/^\d{10}$/,'GB':/^0?7\d{9}$/,'IN':/^[6-9]\d{9}$/,'PK':/^3\d{9}$/,'KW':/^[569]\d{7}$/,'QA':/^3\d{7}$/,'BH':/^3\d{7}$/};
      const nat = clean.replace(/^\+?\d{1,3}/,'');
      if (fb[iso] && fb[iso].test(nat)) ok = true;
    }

    if (!ok){ e.preventDefault(); phoneErr.classList.remove('d-none'); phone.classList.add('is-invalid'); }
    else { phoneErr.classList.add('d-none'); phone.classList.remove('is-invalid'); }
  });
});
</script>
@endsection
