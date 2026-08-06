<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class PermissionsStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $totalPermissions = Permission::count();
        $totalRoles = Role::count();
        $totalUsers = User::where('is_active', true)->count();
        
        $permissionsByResource = Permission::selectRaw('resource, COUNT(*) as count')
            ->whereNotNull('resource')
            ->groupBy('resource')
            ->pluck('count', 'resource')
            ->toArray();
        
        $resourcesCount = count($permissionsByResource);
        $avgPermissionsPerResource = $resourcesCount > 0 ? round(array_sum($permissionsByResource) / $resourcesCount, 1) : 0;

        return [
            Stat::make('Total Permissions', $totalPermissions)
                ->description('Across all modules')
                ->descriptionIcon('heroicon-m-key')
                ->color('primary')
                ->chart([7, 12, 15, 18, 20, $totalPermissions]),

            Stat::make('Active Roles', $totalRoles)
                ->description('Role configurations')
                ->descriptionIcon('heroicon-m-shield-check')
                ->color('success')
                ->chart([1, 2, 3, $totalRoles]),

            Stat::make('Active Users', $totalUsers)
                ->description('With assigned roles')
                ->descriptionIcon('heroicon-m-users')
                ->color('info')
                ->chart([5, 10, 15, $totalUsers]),

            Stat::make('Resource Modules', $resourcesCount)
                ->description("Avg {$avgPermissionsPerResource} permissions per module")
                ->descriptionIcon('heroicon-m-cube')
                ->color('warning'),
        ];
    }
}
