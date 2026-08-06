<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Services\AuditService;
use App\Services\LeadActivityService;
use App\Support\Deny;
use Illuminate\Http\Request;

class LeadActivityController extends Controller
{
    public function index(Request $request, Lead $lead)
    {
        $user = $request->user();

        if (! $this->canAccessLead($user, $lead)) {
            return Deny::hiddenOrForbidden();
        }

        $activities = $lead->activities()->with(['user'])->orderBy('created_at', 'desc')->paginate(50);

        return response()->json($activities);
    }

    public function addNote(Request $request, Lead $lead)
    {
        $user = $request->user();

        if (! $this->canAccessLead($user, $lead)) {
            return Deny::hiddenOrForbidden();
        }

        $data = $request->validate([
            'note' => ['required', 'string', 'max:2000'],
        ]);

        $note = trim($data['note']);

        $activity = LeadActivityService::add($lead->id, $user->id, 'note', $note);
        AuditService::log($user, 'create', 'lead_activity', (string) $activity->id, [
            'lead_id' => $lead->id,
            'note' => $note,
        ]);

        return response()->json($activity, 201);
    }

    private function canAccessLead($user, Lead $lead): bool
    {
        if (! $user) {
            return false;
        }

        if ($user->isAdmin()) {
            return true;
        }

        return (int) $lead->sales_rep_id === (int) $user->id;
    }
}
