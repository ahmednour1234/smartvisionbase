<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Mail\ClientQrMail;
use App\Models\Client;
use App\Models\Form;
use App\Models\HomeSection;
use App\Models\Qrcode as QrcodeModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class RegisterController extends Controller
{
    /** Hudur API */
    private const HUDUR_URL = 'https://www.hudur.net/WebSiteApi/regData.php';
    private const HUDUR_EVENT_CODE = 'Z7hblo5iUczXGpaDEaEsAA==';

    public function index()
    {
        $section = HomeSection::find(11);

        if ($section && (int) $section->is_active === 1) {
            return view('web.content.register');
        }

        return view('web.content.closed')->with('danger', 'التسجيل مقفول حاليًا.');
    }

    public function becomesponsor()
    {
        $section = HomeSection::find(12);

        if ($section && (int) $section->is_active === 1) {
            return view('web.content.becomesponsor');
        }

        return view('web.content.closesponsor')->with('danger', 'التسجيل مقفول حاليًا.');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'         => ['required', 'string', 'max:255'],
            'email'        => ['required', 'email', 'max:255'],
            'phone'        => ['required', 'string', 'max:100'],
            'job'          => ['required', 'string', 'max:255'],
            'country_code' => ['required', 'string', 'max:10'],
            'company_name' => ['nullable', 'string', 'max:255'],
            'img'          => ['nullable', 'image', 'max:5120'],
            'type'         => ['required', 'in:1,2'], // 1: normal flow, 2: SMS notify admin only
        ]);

        $data['email'] = Str::lower(trim($data['email']));
        $data['phone'] = trim($data['phone']);
        $data['country_code'] = trim($data['country_code']);

        $latestForm = Form::latest('id')->first();
        if (! $latestForm) {
            return back()->withErrors(['form_id' => 'لا يوجد نموذج form حالياً.'])->withInput();
        }

        $data['form_id'] = $latestForm->id;

        // Create client (Pending by default in DB)
        $client = Client::create($data);

        if ($request->hasFile('img')) {
            $path = $request->file('img')->store('clients', 'public');
            $client->update(['img' => $path]);
        }

        // type=2 -> SMS للإدارة فقط
        if ((int) $data['type'] === 2) {
            $this->notifyAdminSms($data);

     return redirect()
            ->route('web.thank.you')
            ->with([
                'success'  => 'Thanks for your  request.',
            ]);
            }
