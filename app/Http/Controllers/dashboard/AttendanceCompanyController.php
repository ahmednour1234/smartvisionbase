<?php
// app/Http/Controllers/Dashboard/AttendanceCompanyController.php
namespace App\Http\Controllers\dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\AttendanceCompany\StoreRequest;
use App\Http\Requests\AttendanceCompany\UpdateRequest;
use App\Models\AttendanceCompany;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\AttendanceCompanyExport;
use App\Imports\AttendanceCompanyImport;
use App\Exports\AttendanceCompanyTemplateExport;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class AttendanceCompanyController extends Controller
{
    /** قائمة + فلاتر بسيطة */
    public function index(Request $request)
    {
        $q = AttendanceCompany::query()
            ->when($request->filled('search'), fn($qq) =>
                $qq->where('name', 'like', '%'.$request->string('search')->trim().'%'))
            ->when($request->filled('active'), fn($qq) =>
                $qq->where('active', (bool)$request->boolean('active')))
            ->when($request->filled('attendance'), fn($qq) =>
                $qq->where('attendance', (bool)$request->boolean('attendance')))
            ->orderByDesc('id');

        $items = $q->paginate(20)->withQueryString();
        return view('content.attendance_company.index', compact('items'));
    }

    public function create()
    {
        return view('content.attendance_company.create');
    }

    public function store(StoreRequest $request)
    {
        AttendanceCompany::create($request->validated());
        return redirect()->route('dashboard.attendance_company.index')->with('success', 'تم الإنشاء بنجاح');
    }

    public function edit(AttendanceCompany $attendance_company)
    {
        return view('content.attendance_company.edit', ['item' => $attendance_company]);
    }

    public function update(UpdateRequest $request, AttendanceCompany $attendance_company)
    {
        $attendance_company->update($request->validated());
        return redirect()->route('dashboard.attendance_company.index')->with('success', 'تم التحديث بنجاح');
    }

    public function destroy(AttendanceCompany $attendance_company)
    {
        $attendance_company->delete();
        return back()->with('success', 'تم الحذف بنجاح');
    }

    /** تفعيل/تعطيل سريع */
    public function toggleActive(AttendanceCompany $attendance_company)
    {
        $attendance_company->active = ! $attendance_company->active;
        $attendance_company->save();

        return back()->with('success', 'تم تحديث حالة التفعيل');
    }

public function markAttendance(AttendanceCompany $attendance_company, Request $request)
    {
        $at = $request->filled('attendance_at')
            ? Carbon::parse($request->attendance_at)
            : now();

        $attendance_company->attendance    = true;
        $attendance_company->attendance_at = $at;
        $attendance_company->save();

        // رسالة حضور مختصرة بالاسم فقط
        $name = trim((string)($attendance_company->name ?? ''));
        if ($name === '') {
            $name = "ID#{$attendance_company->id}";
        }

        $msg = "Top trusted Fxbrokers - Attendance\n"
             . "Name: {$name}\n"
             . "Status: attended";

        $this->sendSmsToAdmins($msg);

        return back()->with('success', 'تم تسجيل الحضور');
    }

    /** إلغاء/تصحيح الحضور (إرسال رسالة اعتذار أن الشخص لم يحضر) */
    public function unmarkAttendance(AttendanceCompany $attendance_company)
    {
        $attendance_company->attendance    = false;
        $attendance_company->attendance_at = null;
        $attendance_company->save();

        // رسالة تصحيح مختصرة بالاسم فقط
        $name = trim((string)($attendance_company->name ?? ''));
        if ($name === '') {
            $name = "ID#{$attendance_company->id}";
        }

        $msg = "Top trusted Fxbrokers - Correction\n"
             . "Name: {$name}\n"
             . "Status: not attended (previous message sent by mistake)";

        $this->sendSmsToAdmins($msg);

        return back()->with('success', 'تم إلغاء الحضور');
    }

    /**
     * إرسال SMS لنمرتين محددتين برسالة واحدة (نبعت لكل رقم طلب مستقل).
     * لو API بتقبل عدة أرقام مع بعض، ممكن تعدّل بسهولة.
     */
    private function sendSmsToAdmins(string $smsText): void
    {
        $url   = "https://app.community-ads.com/SendSMSAPI/api/SMSSender/SendSMS";
        $user  = "Smartvision2";     // يفضّل نقلها للـ .env
        $pass  = "=-bZ%Jp_UI";       // يفضّل نقلها للـ .env
        $from  = "SmartVision";
        $lang  = "e"; // English
        $to    = ['201115767168', '201002084370']; // بدون +

        foreach ($to as $receiver) {
            $payload = [
                "UserName"    => $user,
                "Password"    => $pass,
                "SMSText"     => $smsText,
                "SMSLang"     => $lang,
                "SMSSender"   => $from,
                "SMSReceiver" => $receiver,
                "SMSID"       => Str::uuid()->toString(),
            ];

            try {
                $response = Http::asJson()->post($url, $payload);
                if ($response->failed()) {
                    Log::error("SMS send failed", [
                        'receiver' => $receiver,
                        'status'   => $response->status(),
                        'body'     => $response->body(),
                    ]);
                } else {
                    Log::info("SMS sent successfully", [
                        'receiver' => $receiver,
                        'body'     => $response->body(),
                    ]);
                }
            } catch (\Throwable $e) {
                Log::error("SMS send exception", [
                    'receiver' => $receiver,
                    'error'    => $e->getMessage(),
                ]);
            }
        }
    }

    /** تصدير Excel */
    public function export()
    {
        return Excel::download(new AttendanceCompanyExport, 'attendance_company.xlsx');
    }

    /** استيراد Excel */
    public function import(Request $request)
    {
        $request->validate([
            'file' => ['required','file','mimes:xlsx,csv'],
        ]);

        Excel::import(new AttendanceCompanyImport, $request->file('file'));
        return back()->with('success', 'تم الاستيراد بنجاح');
    }
    public function downloadTemplate()
{
    return Excel::download(new AttendanceCompanyTemplateExport, 'attendance_company_template.xlsx');
}
}
