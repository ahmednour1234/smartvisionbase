<?php
// app/Http/Controllers/Dashboard/MarketingRefController.php
namespace App\Http\Controllers\dashboard;

use App\Http\Controllers\Controller;
use App\Models\MarketingRef;
use Illuminate\Http\Request;

class MarketingRefController extends Controller
{
    public function index(Request $request) {
        $refs = MarketingRef::query()
            ->when($request->q, fn($q) => $q->where('name','like','%'.$request->q.'%')
                                            ->orWhere('code','like','%'.$request->q.'%'))
            ->latest()->paginate(20);
        return view('content.marketing_refs.index', compact('refs'));
    }

    public function create() {
        return view('content.marketing_refs.create');
    }

    public function store(Request $request) {
        $data = $request->validate([
            'name'          => ['required','string','max:200'],
            'code'          => ['nullable','string','max:20','unique:marketing_refs,code'],
            'allowed_paths' => ['nullable','array'], // مثال: ['/register','/lead']
            'active'        => ['required','boolean'],
            'expires_at'    => ['nullable','date'],
            'max_uses'      => ['nullable','integer','min:1'],
        ]);
        $data['created_by'] = auth()->id();
        MarketingRef::create($data);
        return back()->with('ok','تم إنشاء مرجع تسويقي');
    }

    public function edit(MarketingRef $marketing_ref) {
        return view('content.marketing_refs.edit', ['ref' => $marketing_ref]);
    }

    public function update(Request $request, MarketingRef $marketing_ref) {
        $data = $request->validate([
            'name'          => ['required','string','max:200'],
            'code'          => ['required','string','max:20','unique:marketing_refs,code,'.$marketing_ref->id],
            'allowed_paths' => ['nullable','array'],
            'active'        => ['required','boolean'],
            'expires_at'    => ['nullable','date'],
            'max_uses'      => ['nullable','integer','min:1'],
        ]);
        $marketing_ref->update($data);
        return back()->with('ok','تم التحديث');
    }

    public function destroy(MarketingRef $marketing_ref) {
        $marketing_ref->delete();
        return back()->with('ok','تم الحذف');
    }
}
