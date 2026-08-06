<?php

namespace App\Filament\Widgets;

use App\Models\Lead;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class LeadsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $query = Lead::query();

        $user = auth()->user();
        if ($user && method_exists($user, 'hasRole') && ! $user->hasRole('admin')) {
            $query->where('sales_rep_id', $user->id);
        }

        $total = (clone $query)->count();

        return [
            Stat::make(__('sv.stats.total_leads'), $total),
            Stat::make(__('sv.status.new'), (clone $query)->where('status', 'new')->count()),
            Stat::make(__('sv.status.contacted'), (clone $query)->where('status', 'contacted')->count()),
            Stat::make(__('sv.status.meeting'), (clone $query)->where('status', 'meeting')->count()),
            Stat::make(__('sv.status.negotiation'), (clone $query)->where('status', 'negotiation')->count()),
            Stat::make(__('sv.status.won'), (clone $query)->where('status', 'won')->count()),
            Stat::make(__('sv.status.lost'), (clone $query)->where('status', 'lost')->count()),
        ];
    }
}
