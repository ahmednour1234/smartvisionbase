<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

class Event extends Model
{
    protected $fillable = [
        'name_ar','name_en','slug','image','cover_image','row_image',
        'start_at','end_at',
        'website_url_ar','website_url_en',
        'is_active','is_featured',
    ];

    protected $casts = [
        'start_at' => 'datetime',
        'end_at'   => 'datetime',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
    ];

    // Accessors
    public function getNameAttribute(): string
    {
        return app()->getLocale() === 'ar' ? $this->name_ar : $this->name_en;
    }

    public function getWebsiteUrlAttribute(): ?string
    {
        $locale = app()->getLocale();
        return $locale === 'ar'
            ? ($this->website_url_ar ?: $this->website_url_en)
            : ($this->website_url_en ?: $this->website_url_ar);
    }

    // Scopes
    public function scopeActive(Builder $q): Builder
    {
        return $q->where('is_active', true);
    }

    public function scopeUpcoming(Builder $q): Builder
    {
        return $q->active()->where('start_at', '>', now())->orderBy('start_at');
    }

    public function scopeCurrent(Builder $q): Builder
    {
        return $q->active()->where('start_at', '<=', now())->where('end_at', '>=', now());
    }

    public function scopeNearest(Builder $q): ?self
    {
        $now = now();

        $current = (clone $q)->current()->orderBy('start_at')->first();
        if ($current) return $current;

        return (clone $q)->upcoming()->orderBy('start_at')->first();
    }

    // Helpers
    public function getStatusAttribute(): string
    {
        $now = now();
        if ($this->start_at <= $now && $this->end_at >= $now) {
            return 'live';
        }
        if ($this->start_at > $now) {
            return 'upcoming';
        }
        return 'ended';
    }
}
