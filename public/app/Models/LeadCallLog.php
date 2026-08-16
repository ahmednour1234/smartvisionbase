<?php
// app/Models/LeadCallLog.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeadCallLog extends Model
{
    protected $fillable = ['lead_id','user_id','called_at','duration_sec','outcome','notes'];

    protected $casts = [
        'called_at' => 'datetime',
        'duration_sec' => 'integer',
    ];

    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
