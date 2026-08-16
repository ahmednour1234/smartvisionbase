<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class HomeSection extends Model
{
    protected $fillable = [
        'title',
        'description',
        'media_type',
        'media_path',
        'section_order',
        'is_active',
        'thumbnail',
    ];

    protected $casts = [
        'title'       => 'array',
        'description' => 'array',
        'is_active'   => 'boolean',
    ];

    /*
     * Scopes مفيدة
     */
    public function scopeActive(Builder $q): Builder
    {
        return $q->where('is_active', true);
    }

    public function scopeOrdered(Builder $q): Builder
    {
        return $q->orderBy('section_order');
    }

    /*
     * Accessors مريحة للواجهة
     */
    public function getTitleForLocale(string $locale = null): ?string
    {
        $locale = $locale ?: app()->getLocale();
        $title  = $this->title;

        if (is_array($title)) {
            return $title[$locale] ?? $title["title_{$locale}"] ?? $title['en'] ?? reset($title) ?: null;
        }

        return (string) ($title ?? '');
    }

    public function getDescriptionForLocale(string $locale = null): ?string
    {
        $locale = $locale ?: app()->getLocale();
        $desc   = $this->description;

        if (is_array($desc)) {
            return $desc[$locale] ?? $desc["description_{$locale}"] ?? $desc['en'] ?? reset($desc) ?: null;
        }

        return (string) ($desc ?? '');
    }
}

