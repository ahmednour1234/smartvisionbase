<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class Company extends Model
{
    protected $guarded = [];

    protected $casts = [
        'next_followup_date' => 'date',
    ];

    protected static function boot()
    {
        parent::boot();

        static::saving(function (self $model): void {
            $normalized = (string) Str::of($model->company_name ?? '')
                ->lower()
                ->trim()
                ->replaceMatches('/[^\p{L}\p{N}]/u', '');

            $model->normalized_company_name = $normalized;

            if ($normalized === '') {
                throw ValidationException::withMessages([
                    'company_name' => 'Company name is invalid after normalization (empty).',
                ]);
            }

            $duplicateExists = static::query()
                ->where('normalized_company_name', $normalized)
                ->where('id', '!=', $model->id ?? 0)
                ->exists();

            if ($duplicateExists) {
                throw ValidationException::withMessages([
                    'company_name' => 'Company already exists.',
                ]);
            }

            if ($model->owner_id) {
                $ownerChanged = ! $model->exists || $model->isDirty('owner_id');
                if ($ownerChanged) {
                    $leadCount = static::query()
                        ->where('owner_id', $model->owner_id)
                        ->where('id', '!=', $model->id ?? 0)
                        ->count();

                    if ($leadCount >= 60) {
                        throw ValidationException::withMessages([
                            'owner_id' => 'Limit reached (60 leads).',
                        ]);
                    }
                }
            }
        });
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function package()
    {
        return $this->belongsTo(Package::class);
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function meetings()
    {
        return $this->hasMany(Meeting::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function bookedBy()
    {
        return $this->belongsTo(User::class, 'booked_by');
    }
}
