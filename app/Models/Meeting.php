<?php

namespace App\Models;

use App\Models\Lead;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class Meeting extends Model
{
    protected static function booted(): void
    {
        static::saving(function (Meeting $meeting) {
    $user = Auth::user();
    if (! $user) {
        return;
    }

    if (method_exists($user, 'isAdmin') && $user->isAdmin()) {
        return;
    }

    // Ownership rules:
    // - create: staff can only create meetings on their own leads; user_id becomes current user
    // - update: staff can update if they are creator OR (optional) they own the lead
    $lead = null;
    if ($meeting->lead_id) {
        $lead = Lead::query()->select('id', 'sales_rep_id')->find($meeting->lead_id);
        if ($lead && (int) $lead->sales_rep_id !== (int) $user->id) {
            throw new AccessDeniedHttpException('Forbidden');
        }
    }

    if (! $meeting->exists) {
        $meeting->user_id = (int) $user->id;
        return;
    }

    if ((int) $meeting->user_id === (int) $user->id) {
        return;
    }

    if ((bool) config('crm.meetings.allow_lead_owner_edit') && $lead && (int) $lead->sales_rep_id === (int) $user->id) {
        return;
    }

    throw new AccessDeniedHttpException('Forbidden');
});
    }

    protected $table = 'meetings';

    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'lead_id',
        'meeting_date',
        'duration_minutes',
        'meeting_type',
        'notes',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function lead()
    {
        return $this->belongsTo(Lead::class, 'lead_id');
    }
}
