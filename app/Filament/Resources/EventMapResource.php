<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EventMapResource\Pages;
use App\Models\EventMap;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class EventMapResource extends Resource
{
    protected static ?string $model = EventMap::class;

    protected static ?string $navigationIcon = 'heroicon-o-map';
    protected static ?string $navigationLabel = 'Event Maps';
    protected static ?string $modelLabel = 'Event Map';
    protected static ?string $pluralModelLabel = 'Event Maps';

    protected static array $bookingStatuses = [
        'Booked' => 'Booked',
        'Confirmed' => 'Confirmed',
        'Complimentary' => 'Complimentary',
    ];

    public static function canViewAny(): bool
    {
        return \Illuminate\Support\Facades\Auth::user()?->hasPermission('view_any_event_maps') ?? false;
    }

    public static function canCreate(): bool
    {
        return \Illuminate\Support\Facades\Auth::user()?->hasPermission('create_event_maps') ?? false;
    }

    public static function canEdit($record): bool
    {
        return \Illuminate\Support\Facades\Auth::user()?->hasPermission('update_event_maps') ?? false;
    }

    public static function canDelete($record): bool
    {
        return \Illuminate\Support\Facades\Auth::user()?->hasPermission('delete_event_maps') ?? false;
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('event_id')
                ->label('Event')
                ->relationship('event', 'name')
                ->searchable()
                ->preload()
                ->required()
                ->native(false)
                ->default(fn () => request()->query('event_id'))
                ->disabled(fn () => request()->filled('event_id'))
                ->dehydrated(),

            Forms\Components\Select::make('company_id')
                ->label('Company')
                ->relationship('company', 'company_name')
                ->searchable()
                ->preload()
                ->required()
                ->native(false),
Forms\Components\Select::make('category_id')
    ->label('Category')
    ->relationship('category', 'name')
    ->searchable()
    ->preload()
    ->required()
    ->native(false),

            Forms\Components\TextInput::make('booth')
                ->label('Booth')
                ->numeric()
                ->required()
                ->minValue(1)
                ->maxValue(100)
                ->default(fn () => request()->query('booth'))
                ->disabled(fn () => request()->filled('booth'))
                ->dehydrated(),

            Forms\Components\Select::make('booking_status')
                ->label('Booking Status')
                ->options(static::$bookingStatuses)
                ->default('Booked')
                ->required()
                ->native(false),
        ])->columns(4);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultPaginationPageOption(100)
            ->defaultSort('booth')
            ->columns([
                Tables\Columns\TextColumn::make('booth')
                    ->label('Booth')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('company.company_name')
                    ->label('Company Name')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('event.name')
                    ->label('Event Name')
                    ->searchable()
                    ->sortable(),
Tables\Columns\TextColumn::make('company.category.name'),
                Tables\Columns\TextColumn::make('company.category.name')
                    ->label('Category')
                    ->placeholder('View Sponsorship Package'),

                Tables\Columns\SelectColumn::make('booking_status')
                    ->label('Booking Status')
                    ->options(static::$bookingStatuses)
                    ->selectablePlaceholder(false),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('event_id')
                    ->label('Event')
                    ->relationship('event', 'name')
                    ->searchable()
                    ->preload(),

                Tables\Filters\SelectFilter::make('company_id')
                    ->label('Company')
                    ->relationship('company', 'company_name')
                    ->searchable()
                    ->preload(),

                Tables\Filters\SelectFilter::make('booking_status')
                    ->label('Booking Status')
                    ->options(static::$bookingStatuses),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEventMaps::route('/'),
            'create' => Pages\CreateEventMap::route('/create'),
            'edit' => Pages\EditEventMap::route('/{record}/edit'),
        ];
    }
}