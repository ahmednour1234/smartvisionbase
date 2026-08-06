<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use Illuminate\Http\Request;

class FaqController extends Controller
{
    public function index()
    {
        $faqs = Faq::orderBy('sort_order')->orderBy('id')->paginate(20);
        return view('dashboard.faqs.index', compact('faqs'));
    }

    public function create()
    {
        $faq = new Faq(['is_active' => true, 'sort_order' => 0]);
        return view('dashboard.faqs.form', compact('faq'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'question' => ['required','string','max:255'],
            'answer' => ['required','string','max:5000'],
            'is_active' => ['nullable','boolean'],
            'sort_order' => ['nullable','integer','min:0','max:999999'],
        ]);
        $data['is_active'] = (bool) ($data['is_active'] ?? false);
        Faq::create($data);
        return redirect()->route('dashboard.faqs.index')->with('success', 'FAQ created.');
    }

    public function edit(Faq $faq)
    {
        return view('dashboard.faqs.form', compact('faq'));
    }

    public function update(Request $request, Faq $faq)
    {
        $data = $request->validate([
            'question' => ['required','string','max:255'],
            'answer' => ['required','string','max:5000'],
            'is_active' => ['nullable','boolean'],
            'sort_order' => ['nullable','integer','min:0','max:999999'],
        ]);
        $data['is_active'] = (bool) ($data['is_active'] ?? false);
        $faq->update($data);
        return redirect()->route('dashboard.faqs.index')->with('success', 'FAQ updated.');
    }

    public function destroy(Faq $faq)
    {
        $faq->delete();
        return redirect()->route('dashboard.faqs.index')->with('success', 'FAQ deleted.');
    }
}


