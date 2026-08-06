<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventMap extends Model
{
    protected $fillable = [
        'event_id',
        'company_id',
        'booking_status',
            'category_id',

        'booth',
    ];

    protected $casts = [
        'booth' => 'integer',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
    public function category()
{
    return $this->belongsTo(Category::class);
}
}