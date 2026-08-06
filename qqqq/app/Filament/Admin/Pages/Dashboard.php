<?php

namespace App\Filament\Admin\Pages;

use App\Filament\Admin\Widgets\MeetingsKpiOverview;
use App\Filament\Admin\Widgets\PermissionsStatsWidget;
use App\Filament\Admin\Widgets\TopAgentsThisWeek;
use Filament\Actions\Action;
use Filament\Pages\Dashboard as BaseDashboard;
use Illuminate\Support\Facades\Auth;

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationIcon = 'heroicon-o-home';

    protected function getHeaderActions(): array
    {
        return [
            Action::make('logout')
                ->label('Logout')
                ->icon('heroicon-o-arrow-right-on-rectangle')
                ->color('danger')
                ->requiresConfirmation()
                ->action(function () {
                    Auth::logout();
                    session()->invalidate();
                    session()->regenerateToken();
                    return redirect()->to('/admin/login');
                }),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            MeetingsKpiOverview::class,
            PermissionsStatsWidget::class,
        ];
    }

    protected function getFooterWidgets(): array
    {
        return [
            TopAgentsThisWeek::class,
        ];
    }
}
