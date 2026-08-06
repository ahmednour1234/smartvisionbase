<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PackageResource\Pages;
use App\Models\Package;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class PackageResource extends Resource
{
    protected static ?string $model = Package::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?int $navigationSort = 22;

    public static function getNavigationLabel(): string
    {
        return __('sv.fields.package');
    }

    
    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()?->can('lookups.manage') ?? false;
    }

    public static function getNavigationGroup(): ?string
    {
        return __('sv.nav.lookups');
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')->label(__('sv.fields.name'))->required()->maxLength(180),
            Forms\Components\Toggle::make('is_active')->label(__('sv.fields.is_active'))->default(true),
            Forms\Components\TextInput::make('sort_order')->label(__('sv.fields.sort_order'))->numeric()->default(0),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('name')->label(__('sv.fields.name'))->searchable()->sortable(),
            Tables\Columns\IconColumn::make('is_active')->label(__('sv.fields.is_active'))->boolean(),
            Tables\Columns\TextColumn::make('sort_order')->label(__('sv.fields.sort_order'))->sortable(),
        ])->actions([
            Tables\Actions\EditAction::make(),
        ])->bulkActions([
            Tables\Actions\BulkActionGroup::make([
                Tables\Actions\DeleteBulkAction::make(),
            ]),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPackages::route('/'),
        ];
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->can('lookups.manage') ?? false;
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->can('lookups.manage') ?? false;
    }

    public static function canEdit($record): bool
    {
        return Auth::user()?->isAdmin() ?? false;
    }

    public static function canDelete($record): bool
    {
        return Auth::user()?->isAdmin() ?? false;
    }
}
