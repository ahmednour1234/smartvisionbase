<?php

namespace App\Http\Controllers\web;

use App\Http\Controllers\Controller;
use App\Models\Package;
use Illuminate\Http\Request;

class PackageController extends Controller
{
public function index(Request $request)
{
$packages = Package::where('active', true)->orderBy('sort', 'asc')->paginate(6000);
    $locale = app()->getLocale();

    if ($request->ajax()) {
        $html = '';
        foreach ($packages as $key => $package) {
            $desc = $locale === 'ar' ? $package->description_ar : $package->description_en;
            $listItems = array_filter(preg_split("/\r\n|\n|\r/", $desc));

            $html .= '<div class="col-sm-6 col-md-4 mb-4 package-item">
                <div class="pricing-modern">
                    <div class="pricing-modern-header">
                        <p class="pricing-modern-price heading-3">' . ($locale === 'ar' ? $package->name_ar : $package->name_en) . '</p>
                    </div>
                    <div class="pricing-modern-body">
                        <ul class="pricing-modern-list">';
            foreach ($listItems as $item) {
                $html .= '<li class="package-description">' . $item . '</li>';
            }
            $html .= '</ul>
                        <button class="button button-primary" type="button" data-triangle=".button-overlay">
                            <span>' . __('Become Sponsor') . '</span>
                            <span class="button-overlay"></span>
                        </button>
                    </div>
                </div>
            </div>';
        }

        return response()->json(['html' => $html]);
    }

    return view('web.content.package', compact('packages', 'locale'));
}


}
