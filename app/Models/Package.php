<?php
// ✅ Model: Package.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    protected $fillable = [
        'name_ar', 'name_en', 'title_ar', 'title_en',
        'price', 'discount_price', 'description_ar', 'description_en',
        'sort', 'active', 'duration', 'image',
    ];
}
