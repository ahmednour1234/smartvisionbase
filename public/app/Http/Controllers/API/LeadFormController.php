<?php
// app/Http/Controllers/Api/LeadFormController.php
namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Lead;

class LeadFormController extends Controller
{
public function handle(Request $request)
{
    $VERIFY_TOKEN = 'Ahmed@8888';

    if ($request->isMethod('get')) {
        \Log::info('✅ Incoming GET', $request->query());

        if (
            $request->query('hub_mode') == 'subscribe' &&
            $request->query('hub_verify_token') == $VERIFY_TOKEN
        ) {
            return response($request->query('hub_challenge'), 200);
        }

        return response('❌ التحقق فشل', 403);
    }

    if ($request->isMethod('post')) {
        \Log::info('📥 Webhook Received:', $request->all());

        Lead::create([
            'name' => 'Lead #' . now()->timestamp,
            'phone' => 'غير معروف',
            'email' => null,
            'campaign' => json_encode($request->all()),
        ]);

        return response()->json(['message' => 'تم الاستلام بنجاح'], 200);
    }

    return response('Not Allowed', 405);
}


}
