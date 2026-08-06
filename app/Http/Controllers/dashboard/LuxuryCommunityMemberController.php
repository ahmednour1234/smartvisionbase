<?php
namespace App\Http\Controllers\dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LuxuryCommunityMember;
use App\Helpers\FileHelper;

class LuxuryCommunityMemberController extends Controller
{
public function index(Request $request)
{
    $query = LuxuryCommunityMember::query();

    // فلترة بالـ active لو مبعوتة (0 أو 1)
    if ($request->filled('active') && in_array($request->active, ['0', '1'], true)) {
        $query->where('active', (int) $request->active);
    }

    $members = $query->latest()->paginate(20)->withQueryString();

    return view('content.luxury_community_members.index', [
        'members'       => $members,
        'selectedActive'=> $request->active, // عشان نظهر الاختيار في الواجهة
    ]);
}

    public function create()
    {
        return view('content.luxury_community_members.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name_ar'     => 'required|string|max:255',
            'name_en'     => 'nullable|string|max:255',
            'title_ar'    => 'required|string|max:255',
            'title_en'    => 'nullable|string|max:255',
            'company'     => 'required|string|max:255',
            'email'       => 'nullable|email',
            'phone'       => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'image'       => 'nullable|image|max:2048',
            'active'      => 'nullable|boolean',
        ]);

        if ($request->hasFile('image')) {
            $data['image'] = FileHelper::uploadImage($request->file('image'), 'luxury_members');
        }

        // لو مش مبعوت، خليه 0
        if (!array_key_exists('active', $data)) {
            $data['active'] = 0;
        }

        LuxuryCommunityMember::create($data);

        return redirect()->route('dashboard.luxury-members.index')->with('success', 'تمت الإضافة بنجاح');
    }

    public function edit($id)
    {
        $member = LuxuryCommunityMember::findOrFail($id);
        return view('content.luxury_community_members.edit', compact('member'));
    }

    public function update(Request $request, $id)
    {
        $member = LuxuryCommunityMember::findOrFail($id);

        $data = $request->validate([
            'name_ar'     => 'required|string|max:255',
            'name_en'     => 'nullable|string|max:255',
            'title_ar'    => 'required|string|max:255',
            'title_en'    => 'nullable|string|max:255',
            'company'     => 'required|string|max:255',
            'email'       => 'nullable|email',
            'phone'       => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'image'       => 'nullable|image|max:2048',
            'active'      => 'nullable|boolean',
        ]);

        if ($request->hasFile('image')) {
            // لو عندك FileHelper بيمسح القديم لو مررته كـ 3rd arg
            $data['image'] = FileHelper::uploadImage($request->file('image'), 'luxury_members', $member->image);
        }

        $member->update($data);

        return redirect()->route('dashboard.luxury-members.index')->with('success', 'تم التحديث بنجاح');
    }

    public function destroy($id)
    {
        $member = LuxuryCommunityMember::findOrFail($id);
        $member->delete();

        return redirect()->back()->with('success', 'تم الحذف بنجاح');
    }

    /**
     * تفعيل العضو: تحدّث العمود active من 0 إلى 1
     */
    public function activate($id)
    {
        $member = LuxuryCommunityMember::findOrFail($id);

        if ( $member->active == 1) {
            return back()->with('info', 'العنصر مُفعّل بالفعل');
        }

        $member->update(['active' => 1]);

        return back()->with('success', 'تم التفعيل بنجاح');
    }
}
