<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EventResource\Pages;
use App\Models\Event;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class EventResource extends Resource
{
    protected static ?string $model = Event::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar';

    protected static ?int $navigationSort = 21;

    public static function getNavigationLabel(): string
    {
        return __('sv.fields.event');
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
            Forms\Components\DatePicker::make('event_date_from')->label('From')->nullable(),
            Forms\Components\DatePicker::make('event_date_to')->label('To')->nullable(),
            Forms\Components\TextInput::make('location')->label('Location')->maxLength(180)->nullable(),
            Forms\Components\Toggle::make('is_active')->label(__('sv.fields.is_active'))->default(true),
            Forms\Components\TextInput::make('sort_order')->label(__('sv.fields.sort_order'))->numeric()->default(0),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('name')->label(__('sv.fields.name'))->searchable()->sortable(),
            Tables\Columns\TextColumn::make('event_date_from')->label('From')->date()->toggleable(),
            Tables\Columns\TextColumn::make('event_date_to')->label('To')->date()->toggleable(),
            Tables\Columns\TextColumn::make('location')->label('Location')->toggleable(),
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
            'index' => Pages\ListEvents::route('/'),
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
