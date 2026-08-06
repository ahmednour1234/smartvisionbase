<?php

namespace App\Filament\Admin\Widgets;

use App\Models\User;
use Carbon\CarbonImmutable;
use Filament\Tables;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class TopAgentsThisWeek extends BaseWidget
{
    protected static ?string $heading = 'Top Agents (Meetings This Week)';

    protected function getTableQuery(): Builder
    {
        $weekStart = CarbonImmutable::now()->startOfWeek();

        return User::query()
            ->whereHas('role', function ($query) {
                $query->where('slug', 'sales');
            })
            ->with('role')
            ->withCount([
                'meetings' => function ($query) use ($weekStart) {
                    $query->where('meeting_at', '>=', $weekStart);
                }
            ])
            ->orderByDesc('meetings_count');
    }

    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('name')->label('Agent'),
            Tables\Columns\TextColumn::make('meetings_count')->label('Meetings')->sortable(),
        ];
    }

    public function getDefaultTableRecordsPerPageSelectOption(): int
    {
        return 10;
    }
}
