<?php 
// app/Models/AttendanceCompany.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AttendanceCompany extends Model
{
    use SoftDeletes;

    protected $table = 'attendance_company';

    protected $fillable = [
        'name', 'active', 'attendance', 'attendance_at',
    ];

    protected $casts = [
        'active'         => 'boolean',
        'attendance'     => 'boolean',
        'attendance_at'  => 'datetime',
    ];

    // Scopes مفيدة
    public function scopeActive($q, $val = true)   { return $q->where('active', (bool)$val); }
    public function scopeAttended($q, $val = true) { return $q->where('attendance', (bool)$val); }
}
