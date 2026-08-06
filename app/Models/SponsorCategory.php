<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SponsorCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'name_en',
        'active',
        'logo',
        'orders'
    ];
    public function sponsors()
{
    return $this->hasMany(Sponsor::class, 'category_sponsor_id');
}

}
