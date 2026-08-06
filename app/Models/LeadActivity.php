<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeadActivity extends Model
{
    protected $table = 'lead_activities';

    public $timestamps = false;

    protected $fillable = [
        'lead_id',
        'user_id',
        'activity_type',
        'message',
        'meta',
    ];

    protected $casts = [
        'meta' => 'array',
    ];

    public function lead()
    {
        return $this->belongsTo(Lead::class, 'lead_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
