<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Models\Qrcode as QrcodeModel;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Mail;
use App\Mail\ClientQrMail;
use App\Models\Form;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Storage;

class RegisterController extends Controller
{
    public function index()
    {
        return view('web.content.register');
    }

    public function becomesponsor()
    {
        return view('web.content.becomesponsor');
    }

    /**
     * Store registration with strong anti-spam / rate limits (no reCAPTCHA).
     */
    public function store(Request $request)
    {
        // ================= Settings =================
        $MIN_INTERVAL_SECONDS = 120; // لازم 30 ثانية بين كل إرسال والتاني (IP و Email+Phone)
        $PER_IP_LIMIT         = 20; // محاولات مسموحة لكل ساعة لنفس الـ IP
        $PER_IP_DECAY         = 60 * 60; // 1 ساعة
        $DAILY_IP_CAP         = 80; // حد أقصى يومي للمحاولات/التسجيلات من نفس الـ IP

        // لو عايز تمنع نفس الشخص "نهائيًا" من التكرار:
        $BLOCK_DUPLICATE_FOREVER = true; // true = لا يسمح بنفس Email/Phone مرّة أخرى أبداً

        // ================= Basic keys =================
        $ip     = $request->ip();
        $email  = strtolower((string) $request->input('email', ''));
        $phoneC = (string) $request->input('country_code', '');
        $phone  = (string) $request->input('phone', '');
        $phoneKey = preg_replace('/\D+/', '', $phoneC . $phone);

        // honeypot (اختياري): أضف input مخفي باسم website في الفورم
        if (trim((string) $request->input('website')) !== '') {
            return back()->withErrors(['spam' => 'Invalid submission.'])->withInput();
        }

        // مفاتيح الـ rate limiter
        $keyIp        = 'register|ip|' . md5($ip);
        $keyIpLast    = 'register:lastAt:ip:' . sha1($ip);
        $keyUserLast  = 'register:lastAt:user:' . sha1($email . '|' . $phoneKey);
        $keyDailyIp   = 'register:daily:' . sha1($ip) . ':' . now()->format('Ymd');

        // ============== Global / IP protections ==============

        // 0) حد يومي IP
        $dailyCount = cache()->get($keyDailyIp, 0);
        if ($dailyCount >= $DAILY_IP_CAP) {
            Log::warning("Register blocked: daily IP cap ($DAILY_IP_CAP) reached for $ip");
            return back()->withErrors([
                'throttle' => 'تم تجاوز الحد اليومي من هذا الجهاز. حاول غدًا.'
            ])->withInput();
        }

        // 1) فاصل 30 ثانية لنفس IP
        $lastIpTs = (int) cache()->get($keyIpLast, 0);
        if ($lastIpTs && (time() - $lastIpTs) < $MIN_INTERVAL_SECONDS) {
            $wait = $MIN_INTERVAL_SECONDS - (time() - $lastIpTs);
            return back()->withErrors([
                'throttle' => "من فضلك انتظر {$wait} ثانية قبل إعادة الإرسال."
            ])->withInput();
        }

        // 2) Rate limit لكل IP (ساعي)
        if (RateLimiter::tooManyAttempts($keyIp, $PER_IP_LIMIT)) {
            $seconds = RateLimiter::availableIn($keyIp);
            Log::warning("Register blocked by IP throttle: $ip");
            return back()->withErrors([
                'throttle' => "تم استقبال عدد كبير من المحاولات من جهازك. أعد المحاولة بعد " . gmdate("H:i:s", $seconds) . "."
            ])->withInput();
        }

        // هنضرب العدّاد (محاولة) من بدري
        RateLimiter::hit($keyIp, $PER_IP_DECAY);

        // ============== Validate Inputs ==============
        $data = $request->validate([
            'name'          => 'required|string|max:255',
            'email'         => 'required|email|max:255',
            'phone'         => 'required|string|max:50',
            'job'           => 'required|string|max:255',
            'country_code'  => 'required|string|max:10',
            'company_name'  => 'nullable|string|max:255',
            'img'           => 'nullable|image',
            'type'          => 'required|in:1,2',
        ]);

        // ============== Per-User (Email+Phone) 30s gate ==============
        $lastUserTs = (int) cache()->get($keyUserLast, 0);
        if ($lastUserTs && (time() - $lastUserTs) < $MIN_INTERVAL_SECONDS) {
            $wait = $MIN_INTERVAL_SECONDS - (time() - $lastUserTs);
            return back()->withErrors([
                'throttle' => "من فضلك انتظر {$wait} ثانية قبل إعادة الإرسال."
            ])->withInput();
        }

        // ============== Duplicate user blocking ==============
        $existsEmailAny = Client::where('email', $data['email'])->exists();
        $existsPhoneAny = Client::where('country_code', $data['country_code'])
            ->where('phone', $data['phone'])
            ->exists();

        if ($BLOCK_DUPLICATE_FOREVER) {
            if ($existsEmailAny || $existsPhoneAny) {
                Log::info("Duplicate blocked permanently for {$data['email']} / {$data['country_code']}{$data['phone']} from IP $ip");
                // عداد يومي للمحاولة
                $this->bumpDaily($keyDailyIp);
                // ستور آخر وقت
                cache()->put($keyIpLast, time(), now()->addHours(12));
                cache()->put($keyUserLast, time(), now()->addHours(12));
                return back()->withErrors([
                    'duplicate' => 'تم التسجيل بهذه البيانات من قبل. إذا كانت هناك مشكلة تواصل معنا.'
                ])->withInput();
            }
        }
        // لو عايز تبقى 24 ساعة بس، بدّل الشرط بفحص created_at خلال نافذة زمنية.

        // ============== Latest form ==============
        $latestForm = Form::latest('id')->first();
        if (!$latestForm) {
            $this->bumpDaily($keyDailyIp);
            cache()->put($keyIpLast, time(), now()->addHours(12));
            cache()->put($keyUserLast, time(), now()->addHours(12));
            return back()->withErrors(['form_id' => 'لا يوجد نموذج form حالياً.'])->withInput();
        }
        $data['form_id'] = $latestForm->id;

        // ============== Upload image (optional) ==============
        if ($request->hasFile('img')) {
            try {
                $file = $request->file('img');
                $data['img'] = $file->store('qrcodes/clients', 'public'); // storage/app/public/qrcodes/clients
            } catch (\Exception $e) {
                Log::error("Image upload error: " . $e->getMessage());
            }
        }

        // ============== Create client ==============
        try {
            $client = Client::create($data);
        } catch (\Exception $e) {
            Log::error("Error creating client: " . $e->getMessage());
            $this->bumpDaily($keyDailyIp);
            cache()->put($keyIpLast, time(), now()->addHours(12));
            cache()->put($keyUserLast, time(), now()->addHours(12));
            return back()->withErrors(['server' => 'حدث خطأ أثناء حفظ البيانات. حاول لاحقًا.'])->withInput();
        }

        // بعد نجاح الإنشاء: إحكام الفواصل 30 ثانية
        $this->bumpDaily($keyDailyIp);
        cache()->put($keyIpLast, time(), now()->endOfDay());
        cache()->put($keyUserLast, time(), now()->addMinutes(10)); // تحفظ 30 ثانية على الأقل (وفر وقت أطول لو حابب)

        // ============== SMS (type = 2) ==============
        if ((int)$data['type'] === 2) {
            try {
                $url  = "https://app.community-ads.com/SendSMSAPI/api/SMSSender/SendSMS";
                $uuid = Str::uuid()->toString();

                $smsText  = "New Top Trusted Fxbrokers Sponsor\n";
                $smsText .= "Name: {$data['name']}\n";
                $smsText .= "Email: {$data['email']}\n";
                $smsText .= "Phone: {$data['country_code']}{$data['phone']}\n";
                $smsText .= "Source: https://toptrustedfxbrokers.com/public/\n";
                $smsText .= "Position: {$data['job']}\n";
                $smsText .= "Company: " . ($data['company_name'] ?? '');

                $postData = [
                    "UserName"     => "Smartvision2",
                    "Password"     => "=-bZ%Jp_UI",
                    "SMSText"      => $smsText,
                    "SMSLang"      => "e",
                    "SMSSender"    => "SmartVision",
                    "SMSReceiver"  => "01224984005",
                    "SMSID"        => $uuid,
                ];

                $response = Http::withHeaders(['Content-Type' => 'application/json'])
                    ->post($url, $postData);

                Log::info("SMS API response: " . $response->body());
            } catch (\Exception $e) {
                Log::error("Exception while sending SMS: " . $e->getMessage());
            }
        }

        // ============== Generate QR + send email (type 1 or 2) ==============
        if (in_array((int)$data['type'], [1, 2], true)) {
            try {
                // short_code فريد
                do {
                    $shortCode = Str::random(10);
                } while (QrcodeModel::where('short_code', $shortCode)->exists());

                $qrUrl = url('/qr/' . $shortCode);

                // نحفظ الصورة في storage/app/public/qrcodes
                $fileName     = 'qr_' . time() . '_' . $client->id . '.png';
                $relativePath = 'qrcodes/' . $fileName;
                $fullPath     = storage_path('app/public/' . $relativePath);

                if (!is_dir(dirname($fullPath))) {
                    mkdir(dirname($fullPath), 0755, true);
                }

                QrCode::format('png')->size(300)->generate($qrUrl, $fullPath);
                $qrImageUrl = asset('storage/' . $relativePath); // تأكد من عمل storage:link

                Mail::to($client->email)->send(new ClientQrMail(
                    $client,
                    $qrUrl,
                    $qrImageUrl,
                    1,   // event ثابت
                    'en' // لغة الميل
                ));

                QrcodeModel::create([
                    'qrcode'      => $fileName,
                    'active'      => 1,
                    'short_code'  => $shortCode,
                    'email'       => $client->email,
                    'scan'        => 0,
                    'register_id' => $client->id,
                ]);
            } catch (\Exception $e) {
                Log::error("Error while sending email or saving QR: " . $e->getMessage());
            }
        }

        return back()->with('success', 'thank you for submitting your sponsorship request.');
    }

    /**
     * Increment the IP daily counter and set expiry to end of day if new.
     */
    private function bumpDaily(string $keyDailyIp): void
    {
        if (!cache()->has($keyDailyIp)) {
            cache()->put($keyDailyIp, 1, now()->endOfDay());
        } else {
            cache()->increment($keyDailyIp);
        }
    }
}
