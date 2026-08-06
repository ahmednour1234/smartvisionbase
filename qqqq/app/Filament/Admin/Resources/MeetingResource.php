<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\MeetingResource\Pages;
use App\Models\Meeting;
use App\Models\User;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class MeetingResource extends Resource
{
    protected static ?string $model = Meeting::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';

    protected static ?string $navigationLabel = 'Meetings (KPI)';

    public static function shouldRegisterNavigation(): bool
    {
        return true;
    }

    protected static function getCurrentUser(): ?User
    {
        return Filament::auth()->user();
    }

    public static function canViewAny(): bool
    {
        return true;
    }

    public static function canCreate(): bool
    {
        return true;
    }

    public static function canEdit($record): bool
    {
        return true;
    }

    public static function canDelete($record): bool
    {
        return true;
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('company_id')
                ->relationship('company', 'company_name')
                ->searchable()
                ->required(),

            Forms\Components\Select::make('user_id')
                ->relationship('user', 'name')
                ->searchable()
                ->required(),

            Forms\Components\DateTimePicker::make('meeting_at')
                ->seconds(false)
                ->required(),

            Forms\Components\TextInput::make('duration_minutes')
                ->numeric()
                ->minValue(0)
                ->maxValue(1440)
                ->default(0)
                ->required(),

            Forms\Components\Select::make('type')
                ->options([
                    'call' => 'Call',
                    'visit' => 'Visit',
                    'online' => 'Online',
                    'other' => 'Other',
                ])
                ->required(),

            Forms\Components\Select::make('outcome')
                ->options([
                    'no_answer' => 'No Answer',
                    'interested' => 'Interested',
                    'not_interested' => 'Not Interested',
                    'follow_up' => 'Follow-up',
                    'won' => 'Won',
                    'lost' => 'Lost',
                ])
                ->nullable(),

            Forms\Components\Textarea::make('notes')
                ->rows(4)
                ->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('meeting_at')->dateTime()->sortable(),
                Tables\Columns\TextColumn::make('user.name')->label('Agent')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('company.company_name')->label('Company')->searchable()->wrap(),
                Tables\Columns\TextColumn::make('type')->badge(),
                Tables\Columns\TextColumn::make('outcome')->badge()->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('duration_minutes')->label('Minutes')->sortable()->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->options([
                        'call' => 'Call',
                        'visit' => 'Visit',
                        'online' => 'Online',
                        'other' => 'Other',
                    ]),

                Tables\Filters\SelectFilter::make('outcome')
                    ->options([
                        'no_answer' => 'No Answer',
                        'interested' => 'Interested',
                        'not_interested' => 'Not Interested',
                        'follow_up' => 'Follow-up',
                        'won' => 'Won',
                        'lost' => 'Lost',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->defaultSort('meeting_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMeetings::route('/'),
            'create' => Pages\CreateMeeting::route('/create'),
            'edit' => Pages\EditMeeting::route('/{record}/edit'),
        ];
    }
}
