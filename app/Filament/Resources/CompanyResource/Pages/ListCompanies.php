<?php

namespace App\Filament\Resources\CompanyResource\Pages;

use App\Filament\Resources\CompanyResource;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListCompanies extends ListRecords
{
    protected static string $resource = CompanyResource::class;

    protected function applySearchToTableQuery(Builder $query): Builder
    {
        $search = trim((string) $this->getTableSearch());

        if ($search === '') {
            return $query;
        }

        $words = collect(preg_split('/\s+/', $search))
            ->map(fn ($word) => trim($word))
            ->filter(fn ($word) => mb_strlen($word) >= 2)
            ->unique()
            ->values();

        if ($words->isEmpty()) {
            return $query;
        }

        return $query->where(function (Builder $mainQuery) use ($words) {
            foreach ($words as $word) {
                $mainQuery->orWhere(function (Builder $q) use ($word) {
                    $q->where('company_name', 'like', "%{$word}%")
                        
                        ->orWhereHas('package', fn (Builder $q) => $q->where('name', 'like', "%{$word}%"))
                        ->orWhereHas('event', fn (Builder $q) => $q->where('name', 'like', "%{$word}%"))
                        ->orWhereHas('country', fn (Builder $q) => $q->where('name', 'like', "%{$word}%"))
                        ->orWhereHas('user', fn (Builder $q) => $q->where('name', 'like', "%{$word}%"));
                });
            }
        });
    }
}