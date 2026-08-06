<?php

namespace App\Providers\Filament;

use Filament\Navigation\MenuItem;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->darkMode()
            ->colors([
                'primary' => Color::Blue,
            ])

            ->renderHook(
                'panels::user-menu.before',
                fn () => view('filament.components.initials-avatar')
            )

            ->userMenuItems([
                MenuItem::make()
                    ->label('My Profile')
                    ->icon('heroicon-o-user-circle')
                    ->url(fn (): string => \App\Filament\Admin\Pages\Profile::getUrl()),

                MenuItem::make()
                    ->label('Logout')
                    ->icon('heroicon-o-arrow-right-on-rectangle')
                    ->url(fn (): string => url('/admin/logout')),
            ])

            ->discoverResources(in: app_path('Filament/Admin/Resources'), for: 'App\\Filament\\Admin\\Resources')
            ->discoverPages(in: app_path('Filament/Admin/Pages'), for: 'App\\Filament\\Admin\\Pages')
            ->discoverWidgets(in: app_path('Filament/Admin/Widgets'), for: 'App\\Filament\\Admin\\Widgets')
            ->resources([
                \App\Filament\Admin\Resources\CompanyResource::class,
            ]);
    }
}
