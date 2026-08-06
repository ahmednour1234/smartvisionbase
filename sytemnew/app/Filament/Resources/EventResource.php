<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EventResource\Pages;
use App\Models\Event;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\FileUpload;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Filters\TrashedFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class EventResource extends Resource
{
    protected static ?string $model = Event::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar';
    protected static ?string $navigationLabel = 'Events';
    protected static ?string $modelLabel = 'Event';
    protected static ?string $pluralModelLabel = 'Events';

    public static function canViewAny(): bool
    {
        return \Illuminate\Support\Facades\Auth::user()?->hasPermission('view_any_events') ?? false;
    }

    public static function canCreate(): bool
    {
        return \Illuminate\Support\Facades\Auth::user()?->hasPermission('create_events') ?? false;
    }

    public static function canEdit($record): bool
    {
        return \Illuminate\Support\Facades\Auth::user()?->hasPermission('update_events') ?? false;
    }

    public static function canDelete($record): bool
    {
        return \Illuminate\Support\Facades\Auth::user()?->hasPermission('delete_events') ?? false;
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Event Details')
                ->schema([
                    Forms\Components\TextInput::make('name')
                        ->label('Event Name')
                        ->required()
                        ->maxLength(255),

                    Forms\Components\DatePicker::make('start_date')
                        ->label('Start Date')
                        ->required()
                        ->native(false)
                        ->displayFormat('m/d/Y'),

                    Forms\Components\DatePicker::make('end_date')
                        ->label('End Date')
                        ->required()
                        ->native(false)
                        ->displayFormat('m/d/Y')
                        ->after('start_date'),

                    Forms\Components\Textarea::make('bank_details')
                        ->label('Bank Details')
                        ->columnSpanFull(),
                ])
                ->columns(2),

            Forms\Components\Section::make('Floor Plan')
                ->schema([
                    FileUpload::make('floor_plan')
                        ->label('Floor Plan')
                        ->image()
                        ->disk('public')
                        ->directory('events/floor-plans')
                        ->visibility('public')
                        ->preserveFilenames(false)
                        ->getUploadedFileNameForStorageUsing(
                            fn (TemporaryUploadedFile $file): string =>
                                now()->format('YmdHis') . '-' .
                                Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) .
                                '.' . $file->getClientOriginalExtension()
                        )
                        ->imagePreviewHeight('250')
                        ->downloadable()
                        ->openable()
                        ->maxSize(5120)
                        ->acceptedFileTypes([
                            'image/jpeg',
                            'image/png',
                            'image/webp',
                        ])
                        ->columnSpanFull(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('floor_plan')
                    ->label('Floor Plan')
                    ->height(60)
                    ->width(100)
                    ->square(false)
                    ->getStateUsing(function (Event $record): ?string {
                        if (! $record->floor_plan) {
                            return null;
                        }

                        return asset('storage/' . $record->floor_plan);
                    })
                    ->defaultImageUrl(asset('images/no-image.png')),

                Tables\Columns\TextColumn::make('name')
                    ->label('Event Name')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('start_date')
                    ->label('Start Date')
                    ->date('m/d/Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('end_date')
                    ->label('End Date')
                    ->date('m/d/Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('deleted_at')
                    ->label('Deleted At')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\RestoreAction::make(),
                Tables\Actions\ForceDeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEvents::route('/'),
            'create' => Pages\CreateEvent::route('/create'),
            'edit' => Pages\EditEvent::route('/{record}/edit'),
        ];
    }
}