<?php
// app/Models/Lead.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Lead extends Model
{
    use SoftDeletes;
protected $fillable = ['name','email','phone','job','status','assigned_user_id','source','notes','next_call_at','qrcode_id'];

    public const STATUSES = ['new','follow_up','accept','reject','lost'];

    public function comments()
    {
        return $this->hasMany(LeadComment::class);
    }

    public function callLogs()
    {
        return $this->hasMany(LeadCallLog::class);
    }

    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'assigned_user_id');
    }
}
