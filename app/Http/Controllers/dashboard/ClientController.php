<?php

namespace App\Http\Controllers\dashboard;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Form;
use App\Models\Event;
use Illuminate\Http\Request;
use App\Helpers\FileHelper;
use App\Imports\ClientsImport;
use App\Exports\ClientsExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ClientsTemplateExport;
use App\Mail\ClientQrMail;
use App\Models\Qrcode as QrcodeModel;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use QrCode; 
class ClientController extends Controller
{
  public function index(Request $request)
  {
    $query = Client::query();
$events=Event::all();
    if ($request->filled('name')) {
      $query->where('name', 'like', '%' . $request->name . '%');
    }
  if ($request->filled('event_id')) {
      $query->where('event_id', $request->event_id);
    }
    if ($request->filled('phone')) {
      $query->where('phone', 'like', '%' . $request->phone . '%');
    }

    if ($request->filled('date_from')) {
      $query->whereDate('created_at', '>=', $request->date_from);
    }

    if ($request->filled('date_to')) {
      $query->whereDate('created_at', '<=', $request->date_to);
    }

    $clients = $query->where('type',1)->latest()->paginate(10);

    return view('content.clients.index', compact('clients','events'));
  }

  public function create()
  {
    $forms = Form::all();
    return view('content.clients.create', compact('forms'));
  }

 public function store(Request $request)
    {
        $data = $request->validate([
            'name'         => 'required|string|max:255',
            'email'        => 'required|email',
            'phone'        => 'nullable|string|max:20',
            'job'          => 'nullable|string|max:255',
            'active'       => 'required|boolean',
            'country_code' => 'required',
            'img'          => 'nullable',
            'form_id'      => 'required|exists:forms,id',
        ]);

        if ($request->hasFile('img')) {
            $data['img'] = FileHelper::uploadImage($request->file('img'), 'uploads/clients');
        }

        $client = Client::create($data);

        // ابعت الدعوة تلقائيًا بعد الحفظ — Event=1, Lang='en'
        try {
            $this->generateAndSendQrCode($client, 1, 'en');
        } catch (Throwable $e) {
            \Log::error('QR invite failed', [
                'client_id' => $client->id,
                'error'     => $e->getMessage(),
            ]);
            // اختيارياً: تقدر تعرض تنبيه، بس من غير ما تفشل إضافة العميل
        }

        return redirect()->route('dashboard.clients.index')->with('success', 'تم إضافة العميل وإرسال الدعوة');
    }

    protected function generateAndSendQrCode(Client $client, int $eventId = 1, string $lang = 'en'): void
    {
        // short_code فريد
        do {
            $shortCode = Str::random(10);
        } while (QrcodeModel::where('short_code', $shortCode)->exists());

        $qrUrl = url('/qr/' . $shortCode);

        // حفظ صورة الـ QR في public/qrcodes
        $directory = public_path('qrcodes');
        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        $fileName   = 'qr_' . time() . '_' . $client->id . '.png';
        $filePath   = $directory . '/' . $fileName;

        // أنشئ الصورة
        QrCode::format('png')->size(300)->generate($qrUrl, $filePath);

        // رابط مطلق للصورة عشان الإيميل
        $qrImageUrl = asset('public/qrcodes/' . $fileName);

        // ابعت الإيميل (لغة إنجليزية دائمًا)
        Mail::to($client->email)->queue(
            new \App\Mail\ClientQrMail($client, $qrUrl, $qrImageUrl, $eventId, $lang)
        );

        // خزّن سجل الـ QR
        QrcodeModel::create([
            'qrcode'      => $fileName,
            'active'      => 1,
            'short_code'  => $shortCode,
            'email'       => $client->email,
            'scan'        => 0,
            'register_id' => $client->id,
        ]);
    }
  public function show($id)
  {
    $client = Client::findOrFail($id);
    return view('content.clients.show', compact('client'));
  }
    public function excel()
  {
    $forms = Form::all();
    $clients = Client::all();
    $events=Event::all();
    return view('content.clients.excel', compact('clients', 'forms','events'));
  }


  public function edit($id)
  {
    $client = Client::findOrFail($id);
    $forms = Form::all();
    return view('content.clients.edit', compact('client', 'forms'));
  }

  public function update(Request $request, $id)
  {
    $client = Client::findOrFail($id);

    $data = $request->validate([
      'name'     => 'required|string|max:255',
      'email'    => 'required|email|unique:clients,email,' . $client->id,
      'phone'    => 'nullable|string|max:20',
      'job'      => 'nullable|string|max:255',
      'active'   => 'required|boolean',
      'country_code'     => 'required',
      'img'      => 'nullable',
      'form_id'  => 'required|exists:forms,id',
              'type'          => 'required|in:1,2', // تم تصحيح الفاليديشن هنا
    ]);

    if ($request->hasFile('img')) {
      if ($client->img) {
        FileHelper::deleteFile($client->img);
      }
      $data['img'] = FileHelper::uploadImage($request->file('img'), 'uploads/clients');
    }

    $client->update($data);

    return redirect()->route('dashboard.clients.index')->with('success', 'تم تحديث بيانات العميل بنجاح');
  }
  
public function importExcel(Request $request)
{
    $request->validate([
        'file'     => 'required|mimes:xlsx,csv',
        'form_id'  => 'required|exists:forms,id',
        'event_id' => 'required|exists:events,id',
        'lang'=>'nullable'
    ]);

    $formId     = $request->input('form_id');
    $eventId    = $request->input('event_id');
    $sendEmails = $request->boolean('send_email');
    $lang = $request->input('lang');

    // تنفيذ الاستيراد
    $importer = new ClientsImport($formId, $eventId, $sendEmails,$lang);
    Excel::import($importer, $request->file('file'));

    // نتائج
    $added   = $importer->getAddedCount();
    $skipped = $importer->getSkippedCount();

    return redirect()->back()->with('success', "تم استيراد $added عميل، وتجاهل $skipped مكرر");
}



  public function exportExcel()
  {
    return Excel::download(new ClientsExport, 'clients.xlsx');
  }

  public function exportTemplate()
  {
    return Excel::download(new ClientsTemplateExport, 'client_template.xlsx');
  }
}
