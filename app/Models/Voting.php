<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Voting extends Model
{
    use HasFactory;

    protected $fillable = ['company_id','ip_address'];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
