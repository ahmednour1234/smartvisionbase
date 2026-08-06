<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

/**
 * Enterprise-grade lead search.
 *
 * Goals:
 * - Avoid full table scans for common lookups.
 * - Prefer index-friendly strategies (prefix on email/mobile).
 * - Prefer FULLTEXT for large datasets (MySQL) when available.
 * - Provide a guarded legacy "contains" mode for compatibility.
 */
class LeadSearchService
{
    /** Cache FULLTEXT availability per PHP process. */
    private static ?bool $hasFulltext = null;

    public static function apply(Builder $q, string $termRaw, string $mode = 'smart'): Builder
    {
        $termRaw = trim($termRaw);
        if ($termRaw === '') {
            return $q;
        }

        $digits = preg_replace('/\D+/', '', $termRaw) ?? '';
        $isEmail = str_contains($termRaw, '@');

        // 1) Email: prefix search (index-friendly)
        if ($isEmail) {
            return $q->where('contact_email', 'like', $termRaw.'%');
        }

        // 2) Phone: digits/prefix search (index-friendly)
        if (is_string($digits) && strlen($digits) >= 6) {
            return $q->where(function ($w) use ($digits, $termRaw) {
                $w->where('contact_mobile', 'like', $digits.'%')
                    ->orWhere('contact_mobile', 'like', '+'.$digits.'%')
                    ->orWhere('contact_mobile', 'like', $termRaw.'%');
            });
        }

        // 3) Names/Company: SMART (default) or guarded legacy contains
        if ($mode === 'contains' && mb_strlen($termRaw) >= 3) {
            return $q->where(function ($w) use ($termRaw) {
                $w->where('company_name', 'like', '%'.$termRaw.'%')
                    ->orWhere('contact_person', 'like', '%'.$termRaw.'%')
                    // still prefer prefix for email/mobile
                    ->orWhere('contact_email', 'like', $termRaw.'%')
                    ->orWhere('contact_mobile', 'like', $termRaw.'%');
            });
        }

        // SMART mode:
        // - FULLTEXT if available and term is usable
        // - Fallback to tokenized prefix
        $tokens = self::tokens($termRaw);
        $boolean = self::booleanQuery($tokens);
        $hasFulltext = self::hasFulltextIndex();

        return $q->where(function ($w) use ($termRaw, $boolean, $tokens, $hasFulltext) {
            if ($hasFulltext && $boolean !== '') {
                $w->whereRaw('MATCH(company_name, contact_person) AGAINST (? IN BOOLEAN MODE)', [$boolean]);
                return;
            }

            // Fallback: tokenized prefix matching (best-effort)
            if (!empty($tokens)) {
                foreach ($tokens as $t) {
                    $w->where(function ($ww) use ($t) {
                        $ww->where('company_name', 'like', $t.'%')
                            ->orWhere('contact_person', 'like', $t.'%');
                    });
                }
                return;
            }

            // last resort for 1-2 chars: prefix only
            $w->where('company_name', 'like', $termRaw.'%')
                ->orWhere('contact_person', 'like', $termRaw.'%');
        });
    }

    private static function tokens(string $termRaw): array
    {
        $tokens = preg_split('/\s+/', trim($termRaw)) ?: [];
        $tokens = array_values(array_filter($tokens, fn ($t) => mb_strlen($t) >= 2));

        // Normalize to safe alnum/underscore (strip boolean operators & punctuation)
        $tokens = array_map(function ($t) {
            $clean = preg_replace('/[^\pL\pN_]+/u', '', (string) $t);
            return (string) $clean;
        }, $tokens);

        return array_values(array_filter($tokens, fn ($t) => $t !== ''));
    }

    /**
     * Boolean mode query: require each token, prefix-match each token.
     * Example: ["acme","john"] => "+acme* +john*"
     */
    private static function booleanQuery(array $tokens): string
    {
        if (empty($tokens)) {
            return '';
        }
        $parts = [];
        foreach ($tokens as $t) {
            $parts[] = '+'.$t.'*';
        }
        return trim(implode(' ', $parts));
    }

    private static function hasFulltextIndex(): bool
    {
        if (self::$hasFulltext !== null) {
            return self::$hasFulltext;
        }

        self::$hasFulltext = false;
        try {
            if (DB::getDriverName() !== 'mysql') {
                return self::$hasFulltext;
            }
            $idx = DB::select('SHOW INDEX FROM `leads` WHERE Key_name = ?', ['leads_company_contact_ft']);
            self::$hasFulltext = !empty($idx);
        } catch (\Throwable $e) {
            self::$hasFulltext = false;
        }

        return self::$hasFulltext;
    }
}
