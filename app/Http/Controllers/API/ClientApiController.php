<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Qrcode as QrcodeModel;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Mail;
use App\Mail\ClientQrMail;
use Illuminate\Support\Facades\Log;
use App\Models\Client;
use App\Models\Form;

class ClientApiController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'name'                  => 'required|string|max:255',
            'email'                 => 'required|email',
            'phone'                 => 'required|string|max:20',
            'job'                   => 'required|string|max:255',
            'country_code'          => 'required|string',
            'company_name'          => 'nullable|string',
            'img'                   => 'nullable|image',
            'type'                  => 'required|in:1,2',
            'do_you_have_experince' => 'required|in:yes,no',
        ]);

        $data['type'] = 1;
        $data['do_you_have_experince'] = $data['do_you_have_experince'] === 'yes' ? 1 : 0;
        $data['status'] = 'pending';

        $latestForm = Form::latest('id')->first();
        if (!$latestForm) {
            return response()->json([
                'message' => 'Form not found.'
            ], 422);
        }

        $data['form_id'] = $latestForm->id;
        $client = Client::create($data);

        $this->generateAndSendQr($client);

        return response()->json([
            'message' => 'Registration successful. We will contact you soon.',
            'client' => $client
        ], 201);
    }
public function resendQrCode($id)
{
    $client = Client::findOrFail($id);

    try {
        // إجبار الإرسال حتى لو كان بريد عام
        $success = $this->generateAndSendQr($client, true);

        if ($success) {
            $client->update(['status' => 'complete']);
            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false, 'message' => 'Failed to send.']);
    } catch (\Exception $e) {
        \Log::error('❌ Send Code Failed: ' . $e->getMessage());
        return response()->json(['success' => false, 'message' => 'Internal Server Error.'], 500);
    }
}
private function generateAndSendQr(Client $client, $forceSend = false): bool
{
    $emailDomain = strtolower(substr(strrchr($client->email, "@"), 1));
    $publicDomains = [
        'gmail.com', 'yahoo.com', 'hotmail.com', 'outlook.com',
        'aol.com', 'icloud.com', 'live.com', 'protonmail.com'
    ];

    if (in_array($emailDomain, $publicDomains) && !$forceSend) {
        return false;
    }

    QrcodeModel::where('register_id', $client->id)->delete();

    $shortCode = Str::random(10);
    $qrUrl     = url('/qr/' . $shortCode);

    $directory = public_path('qrcodes');
    if (!file_exists($directory)) {
        mkdir($directory, 0755, true);
    }

    $fileName   = 'qr_' . time() . '_' . $client->id . '.png';
    $filePath   = $directory . '/' . $fileName;

    // رابط مباشر وثابت
    $qrImageUrl = "https://smartvisionexpo.com/public/qrcodes/{$fileName}";

    \QrCode::format('png')->size(300)->generate($qrUrl, $filePath);

    Mail::to($client->email)->send(new ClientQrMail($client, $qrUrl, $qrImageUrl));

    QrcodeModel::create([
        'qrcode'      => $fileName,
        'active'      => 1,
        'short_code'  => $shortCode,
        'email'       => $client->email,
        'scan'        => 0,
        'register_id' => $client->id,
    ]);

    return true;
}


}
