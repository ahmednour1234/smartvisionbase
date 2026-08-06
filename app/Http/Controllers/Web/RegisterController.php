<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Mail\ClientQrMail;
use App\Models\Client;
use App\Models\Form;
use App\Models\HomeSection;
use App\Models\Qrcode as QrcodeModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

class RegisterController extends Controller
{
    /** Hudur API */
    private const HUDUR_URL = 'https://www.hudur.net/WebSiteApi/regData.php';
    private const HUDUR_EVENT_CODE = 'NJlCzusy3ZOsKzPbmz+Fbw==';

    public function index()
    {
        $section = HomeSection::find(10);

        if ($section && (int) $section->is_active === 1) {
            return view('web.content.register');
        }

        return view('web.content.closed')
            ->with('danger', 'التسجيل مقفول حاليًا.');
    }

    public function becomesponsor()
    {
        $section = HomeSection::find(11);

        if ($section && (int) $section->is_active === 1) {
            return view('web.content.becomesponsor');
        }

        return view('web.content.closesponsor')
            ->with('danger', 'التسجيل مقفول حاليًا.');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'         => 'required|string|max:255',
            'email'        => 'required|email',
            'phone'        => 'required|string|max:100',
            'job'          => 'required|string|max:255',
            'country_code' => 'required|string|max:10',
            'company_name' => 'nullable|string|max:255',
            'img'          => 'nullable|image|max:5120',
            'type'         => 'required|in:1,2', // 1: Email QR, 2: SMS
        ]);

        $latestForm = Form::latest('id')->first();
        if (! $latestForm) {
            return back()->withErrors(['form_id' => 'لا يوجد نموذج form حالياً.'])->withInput();
        }
        $data['form_id'] = $latestForm->id;

        // (اختياري) تطبيع الإيميل
        $data['email'] = Str::lower(trim($data['email']));

        // إنشاء العميل
        $client = Client::create($data);

        // لو فيه صورة
        if ($request->hasFile('img')) {
            $path = $request->file('img')->store('clients', 'public');
            $client->update(['img' => $path]);
        }

        /**
         * ✅ 1) Hudur send (بعد ما نسجل العميل مباشرة) — مطلوب "نفس الكلام"
         * لو مش عايزها تتكرر مرتين قولّي وهخليها مرة واحدة بس.
         */
      

        if ((int) $data['type'] === 2) {
            // ====== SMS للإدارة (كما هو) ======
            try {
                $url  = "https://app.community-ads.com/SendSMSAPI/api/SMSSender/SendSMS";
                $uuid = Str::uuid()->toString();

                $smsText  = "New Smart Vision Morocco\n";
                $smsText .= "Name: {$data['name']}\n";
                $smsText .= "Email: {$data['email']}\n";
                $smsText .= "Phone: {$data['country_code']}{$data['phone']}\n";
                $smsText .= "Source: https://morocco.smartvisionsummit.com/\n";
                $smsText .= "Position: {$data['job']}\n";
                $smsText .= "Company: {$data['company_name']}";

                $postData = [
                    "UserName"     => "Smartvision2",
                    "Password"     => "=-bZ%Jp_UI",
                    "SMSText"      => $smsText,
                    "SMSLang"      => "e",
                    "SMSSender"    => "SmartVision",
                    "SMSReceiver"  => "01224984005",
                    "SMSID"        => $uuid,
                ];

                $ch = curl_init();
                curl_setopt_array($ch, [
                    CURLOPT_URL            => $url,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_POST           => true,
                    CURLOPT_HTTPHEADER     => ["Content-Type: application/json"],
                    CURLOPT_POSTFIELDS     => json_encode($postData),
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

     return redirect()
    ->route('web.thank.you')
    ->with([
                'success'  => 'Thanks for your sponsorship request. Your request is pending review.',
            ]);
            }else{

        // ====== type = 1 → توليد QR وإرسال إيميل ======
        try {
            // 1) short_code فريد
            do {
                $shortCode = Str::upper(Str::random(10));
            } while (QrcodeModel::where('short_code', $shortCode)->exists());

            $qrUrl = url('/qr/' . $shortCode);

            // 2) توليد صورة QR وحفظها
            $fileName = 'qr_' . $client->id . '_' . time() . '.png';
            $path     = 'qrcodes/' . $fileName;

            $png = \QrCode::format('png')
                ->size(300)
                ->errorCorrection('H')
                ->generate($qrUrl);

            Storage::disk('public')->put($path, $png);

            // ✅ ده كان غلط عندك (asset('storage/app/public/...')) — الصحيح:
            $qrImageUrl = asset('storage/' . $path);

            // 3) إرسال الإيميل (مع فولباك تلقائي)
            $context = [
                'client_id'   => $client->id,
                'to'          => $client->email,
                'from'        => config('mail.from.address'),
                'subject'     => 'Your Invitation QR',
                'short_code'  => $shortCode,
                'qr_url'      => $qrUrl,
                'qr_image'    => $qrImageUrl,
            ];


            Log::info('✅ Email sent successfully', array_merge($context, $this->currentSmtpDiag()));

            /**
             * ✅ 2) Hudur send (وانت بتبعتله الايميل ابعتله ده كمان)
             * هنكرر الإرسال هنا برضه زي طلبك.
             */
            try {
            $this->sendToHudur($client);
            } catch (\Throwable $e) {
                Log::error('Hudur send failed (after email success): ' . $e->getMessage(), [
                    'client_id' => $client->id,
                    'email'     => $client->email,
                ]);
            }

            // 4) تخزين سجل الـ QR بعد نجاح الإرسال
            QrcodeModel::create([
                'qrcode'      => $path,       // داخل storage/public
                'active'      => 1,
                'short_code'  => $shortCode,
                'email'       => $client->email,
                'scan'        => 0,
                'register_id' => $client->id,
            ]);

     return redirect()
            ->route('web.thank.you')
            ->with([
                'success'  => 'Thanks for your Registraion request.',
            ]);
            } catch (\Throwable $e) {
            $smtpExtra = [];
            if ($e instanceof TransportExceptionInterface && method_exists($e, 'getDebug')) {
                $smtpExtra['smtp_debug'] = $e->getDebug();
            }

            Log::error('❌ Email/SMTP send failed', array_merge([
                'exception' => get_class($e),
                'code'      => (int) $e->getCode(),
                'message'   => $e->getMessage(),
                'trace'     => $e->getTraceAsString(),
            ], $this->currentSmtpDiag(), $smtpExtra));

            return back()->with('warning', ' Registration successful.');
        }
            }
    }

    /**
     * يحاول الإرسال بالإعدادات الحالية، ثم فولباك:
     * 1) 587 + tls
     * 2) 465 + ssl
     */
    protected function sendClientQrWithFallback(Client $client, string $qrUrl, string $qrImageUrl, array $context): void
    {
        // المحاولة 0: استخدم الإعدادات الحالية كما هي
        try {
            Mail::to($client->email)->send(
                (new ClientQrMail($client, $qrUrl, $qrImageUrl))
                    ->from(config('mail.from.address'), config('mail.from.name'))
                    ->subject('Your Invitation QR')
            );
            return; // نجاح
        } catch (TransportExceptionInterface $e) {
            Log::warning('SMTP attempt #0 failed (current config): ' . $e->getMessage(), $this->currentSmtpDiag());
        }

        $orig = config('mail.mailers.smtp') ?: [];

        // المحاولة 1: 587 + tls
        try {
            $tls = array_merge($orig, [
                'port'       => 587,
                'encryption' => 'tls',
            ]);
            config(['mail.mailers.smtp' => $tls]);

            Mail::mailer('smtp')->to($client->email)->send(
                (new ClientQrMail($client, $qrUrl, $qrImageUrl))
                    ->from(config('mail.from.address'), config('mail.from.name'))
                    ->subject('Your Invitation QR')
            );

            Log::info('SMTP attempt #1 (587/tls) succeeded.');
            return;
        } catch (TransportExceptionInterface $e) {
            Log::warning('SMTP attempt #1 (587/tls) failed: ' . $e->getMessage());
        } finally {
            config(['mail.mailers.smtp' => $orig]);
        }

        // المحاولة 2: 465 + ssl
        try {
            $ssl = array_merge($orig, [
                'port'       => 465,
                'encryption' => 'ssl',
            ]);
            config(['mail.mailers.smtp' => $ssl]);

            Mail::mailer('smtp')->to($client->email)->send(
                (new ClientQrMail($client, $qrUrl, $qrImageUrl))
                    ->from(config('mail.from.address'), config('mail.from.name'))
                    ->subject('Your Invitation QR')
            );

            Log::info('SMTP attempt #2 (465/ssl) succeeded.');
            return;
        } catch (TransportExceptionInterface $e) {
            Log::warning('SMTP attempt #2 (465/ssl) failed: ' . $e->getMessage());
            throw $e;
        } finally {
            config(['mail.mailers.smtp' => $orig]);
        }
    }

    protected function currentSmtpDiag(): array
    {
        $defaultMailer = config('mail.default');
        $mailerCfg = config("mail.mailers.$defaultMailer") ?? [];

        return [
            'mailer'     => $defaultMailer,
            'host'       => $mailerCfg['host'] ?? null,
            'port'       => $mailerCfg['port'] ?? null,
            'encryption' => $mailerCfg['encryption'] ?? null,
            'username'   => $mailerCfg['username'] ?? null,
        ];
    }

    /**
     * ✅ إرسال بيانات العميل لـ Hudur API (form-data)
     */
    protected function sendToHudur(Client $client): void
    {
        $mobile = trim(($client->country_code ?? '') . ($client->phone ?? ''));

        $payload = [
            'eventCode' => self::HUDUR_EVENT_CODE,
            'name'      => (string) $client->name,
            'mobile'    => (string) $mobile,
            'email'     => (string) $client->email,
            'job'       => (string) $client->job,
        ];

        $response = Http::asForm()
            ->timeout(20)
            ->post(self::HUDUR_URL, $payload);

        if (! $response->successful()) {
            Log::warning('Hudur API failed', [
                'status'   => $response->status(),
                'body'     => $response->body(),
                'payload'  => $payload,
                'clientId' => $client->id,
            ]);
            return;
        }

        Log::info('Hudur API success', [
            'clientId' => $client->id,
            'email'    => $client->email,
            'resp'     => $response->json(),
        ]);
    }
    
 public function thankYou(Request $request)
    {
        $next = $request->session()->pull('next_url', null);

        return view('web.thank-you', ['nextUrl' => route('web.home')]);
    }
}
