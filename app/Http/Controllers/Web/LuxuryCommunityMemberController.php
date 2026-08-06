<?php
namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LuxuryCommunityMember;
use App\Models\HomeSection;
use App\Helpers\FileHelper;

class LuxuryCommunityMemberController extends Controller
{
    public function index()
    {
        $members = LuxuryCommunityMember::where('active', true)->latest()->get();
            $aboutSection   = HomeSection::where('is_active', true)->where('id', 3)->first();

        return view('web.content.luxury', compact('members','aboutSection'));
    }
      public function luxurystore()
    {
        $members = LuxuryCommunityMember::latest()->get();
            $aboutSection   = HomeSection::where('is_active', true)->where('id', 3)->first();

        return view('web.content.lexurystore', compact('members','aboutSection'));
    }
public function store(Request $request)
    {
        $data = $request->validate([
            'name'         => 'required|string|max:255',
            'title'        => 'required|string|max:255',
            'company'      => 'required|string|max:255',
            'email'        => 'nullable|email',
            'country_code' => 'required|string|max:10',
            'phone'        => 'required|string|max:50',
            'image'        => 'nullable|image|max:2048',
        ]);

        // ارفع الصورة لو موجودة
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = FileHelper::uploadImage($request->file('image'), 'luxury_members');
        }

        // خزّن العربي والإنجليزي بنفس القيمة
        $member = LuxuryCommunityMember::create([
            'name_ar'   => $data['name'],
            'name_en'   => $data['name'],
            'title_ar'  => $data['title'],
            'title_en'  => $data['title'],
            'company'   => $data['company'],
            'email'     => $data['email'] ?? null,
            'country_code' => $data['country_code'],
            'phone'     => $data['phone'],
            'image'     => $imagePath,
            'active'    => 0, // دايمًا غير مفعّل لحين الموافقة
        ]);

        return redirect()->route('web.home')
            ->with('success', 'Your request has been submitted successfully.');
    }
}
