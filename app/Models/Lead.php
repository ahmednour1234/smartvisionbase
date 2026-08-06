<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class Lead extends Model
{
    protected static function booted(): void
    {
        static::saving(function (Lead $lead) {
            $user = Auth::user();
            if (! $user) {
                return;
            }

            // Staff users are restricted to their own leads (server-side enforcement).
            if (method_exists($user, 'isAdmin') && $user->isAdmin()) {
                return;
            }

            if ($lead->exists && (int) $lead->sales_rep_id !== (int) $user->id) {
                throw new AccessDeniedHttpException('Forbidden');
            }

            $lead->sales_rep_id = (int) $user->id;
        });
    }

    protected $table = 'leads';

    public $timestamps = false;

    protected $fillable = [
        'company_name',
        'contact_person',
        'contact_mobile',
        'contact_email',
        'contact_linkedin',
        'company_website',
        'event_id',
        'interested_package_id',
        'expected_value',
        'currency',
        'probability',
        'expected_close_date',
        'lead_notes',
        'status',
        'lost_category_id',
        'lost_reason',
        'lost_at',
        'sales_rep_id',
        'last_meeting',
        'next_followup',
        'created_by',
        'updated_by',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class, 'event_id');
    }

    public function package()
    {
        return $this->belongsTo(Package::class, 'interested_package_id');
    }

    public function lostCategory()
    {
        return $this->belongsTo(LostCategory::class, 'lost_category_id');
    }

    public function salesRep()
    {
        return $this->belongsTo(User::class, 'sales_rep_id');
    }

    public function countries()
    {
        return $this->belongsToMany(Country::class, 'lead_countries', 'lead_id', 'country_id');
    }

    public function meetings()
    {
        return $this->hasMany(Meeting::class, 'lead_id');
    }

    public function activities()
    {
        return $this->hasMany(LeadActivity::class, 'lead_id');
    }
}
