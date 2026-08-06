<?php

namespace App\Http\Controllers\dashboard;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Form;
use Illuminate\Http\Request;
use App\Helpers\FileHelper;
use App\Imports\BecomeImport;
use App\Exports\BecomeExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\BecomesTemplateExport;

class BecomeSponsorsController extends Controller
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

    $clients = $query->where('type',2)->latest()->paginate(10);

    return view('content.becomesponsor.index', compact('clients'));
  }

  public function create()
  {
    $forms = Form::all();
    return view('content.becomesponsor.create', compact('forms'));
  }

  public function store(Request $request)
  {
    $data = $request->validate([
      'name'     => 'required|string|max:255',
      'email'    => 'required|email|unique:clients,email',
      'phone'    => 'nullable|string|max:20',
      'job'      => 'nullable|string|max:255',
            'company_name'=>'required',
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

    return redirect()->route('dashboard.becomesponsor.index')->with('success', 'تم إضافة العميل بنجاح');
  }

  public function show($id)
  {
    $client = Client::findOrFail($id);
    return view('content.becomesponsor.show', compact('client'));
  }
    public function excel()
  {
    $forms = Form::all();
    $clients = Client::all();
    return view('content.becomesponsor.excel', compact('clients', 'forms'));
  }


  public function edit($id)
  {
    $client = Client::findOrFail($id);
    $forms = Form::all();
    return view('content.becomesponsor.edit', compact('client', 'forms'));
  }

  public function update(Request $request, $id)
  {
    $client = Client::findOrFail($id);

    $data = $request->validate([
      'name'     => 'required|string|max:255',
      'email'    => 'required|email|unique:clients,email,' . $client->id,
      'phone'    => 'nullable|string|max:20',
      'job'      => 'nullable|string|max:255',
      'company_name'=>'required',
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

    return redirect()->route('dashboard.becomesponsor.index')->with('success', 'تم تحديث بيانات العميل بنجاح');
  }
public function importExcel(Request $request)
{
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



  public function exportExcel()
  {
    return Excel::download(new BecomeExport, 'becomesponsor.xlsx');
  }

  public function exportTemplate()
  {
    return Excel::download(new BecomesTemplateExport, 'becomesponsor.xlsx');
  }
}
