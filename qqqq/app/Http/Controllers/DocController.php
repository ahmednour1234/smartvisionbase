<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\Request;

class DocController extends Controller
{
    public function proforma(Request $request, Company $company)
    {
        $user = $request->user();

        abort_unless($user && $user->role && in_array($user->role->slug, ['admin', 'manager'], true), 403);

        $company->loadMissing(['event', 'package', 'country', 'owner']);

        return view('docs.proforma', [
            'company' => $company,
            'issuedAt' => now(),
        ]);
    }


    public function contract(Request $request, Company $company)
    {
        $user = $request->user();

        abort_unless($user && $user->role && in_array($user->role->slug, ['admin', 'manager'], true), 403);

        $company->loadMissing(['event', 'package', 'country', 'owner']);

        return view('docs.contract', [
            'company' => $company,
            'issuedAt' => now(),
        ]);
    }

}
