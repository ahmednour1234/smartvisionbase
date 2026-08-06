<?php

namespace App\Http\Controllers\dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pdf;
use App\Helpers\FileHelper;

class PdfController extends Controller
{
    public function index()
    {
        $pdfs = Pdf::latest()->get();
        return view('content.pdfs.index', compact('pdfs'));
    }

    public function create()
    {
        return view('content.pdfs.create');
    }

public function store(Request $request)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'pdf' => 'required|file|mimes:pdf',
    ]);

    // اجعل اسم الملف مأخوذ من الاسم المدخل
    $pdfPath = FileHelper::uploadfile(
        $request->file('pdf'),
        'pdfs',
        $request->name . '.pdf' // اسم مخصص بدل uuid
    );

    Pdf::create([
        'name' => $request->name,
        'pdf' => $pdfPath,
        'active' => 1,
    ]);

    return redirect()->route('pdfs.index')->with('success', 'PDF uploaded successfully');
}

    public function edit(Pdf $pdf)
    {
        return view('content.pdfs.edit', compact('pdf'));
    }
public function update(Request $request, Pdf $pdf)
{
    $request->validate([
        'name' => 'sometimes|required|string|max:255',
        'pdf' => 'nullable|file|mimes:pdf',
    ]);

    $data = [];

    if ($request->filled('name')) {
        $data['name'] = $request->name;
    }

    if ($request->hasFile('pdf')) {
        // إنشاء اسم ملف مناسب من الاسم (أو الاسم القديم إن لم يُرسل جديد)
        $baseName = $request->filled('name') ? $request->name : $pdf->name;
        $pdfPath = FileHelper::uploadfile($request->file('pdf'), 'pdfs', $baseName . '.pdf');
        $data['pdf'] = $pdfPath;
    }

    $pdf->update($data);

    return redirect()->route('pdfs.index')->with('success', 'PDF updated successfully');
}


    public function destroy(Pdf $pdf)
    {
        $pdf->delete();
        return back()->with('success', 'PDF deleted successfully');
    }

    public function toggleActive(Pdf $pdf)
    {
        $pdf->update(['active' => !$pdf->active]);
        return back()->with('success', 'PDF status updated');
    }
}
