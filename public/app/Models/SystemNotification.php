<?php
// app/Models/SystemNotification.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemNotification extends Model
{
    protected $table = 'notifications';

    protected $fillable = [
        'notifiable_type','notifiable_id',
        'actor_type','actor_id',
        'subject_type','subject_id','subject_slug',
        'verb','title','body','data','deeplink','web_url',
        'delivered_at','read_at',
    ];

    protected $casts = [
        'data'         => 'array',
        'delivered_at' => 'datetime',
        'read_at'      => 'datetime',
        'created_at'   => 'datetime',
        'updated_at'   => 'datetime',
    ];

    // المستلم
    public function notifiable()
    {
        return $this->morphTo(__FUNCTION__, 'notifiable_type', 'notifiable_id');
    }

    // الفاعل (اختياري)
    public function actor()
    {
        return $this->morphTo(__FUNCTION__, 'actor_type', 'actor_id');
    }

    // الموضوع/الهدف
    public function subject()
    {
        return $this->morphTo(__FUNCTION__, 'subject_type', 'subject_id');
    }

    // Scopes مفيدة
    public function scopeInbox($q, $notifiable)
    {
        return $q->where('notifiable_type', get_class($notifiable))
                 ->where('notifiable_id', $notifiable->getKey());
    }

    public function scopeUnread($q)  { return $q->whereNull('read_at'); }
    public function scopeRecent($q)  { return $q->orderByDesc('id'); }
}
