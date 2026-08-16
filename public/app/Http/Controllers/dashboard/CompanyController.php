<?php

namespace App\Http\Controllers\dashboard;

use App\Http\Controllers\Controller;
use App\Models\Company;
use Illuminate\Http\Request;
use App\Helpers\FileHelper;
use Illuminate\Support\Facades\DB;

class CompanyController extends Controller
{
public function index(Request $request)
{
    // تحقق بسيط لمدخلات الفلاتر
    $request->validate([
        'q'          => 'nullable|string|max:255',
        'status'     => 'nullable|in:all,active,inactive',
        'min_votes'  => 'nullable|integer|min:0',
        'max_votes'  => 'nullable|integer|min:0',
        'per_page'   => 'nullable|integer|min:5|max:200',
    ]);

    $query = Company::query();

    // بحث بالاسم (AR/EN)
    if ($q = trim((string) $request->input('q'))) {
        $query->where(function ($w) use ($q) {
            $w->where('name_ar', 'like', "%{$q}%")
              ->orWhere('name_en', 'like', "%{$q}%");
        });
    }

    // حالة التفعيل
    $status = $request->input('status');
    if ($status === 'active') {
        $query->where('active', 1);
    } elseif ($status === 'inactive') {
        $query->where('active', 0);
    }

    // حدود الأصوات
    if ($request->filled('min_votes')) {
        $query->where('count_vote', '>=', (int) $request->input('min_votes'));
    }
    if ($request->filled('max_votes')) {
        $query->where('count_vote', '<=', (int) $request->input('max_votes'));
    }

    // عدد العناصر في الصفحة
    $perPage = (int) ($request->input('per_page', 20));

    $companies = $query
        ->orderBy('orders', 'asc')
        ->paginate($perPage)
        ->withQueryString(); // يحافظ على الاستعلامات داخل روابط الصفحات

    return view('content.companies.index', compact('companies'));
}

    public function create()
    {
        return view('content.companies.create');
    }
  public function store(Request $request)
    {
        $data = $request->validate([
            'name_ar'        => 'required|string|max:255',
            'name_en'        => 'required|string|max:255',
            'description_ar' => 'nullable|string',
            'description_en' => 'nullable|string',
            'title_ar'       => 'nullable|string|max:255',
            'title_en'       => 'nullable|string|max:255',
            'country'        => 'nullable|string|max:100',
            'link'           => 'nullable|url',
            'image'          => 'nullable',
            'regulation'     => 'nullable|string',
            'stars'          => 'nullable',
            'category'       => 'nullable',
            'count_vote'     => 'nullable',
            'orders'         => 'nullable|integer|min:1',
                        'number_of_followers'=>'nullable',
                        'followers_ticktock'=>'nullable'

        ]);

        if ($request->hasFile('image')) {
            $data['image'] = FileHelper::uploadImage($request->file('image'), 'content/companies');
        }

        DB::transaction(function () use (&$data) {
            // لو عايز ضمن نفس الـ category فقط، فعّل السطر الجاي واستبدل الاستعلامات كلها بنفس الشرط
            // $scope = Company::query()->where('category', $data['category'] ?? null);

            $max = (int) Company::max('orders'); // ضمن كل الشركات
            $desired = isset($data['orders']) ? (int) $data['orders'] : ($max + 1);
            $desired = max(1, min($desired, $max + 1)); // Clamp

            // زحزحة اللي >= desired علشان نفسح مكان
            Company::where('orders', '>=', $desired)->increment('orders');

            $data['orders'] = $desired;

            Company::create($data);
        });

        return redirect()->route('dashboard.companies.index')
            ->with('success', 'Company created & ordered successfully.');
    }

