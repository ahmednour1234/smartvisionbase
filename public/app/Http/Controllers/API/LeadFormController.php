<?php
// app/Http/Controllers/Api/LeadFormController.php
namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Lead;

class LeadFormController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:50',
            'email' => 'nullable|email',
            'campaign' => 'nullable|string|max:255',
        ]);

        $lead = Lead::create($validated);

        return response()->json([
            'message' => 'Lead stored successfully',
            'data' => $lead
        ], 201);
    }
}
