<?php
// app/Http/Controllers/Dashboard/LeadController.php
namespace App\Http\Controllers\dashboard;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use Illuminate\Http\Request;

class LeadController extends Controller
{
    public function index(Request $request)
    {
        $leads = Lead::query();

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $leads->whereBetween('created_at', [$request->start_date, $request->end_date]);
        }

        if ($request->filled('campaign')) {
            $leads->where('campaign', 'like', '%' . $request->campaign . '%');
        }

        if ($request->filled('email')) {
            $leads->where('email', 'like', '%' . $request->email . '%');
        }

        $leads = $leads->latest()->paginate(20);

        // إحصائيات سريعة
        $stats = [
            'total_leads' => Lead::count(),
            'today_leads' => Lead::whereDate('created_at', now()->toDateString())->count(),
            'unique_campaigns' => Lead::select('campaign')->distinct()->count(),
        ];

        return view('content.leads.index', compact('leads', 'stats'));
    }

    public function show($id)
    {
        $lead = Lead::findOrFail($id);
        return view('content.leads.show', compact('lead'));
    }
}
