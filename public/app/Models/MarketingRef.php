<?php
// app/Models/MarketingRef.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Str;

class MarketingRef extends Model
{
    protected $fillable = [
        'name','code','allowed_paths','active','expires_at','created_by','max_uses','uses_count'
    ];

    protected $casts = [
        'allowed_paths' => 'array',
        'expires_at'    => 'datetime',
        'active'        => 'boolean',
    ];

    protected static function booted() {
        static::creating(function ($ref) {
            if (empty($ref->code)) {
                $ref->code = strtoupper(Str::random(6)); // مثل ABC123
            }
        });
    }

    public function isUsableForPath(string $path): bool
    {
        if (!$this->active) return false;
        if ($this->expires_at && now()->greaterThan($this->expires_at)) return false;
        if (!empty($this->max_uses) && $this->uses_count >= $this->max_uses) return false;

        $allowed = $this->allowed_paths ?: [];
        if (empty($allowed)) return true; // لو فاضي يبقى كل المسارات مسموح بها

        // يتحقق بمطابقة بادئة المسار
        foreach ($allowed as $p) {
            if (Str::startsWith($path, rtrim($p, '/'))) return true;
        }
        return false;
    }


protected function shareUrl(): Attribute
{
    return Attribute::get(fn() => route('ref.hit', $this));
}
    public function hits() { return $this->hasMany(MarketingRefHit::class); }
}
