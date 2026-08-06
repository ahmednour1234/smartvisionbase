<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Lead;
use App\Models\Meeting;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class OpsController extends Controller
{
    public function metrics(Request $request)
    {
        $now = Carbon::now();

        // DB health
        $dbOk = true;
        try {
            DB::select('select 1');
        } catch (\Throwable $e) {
            $dbOk = false;
        }

        // Counts
        $counts = [
            'users' => User::count(),
            'leads' => Lead::count(),
            'meetings' => Meeting::count(),
        ];

        // Optional audit stats (table name: audit_log)
        $audit = null;
        if (Schema::hasTable('audit_log')) {
            $since = $now->copy()->subMinutes(60);
            $audit = [
                'last_60m' => AuditLog::where('created_at', '>=', $since)->count(),
                'last_24h' => AuditLog::where('created_at', '>=', $now->copy()->subHours(24))->count(),
            ];
        }

        return response()->json([
            'ok' => true,
            'time' => $now->toIso8601String(),
            'db' => ['ok' => $dbOk, 'driver' => DB::getDriverName()],
            'counts' => $counts,
            'audit' => $audit,
            'observability' => [
                'LOG_API_REQUESTS' => (bool) env('LOG_API_REQUESTS', false),
                'LOG_SLOW_QUERIES' => (bool) env('LOG_SLOW_QUERIES', false),
                'SLOW_REQUEST_MS' => (int) env('SLOW_REQUEST_MS', 900),
                'SLOW_QUERY_MS' => (int) env('SLOW_QUERY_MS', 250),
            ],
        ]);
    }
}
