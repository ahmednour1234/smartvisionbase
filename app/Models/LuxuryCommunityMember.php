<?php
// app/Models/LuxuryCommunityMember.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LuxuryCommunityMember extends Model
{
    protected $fillable = [
        'name_ar', 'name_en',
        'title_ar', 'title_en',
        'company', 'email', 'phone',
        'description', 'image',
        'active'
    ];
}
