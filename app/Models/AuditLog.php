<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    protected $table = 'audit_log';

    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'action',
        'entity',
        'entity_id',
        'meta',
        'ip',
    ];

    protected $casts = [
        'meta' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