else{
  
if ((int) $data['type'] === 1) {
        try {
            // 1) Hudur
            $this->sendToHudur($client);

            // 2) QR + Email
            $this->generateAndSendQrCode($client);

            // (لو عندك status field وتحب تقلبه Approved هنا اعمل update)
            // $client->update(['status' => 'approved']);

            return redirect()
            ->route('web.thank.you')
            ->with([
                'success'  => 'Thanks for request.',
            ]);
        } catch (\Throwable $e) {
            Log::error('Hudur/QR flow failed: '.$e->getMessage(), [
                'client_id' => $client->id,
                'email' => $client->email,
            ]);
     return redirect()
            ->route('web.thank.you')
            ->with([
                'success'  => 'Thanks for your  request..',
            ]);
        }
}
}
    }

    public function thankYou(Request $request)
    {
        $next = $request->session()->pull('next_url', null);

        return view('web.thank-you', ['nextUrl' => route('web.home')]);
    }

    /* ================= Helpers ================= */

    protected function isOfficialEmail(string $email): bool
    {
        $domain = Str::lower(Str::after($email, '@'));

        $free = [
            'gmail.com','yahoo.com','hotmail.com','outlook.com','live.com','msn.com','aol.com',
            'icloud.com','proton.me','protonmail.com','yandex.com','gmx.com','zoho.com','mail.com'
        ];

        return $domain !== '' && ! in_array($domain, $free, true);
    }

    /**
     * Normalize phone to E.164-like: +<countrycode><national_number>
     * - Removes +, spaces, -, (), etc.
     * - If phone already starts with country code digits, do NOT double prefix
     * - Removes leading zeros from national number
     */
    protected function normalizeMobile(?string $countryCode, ?string $phone): string
    {
        $cc = preg_replace('/\D+/', '', (string) $countryCode);
        $p  = preg_replace('/\D+/', '', (string) $phone);

        // if phone starts with 00 -> international format
        if (str_starts_with($p, '00')) {
            $p = substr($p, 2);
        }

        // If phone already starts with cc digits, avoid double prefix
        if ($cc !== '' && str_starts_with($p, $cc)) {
            return '+' . $p;
        }

        // remove leading zeros (e.g. Egypt 01xxxxxxxxx)
        $p = ltrim($p, '0');

        if ($cc === '') {
            return '+' . $p;
        }

        return '+' . $cc . $p;
    }

    /**
     * Send client data to Hudur (form-data)
     */
    protected function sendToHudur(Client $client): void
    {
        $mobile = $this->normalizeMobile($client->country_code, $client->phone);

        $payload = [
            'eventCode' => self::HUDUR_EVENT_CODE,
            'name'      => (string) $client->name,
            'mobile'    => (string) $mobile, // لو Hudur عايزه بدون +: ltrim($mobile,'+')
            'email'     => (string) $client->email,
            'job'       => (string) $client->job,
        ];

        $response = Http::asForm()
            ->timeout(20)
            ->post(self::HUDUR_URL, $payload);

        if (! $response->successful()) {
            Log::warning('Hudur API failed', [
                'status' => $response->status(),
                'body'   => $response->body(),
                'email'  => $client->email,
                'mobile' => $mobile,
            ]);

            // لو تحب تعتبره failure وترمي Exception:
            // throw new \RuntimeException('Hudur API failed: '.$response->status());
            return;
        }

        Log::info('Hudur API success', [
            'email'  => $client->email,
            'mobile' => $mobile,
            'resp'   => $response->json(),
        ]);
    }

    /**
     * Generate QR, store it, create qrcode row, and send email with QR
     */
    protected function generateAndSendQrCode(Client $client): void
    {
        do {
            $shortCode = Str::upper(Str::random(10));
        } while (QrcodeModel::where('short_code', $shortCode)->exists());

        $qrUrl = url('/qr/' . $shortCode);

        File::ensureDirectoryExists(public_path('qrcode'));

        $fileName     = 'qr_' . $client->id . '_' . time() . '.png';
        $relativePath = '/qrcode/' . $fileName;
        $absolutePath = public_path($relativePath);

        $png = QrCode::format('png')
            ->size(300)
            ->errorCorrection('H')
            ->generate($qrUrl);

        File::put($absolutePath, $png);

        QrcodeModel::create([
            'qrcode'      => $relativePath,
            'active'      => 1,
            'short_code'  => $shortCode,
            'email'       => $client->email,
            'scan'        => 0,
            'register_id' => $client->id,
        ]);

        // Send Email (ClientQrMail لازم يكون بيستقبل $client و $relativePath أو $qrUrl حسب تصميمك)
        try {
        } catch (\Throwable $e) {
            Log::error('QR email send failed: '.$e->getMessage(), [
                'client_id' => $client->id,
                'email' => $client->email,
            ]);
            // لو عايز توقف الفلو هنا throw
            // throw $e;
        }
    }

    protected function notifyAdminSms(array $data): void
    {
        try {
            $url  = "https://app.community-ads.com/SendSMSAPI/api/SMSSender/SendSMS";
            $uuid = Str::uuid()->toString();

            $smsText  = "New Smart Vision Summit Dubai Sponsor\n";
            $smsText .= "Name: {$data['name']}\n";
            $smsText .= "Email: {$data['email']}\n";
            $smsText .= "Phone: {$data['country_code']}{$data['phone']}\n";
            $smsText .= "Source: https://forextraderssummit.com/\n";
            $smsText .= "Position: {$data['job']}\n";
            $smsText .= "Company: " . ($data['company_name'] ?? '-');

            $postData = [
                "UserName"    => "Smartvision2",
                "Password"    => "=-bZ%Jp_UI",
                "SMSText"     => $smsText,
                "SMSLang"     => "e",
                "SMSSender"   => "SmartVision",
                "SMSReceiver" => "01224984005",
                "SMSID"       => $uuid,
            ];

            $ch = curl_init();
            curl_setopt_array($ch, [
                CURLOPT_URL            => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST           => true,
                CURLOPT_HTTPHEADER     => ["Content-Type: application/json"],
                CURLOPT_POSTFIELDS     => json_encode($postData, JSON_UNESCAPED_UNICODE),
                CURLOPT_TIMEOUT        => 20,
            ]);

            $response = curl_exec($ch);
            $error    = curl_error($ch);
            curl_close($ch);

            if ($error) {
                Log::error("cURL SMS Error: $error");
            } else {
                Log::info("SMS sent. Response: $response");
            }
        } catch (\Throwable $e) {
            Log::error("SMS Exception: " . $e->getMessage());
        }
    }
}
