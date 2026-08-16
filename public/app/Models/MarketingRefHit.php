<?php
// app/Models/MarketingRefHit.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MarketingRefHit extends Model
{
    protected $fillable = ['marketing_ref_id','ip','user_agent','path'];

    public function ref() { return $this->belongsTo(MarketingRef::class, 'marketing_ref_id'); }
}