    /**
     * تعديل شركة مع تبديل/تحريك موقعها في الترتيب.
     * - لو غيّرت orders: هنحرّكه ونرتّب الباقي بدون فراغات.
     * - لو ما غيّرتش orders: مجرد تحديث بيانات.
     */
    public function update(Request $request, Company $company)
    {
        $data = $request->validate([
            'name_ar'        => 'required|string|max:255',
            'name_en'        => 'required|string|max:255',
            'description_ar' => 'nullable|string',
            'description_en' => 'nullable|string',
            'title_ar'       => 'nullable|string|max:255',
            'title_en'       => 'nullable|string|max:255',
            'country'        => 'nullable|string|max:100',
            'link'           => 'nullable|url',
            'image'          => 'nullable|image|max:2048',
            'regulation'     => 'nullable|string',
            'stars'          => 'nullable',
            'category'       => 'nullable',
            'count_vote'     => 'nullable|integer',
            'orders'         => 'nullable|integer|min:1',
            'number_of_followers'=>'nullable',
                                    'followers_ticktock'=>'nullable'

        ]);

        if ($request->hasFile('image')) {
            $data['image'] = FileHelper::uploadImage($request->file('image'), 'content/companies');
        }

        DB::transaction(function () use (&$data, $company) {

            // 1) حدّث باقي الحقول أولاً (بدون orders)
            $updateData = $data;
            unset($updateData['orders']);
            if (!empty($updateData)) {
                $company->update($updateData);
            }

            // 2) لو فيه تغيير في الترتيب، حرّكه
            if (array_key_exists('orders', $data)) {
                $old = (int) ($company->orders ?? 0);
                $new = (int) $data['orders'];

                if ($new !== $old) {
                    $this->moveRow($company, $new);   // تحريك بنظام "توسيع/تقليص" النطاق
                    // أو لو تفضّل "تبديل مباشر" فقط مع عنصر واحد، استخدم:
                    // $this->swapOrder($company, $new);
                }

                // أضمن إن الترتيب متسلسل بدون فجوات (اختياري لكنه مريح)
                $this->normalizeOrders();
            }
        });

        return redirect()->route('dashboard.companies.index')
            ->with('success', 'Company updated & re-ordered successfully.');
    }

    /**
     * تحريك عنصر إلى موضع جديد مع إعادة ضبط بقية العناصر بدون فجوات.
     * - لو new < old: نزود +1 لكل اللي بين [new .. old-1]
     * - لو new > old: ننقص -1 لكل اللي بين [old+1 .. new]
     */
    protected function moveRow(Company $company, int $new): void
    {
        $old = (int) ($company->orders ?? 0);
        if ($old === 0) {
            // لو مفيش ترتيب قديم، اعتبره في الآخر
            $old = (int) Company::max('orders') + 1;
        }

        $max = (int) Company::where('id', '!=', $company->id)->max('orders');
        $new = max(1, min($new, max(1, $max))); // Clamp داخل النطاق الحالي

        if ($new < $old) {
            Company::where('id', '!=', $company->id)
                ->whereBetween('orders', [$new, $old - 1])
                ->increment('orders');
        } elseif ($new > $old) {
            Company::where('id', '!=', $company->id)
                ->whereBetween('orders', [$old + 1, $new])
                ->decrement('orders');
        }

        $company->update(['orders' => $new]);
    }

    /**
     * تبديل مباشر مع العنصر اللي في موضع $new (Swap فقط).
     */
    protected function swapOrder(Company $company, int $new): void
    {
        $other = Company::where('orders', $new)->first();
        if (!$other) {
            // لو مفيش حد في المكان ده، فقط حرّكه
            $company->update(['orders' => $new]);
            return;
        }

        $old = (int) $company->orders;
        $other->update(['orders' => $old]);
        $company->update(['orders' => $new]);
    }

    /**
     * إعادة ترقيم كل الصفوف 1..N حسب order asc ثم created_at desc
     * تضمن عدم وجود NULL أو فجوات.
     */
    protected function normalizeOrders(): void
    {
        $ids = Company::
            orderBy('orders', 'asc')
            ->pluck('id');

        $i = 1;
        foreach ($ids as $id) {
            Company::where('id', $id)->update(['orders' => $i++]);
        }
    }

    public function edit(Company $company)
    {
        return view('content.companies.edit', compact('company'));
    }


    public function show(Company $company)
    {
        return view('content.companies.show', compact('company'));
    }

    public function activate($id)
    {
        $company = Company::findOrFail($id);
        $company->update(['active' => true]);

        return redirect()->back()->with('success', 'Company activated.');
    }
    public function destroy(Company $company)
{
    // حذف الصورة إذا كانت موجودة
    if ($company->image && file_exists(public_path($company->image))) {
        @unlink(public_path($company->image));
    }

    // حذف السجل من قاعدة البيانات
    $company->delete();

    return redirect()->route('dashboard.companies.index')->with('success', 'Company deleted successfully.');
}


    public function deactivate($id)
    {
        $company = Company::findOrFail($id);
        $company->update(['active' => false]);

        return redirect()->back()->with('success', 'Company deactivated.');
    }
}
