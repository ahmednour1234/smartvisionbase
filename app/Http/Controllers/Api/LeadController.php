<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LeadStoreRequest;
use App\Http\Requests\LeadUpdateRequest;
use App\Models\Lead;
use App\Services\AuditService;
use App\Services\LeadActivityService;
use App\Support\Deny;
use Illuminate\Http\Request;

class LeadController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $q = trim((string) $request->get('q', ''));
        $status = $request->get('status');
        $eventId = $request->get('event_id');

        $query = Lead::query()->with(['event', 'salesRep']);

        // Scope: staff users only see their own leads.
        if ($user && ! $user->isAdmin()) {
            $query->where('sales_rep_id', $user->id);
        }

        if ($q !== '') {
            $query->where(function ($sub) use ($q) {
                $sub->where('company_name', 'like', "%{$q}%")
                    ->orWhere('contact_person', 'like', "%{$q}%")
                    ->orWhere('contact_email', 'like', "%{$q}%")
                    ->orWhere('contact_mobile', 'like', "%{$q}%");
            });
        }

        if (! empty($status)) {
            $query->where('status', $status);
        }

        if (! empty($eventId)) {
            $query->where('event_id', $eventId);
        }

        return response()->json(
            $query->orderByDesc('id')->paginate((int) $request->get('per_page', 20))
        );
    }

    public function store(LeadStoreRequest $request)
    {
        $user = $request->user();

        $data = $request->validated();

        if (! $user->isAdmin()) {
            // Force assignment to the current staff user.
            $data['sales_rep_id'] = $user->id;
        }

        $data['created_by'] = $user->id;
        $data['updated_by'] = $user->id;

        $lead = Lead::create($data);

        if (! empty($data['countries'])) {
            $lead->countries()->sync($data['countries']);
        }

        LeadActivityService::add($lead->id, $user->id, 'create', 'Lead created');
        AuditService::log($user, 'create', 'lead', $lead->id, $data);

        return response()->json($lead->load(['countries', 'event', 'salesRep']), 201);
    }

    public function show(Request $request, Lead $lead)
    {
        $user = $request->user();

        if (! $this->canAccessLead($user, $lead)) {
            return Deny::hiddenOrForbidden();
        }

        return response()->json($lead->load(['countries', 'event', 'salesRep', 'meetings', 'activities.user']));
    }

    public function update(LeadUpdateRequest $request, Lead $lead)
    {
        $user = $request->user();

        if (! $this->canAccessLead($user, $lead)) {
            return Deny::hiddenOrForbidden();
        }

        $data = $request->validated();

        // Staff cannot reassign ownership.
        if (! $user->isAdmin()) {
            unset($data['sales_rep_id']);
        }

        $data['updated_by'] = $user->id;

        $lead->update($data);

        if (array_key_exists('countries', $data)) {
            $lead->countries()->sync($data['countries'] ?? []);
        }

        LeadActivityService::add($lead->id, $user->id, 'update', 'Lead updated');
        AuditService::log($user, 'update', 'lead', $lead->id, $data);

        return response()->json($lead->refresh()->load(['countries', 'event', 'salesRep']));
    }

    public function destroy(Request $request, Lead $lead)
    {
        $user = $request->user();

        if (! $user || ! $user->can('leads.delete')) {
            return Deny::hiddenOrForbidden();
        }

        // Only admins (or explicit permission holders) can delete leads; staff should not.
        if (! $user->isAdmin()) {
            return Deny::hiddenOrForbidden();
        }

        if (! $this->canAccessLead($user, $lead)) {
            return Deny::hiddenOrForbidden();
        }

        $payload = $lead->toArray();

        $leadId = $lead->id;
        $lead->countries()->detach();
        $lead->delete();

        LeadActivityService::add($leadId, $user->id, 'delete', 'Lead deleted');
        AuditService::log($user, 'delete', 'lead', $leadId, $payload);

        return response()->json(['ok' => true]);
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
