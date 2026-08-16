@php
    use App\Models\Setting;
    use Illuminate\Support\Str;

    $locale = app()->getLocale();

    // جِب عمود floor_plan فقط
    $settingsRow = Setting::query()->select('floor_plan')->first();
    $rawFloor = $settingsRow->floor_plan ?? null;

    // حوّل القيمة إلى Array موحّد
    $data = null;
    if (is_string($rawFloor)) {
        $decoded = json_decode($rawFloor, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            $data = $decoded;
        } else {
            // قيمة نصية مباشرة (مسار صورة)
            $data = ['media_path' => $rawFloor];
        }
    } elseif (is_array($rawFloor)) {
        $data = $rawFloor;
    } elseif (is_object($rawFloor)) {
        $data = (array) $rawFloor;
    }

    // لو Array بقائمة عناصر، خُد أول عنصر
    $first = null;
    if (is_array($data)) {
        $isAssoc = array_keys($data) !== range(0, count($data) - 1);
        $first   = $isAssoc ? $data : ($data[0] ?? null);
    }

    // العنوان
    $title = data_get($first, "title.$locale")
        ?? data_get($first, "title_$locale")
        ?? data_get($first, 'title');

    // مسار الصورة (يدعم مفاتيح متعددة أو قيمة نصية مباشرة)
    $rawImage = data_get($first, 'media_path')
        ?? data_get($first, 'image')
        ?? data_get($first, 'img')
        ?? data_get($first, 'path')
        ?? data_get($first, 'url')
        ?? (is_string($settingsRow->floor_plan ?? null) ? ($settingsRow->floor_plan) : null);

    // حضّر رابط الصورة (نعرض فقط لو صالح)
    $imageUrl = null;
    if (!empty($rawImage)) {
        if (Str::startsWith($rawImage, ['http://','https://'])) {
            $imageUrl = $rawImage;
        } else {
            $relative = ltrim($rawImage, '/');
            // تأكد أن الملف موجود فعليًا لتفادي كسر الصورة
            if (file_exists(public_path($relative))) {
                $imageUrl = asset('public/' . $relative);
            }
        }
    }

    $showSection = filled($imageUrl); // اعرض فقط لو فيه صورة صالحة
@endphp

@if($showSection)
<section id="home-promo" class="overflow-hidden" style="margin-top:5px; background:#fff; position:relative;">
    <div class="container text-center py-3">
            <h2
                class="promo-title"
                style="
                    font-size:60px; font-weight:900; line-height:1.2; margin-bottom:0px;
                    background:linear-gradient(270deg, #000000, #E73701, #000000);
                    background-size:600% 600%;
                    -webkit-background-clip:text; -webkit-text-fill-color:transparent;
                    animation:gradientShift 5s ease infinite;
                "
            >
            Floor Plan
            </h2>
    </div>

    <style>
        @keyframes gradientShift {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
        @media (max-width: 768px){
            #home-promo .promo-title{ font-size:38px; }
        }
    </style>

    <img
        src="{{ $imageUrl }}"
        alt="Floor Plan"
        style="width:100%; height:100%; object-fit:cover; display:block;"
    />

    <style>
        @media (max-width: 768px) {
            #home-promo img { height:50vh; }
        }
    </style>
</section>
@endif
