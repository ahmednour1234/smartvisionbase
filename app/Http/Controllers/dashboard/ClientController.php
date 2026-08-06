<?php

namespace App\Http\Controllers\dashboard;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Form;
use Illuminate\Http\Request;
use App\Helpers\FileHelper;
use App\Imports\ClientsImport;
use App\Exports\ClientsExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ClientsTemplateExport;

class ClientController extends Controller
{
  public function index(Request $request)
{
    $query = Client::query();

    if ($request->filled('name')) {
        $query->where('name', 'like', '%' . $request->name . '%');
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

    if ($request->filled('form_id')) {
        $query->where('form_id', $request->form_id);
    }

    $clients = $query->where('type', 1)->latest()->paginate(10);
$forms=Form::all();
    return view('content.clients.index', compact('clients','forms'));
}


  public function create()
  {
    $forms = Form::all();
    return view('content.clients.create', compact('forms'));
  }

  public function store(Request $request)
  {
    $data = $request->validate([
      'name'     => 'required|string|max:255',
      'email'    => 'required|email|unique:clients,email',
      'phone'    => 'nullable|string|max:20',
      'job'      => 'nullable|string|max:255',
      'active'   => 'required|boolean',
      'country_code'     => 'required',
      'img'      => 'nullable',
      'form_id'  => 'required|exists:forms,id',
              'type'          => 'required|in:1,2', // تم تصحيح الفاليديشن هنا

    ]);

    if ($request->hasFile('img')) {
      $data['img'] = FileHelper::uploadImage($request->file('img'), 'uploads/clients');
    }

    Client::create($data);

    return redirect()->route('dashboard.clients.index')->with('success', 'تم إضافة العميل بنجاح');
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
    return view('content.clients.excel', compact('clients', 'forms'));
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
    ini_set('max_execution_time', 300); // 5 دقائق
    ini_set('memory_limit', '512M');

    $request->validate([
        'file' => 'required|mimes:xlsx,csv',
        'form_id' => 'required|exists:forms,id',
    ]);

    $importer = new ClientsImport($request->form_id);
    Excel::import($importer, $request->file('file'));

    $added = $importer->getAddedCount();
    $skipped = $importer->getSkippedCount();

    return redirect()->back()->with('success', "تم استيراد $added عميل، وتجاهل $skipped مكرر");
}



public function exportExcel(Request $request)
{
    $form_id = $request->input('form_id');
    $date_from = $request->input('from');
    $date_to = $request->input('to');

    return Excel::download(
        new ClientsExport($form_id, $date_from, $date_to),
        'clients.xlsx'
    );
}

  public function exportTemplate()
  {
    return Excel::download(new ClientsTemplateExport, 'client_template.xlsx');
  }
}
