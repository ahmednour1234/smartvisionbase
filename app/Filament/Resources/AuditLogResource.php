<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AuditLogResource\Pages;
use App\Models\AuditLog;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class AuditLogResource extends Resource
{
    protected static ?string $model = AuditLog::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?int $navigationSort = 80;

    public static function getNavigationLabel(): string
    {
        return __('sv.nav.audit');
    }

    
    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()?->can('audit.view') ?? false;
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->can('audit.view') ?? false;
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('created_at')->dateTime()->label(__('sv.fields.created_at'))->sortable(),
                Tables\Columns\TextColumn::make('action')->label(__('sv.fields.action'))->badge()->sortable(),
                Tables\Columns\TextColumn::make('entity')->label(__('sv.fields.entity'))->sortable(),
                Tables\Columns\TextColumn::make('entity_id')->label(__('sv.fields.entity_id'))->sortable(),
                Tables\Columns\TextColumn::make('user.name')->label(__('sv.fields.sales_rep'))->toggleable(),
                Tables\Columns\TextColumn::make('ip')->label(__('sv.fields.ip'))->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->actions([])
            ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAuditLogs::route('/'),
        ];
    }
}
