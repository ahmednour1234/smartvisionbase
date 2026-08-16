<?php

namespace App\Support;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;

class UserNameResolver
{
    /**
     * Resolve a display name from a polymorphic model (client/sponsor/speaker or FQCN).
     *
     * @param  string|null  $typeClass  FQCN (e.g. App\Models\Client) OR morph alias (e.g. 'client'|'speaker'|'sponsor')
     * @param  int|null     $id         Primary key
     * @param  string       $lang       'ar' | 'en'
     * @return string|null
     */
    public static function name(?string $typeClass, ?int $id, string $lang = 'en'): ?string
    {
        if (!$typeClass || !$id) {
            return null;
        }

        // Resolve alias (e.g. 'sponsor') to a real class if needed
        $fqcn = self::resolveClass($typeClass);

        /** @var Model|null $model */
        $model = $fqcn && class_exists($fqcn) && method_exists($fqcn, 'query')
            ? $fqcn::query()->find($id)
            : null;

        if (!$model) {
            // Fallback: show friendly placeholder even if class not found
            $short = ucfirst(strtolower(class_basename($typeClass)));
            return "{$short} #{$id}";
        }

        // Common name/title candidates in priority order
        $candidates = [
            "name_{$lang}", "title_{$lang}",
            'display_name', 'name', 'full_name',
            fn($m) => trim(($m->first_name ?? '').' '.($m->last_name ?? '')) ?: null,
            'company_name', 'username',
        ];

        foreach ($candidates as $key) {
            if (is_callable($key)) {
                $v = $key($model);
                if ($v) return (string) $v;
            } else {
                if (isset($model->{$key}) && $model->{$key}) return (string) $model->{$key};
            }
        }

        // Secondary generic fallbacks
        foreach (['name_en', 'name_ar', 'title', 'label'] as $k) {
            if (isset($model->{$k}) && $model->{$k}) return (string) $model->{$k};
        }

        // Last resort
        $short = class_basename($fqcn ?: $typeClass);
        return "{$short} #{$id}";
    }

    /**
     * Turn a morph alias like 'sponsor' into its mapped FQCN using Relation::getMorphedModel(),
     * or guess common namespaces if no morph map exists.
     */
    private static function resolveClass(string $type): ?string
    {
        $type = ltrim($type, '\\');

        // Already a class?
        if (class_exists($type)) {
            return $type;
        }

        // Try morph map (set in AppServiceProvider::boot with Relation::enforceMorphMap)
        $mapped = Relation::getMorphedModel($type);
        if ($mapped && class_exists($mapped)) {
            return $mapped;
        }

        // Try common namespaces with StudlyCase guess
        $studly = str_replace(' ', '', ucwords(str_replace(['-', '_'], ' ', $type)));
        $guesses = [
            "App\\Models\\{$studly}",
            "App\\Models\\Chat\\{$studly}",
            "App\\{$studly}",
        ];
        foreach ($guesses as $candidate) {
            if (class_exists($candidate)) {
                return $candidate;
            }
        }

        // Could not resolve; return null and let caller fallback
        return null;
    }
}
