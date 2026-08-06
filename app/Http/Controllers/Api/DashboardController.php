<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function summary(Request $request)
    {
        $todayStr = Carbon::today()->toDateString();

        $user = $request->user();
        $base = Lead::query();

        // Authorization: staff only sees own leads
        if ($user && !$user->isAdmin()) {
            $base->where('sales_rep_id', $user->id);
        }

        $row = (clone $base)->selectRaw(
            "SUM(CASE WHEN status = 'new' THEN 1 ELSE 0 END) AS new_count,"
            . " SUM(CASE WHEN status = 'contacted' THEN 1 ELSE 0 END) AS contacted_count,"
            . " SUM(CASE WHEN status = 'meeting' THEN 1 ELSE 0 END) AS meeting_count,"
            . " SUM(CASE WHEN status = 'negotiation' THEN 1 ELSE 0 END) AS negotiation_count,"
            . " SUM(CASE WHEN status = 'won' THEN 1 ELSE 0 END) AS won_count,"
            . " SUM(CASE WHEN status = 'lost' THEN 1 ELSE 0 END) AS lost_count,"
            . " SUM(CASE WHEN next_followup = ? THEN 1 ELSE 0 END) AS due_today,"
            . " SUM(CASE WHEN next_followup IS NOT NULL AND next_followup < ? THEN 1 ELSE 0 END) AS overdue",
            [$todayStr, $todayStr]
        )->first();

        $byStatus = [
            'new' => (int) ($row->new_count ?? 0),
            'contacted' => (int) ($row->contacted_count ?? 0),
            'meeting' => (int) ($row->meeting_count ?? 0),
            'negotiation' => (int) ($row->negotiation_count ?? 0),
            'won' => (int) ($row->won_count ?? 0),
            'lost' => (int) ($row->lost_count ?? 0),
        ];

        return response()->json([
            'by_status' => $byStatus,
            'due_today' => (int) ($row->due_today ?? 0),
            'overdue' => (int) ($row->overdue ?? 0),
            'won' => (int) ($row->won_count ?? 0),
            'lost' => (int) ($row->lost_count ?? 0),
        ]);
    }
}
