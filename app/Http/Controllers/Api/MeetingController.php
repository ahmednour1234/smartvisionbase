<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Models\Meeting;
use App\Services\AuditService;
use App\Services\LeadActivityService;
use App\Support\Deny;
use Illuminate\Http\Request;

class MeetingController extends Controller
{
    public function indexByLead(Request $request, Lead $lead)
    {
        $user = $request->user();

        if (! $this->canAccessLead($user, $lead)) {
            return Deny::hiddenOrForbidden();
        }

        $meetings = Meeting::query()
            ->where('lead_id', $lead->id)
            ->with('user:id,name,email')
            ->orderByDesc('meeting_date')
            ->get();

        return response()->json($meetings);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'lead_id' => 'required|integer|exists:leads,id',
            'meeting_date' => 'required|date',
            'meeting_type' => 'required|string|in:call,online,in_person',
            'duration_minutes' => 'nullable|integer|min:0',
            'notes' => 'nullable|string|max:255',
            'user_id' => 'nullable|integer|exists:users,id',
        ]);

        $user = $request->user();
        $lead = Lead::findOrFail($data['lead_id']);

        if (! $this->canAccessLead($user, $lead)) {
            return Deny::hiddenOrForbidden();
        }

        // Staff can only create meetings for themselves.
        if (! $user->isAdmin()) {
            $data['user_id'] = $user->id;
        } else {
            $data['user_id'] ??= $user->id;
        }

        $meeting = Meeting::create([
            'lead_id' => $lead->id,
            'meeting_date' => $data['meeting_date'],
            'meeting_type' => $data['meeting_type'],
            'duration_minutes' => $data['duration_minutes'] ?? 0,
            'notes' => $data['notes'] ?? null,
            'user_id' => $data['user_id'],
        ]);

        LeadActivityService::add($lead->id, $user->id, 'meeting_created', 'Meeting created');
        AuditService::log($user, 'create', 'meeting', $meeting->id, $meeting->toArray());

        return response()->json($meeting, 201);
    }

    public function update(Request $request, int $id)
    {
        $meeting = Meeting::with('lead')->findOrFail($id);
        $user = $request->user();

        if (! $this->canEditMeeting($user, $meeting)) {
            return Deny::hiddenOrForbidden();
        }

        $data = $request->validate([
            'meeting_date' => 'sometimes|date',
            'meeting_type' => 'sometimes|string|in:call,online,in_person',
            'duration_minutes' => 'sometimes|integer|min:0',
            'notes' => 'nullable|string|max:255',
        ]);

        $before = $meeting->toArray();
        $meeting->update($data);

        LeadActivityService::add($meeting->lead_id, $user->id, 'meeting_updated', 'Meeting updated');
        AuditService::log($user, 'update', 'meeting', $meeting->id, [
            'before' => $before,
            'after' => $meeting->fresh()->toArray(),
        ]);

        return response()->json($meeting->fresh());
    }

    public function destroy(Request $request, int $id)
    {
        $meeting = Meeting::with('lead')->findOrFail($id);
        $user = $request->user();

        if (! $this->canEditMeeting($user, $meeting)) {
            return Deny::hiddenOrForbidden();
        }

        $payload = $meeting->toArray();
        $leadId = $meeting->lead_id;

        $meeting->delete();

        LeadActivityService::add($leadId, $user->id, 'meeting_deleted', 'Meeting deleted');
        AuditService::log($user, 'delete', 'meeting', $id, $payload);

        return response()->json(['ok' => true]);
    }

    private function canEditMeeting($user, Meeting $meeting): bool
    {
        if (! $user) {
            return false;
        }

        if ($user->isAdmin()) {
            return true;
        }

        // If enabled, allow lead owner to manage meetings even if they are not the creator.
        if (config('crm.security.allow_lead_owner_edit_meetings') && $meeting->lead) {
            return (int) $meeting->lead->sales_rep_id === (int) $user->id;
        }

        // Default: only the creator can edit/delete.
        return (int) $meeting->user_id === (int) $user->id;
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
