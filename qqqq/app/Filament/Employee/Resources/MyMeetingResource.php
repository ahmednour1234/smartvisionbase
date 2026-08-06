<?php

namespace App\Filament\Employee\Resources;

use App\Filament\Employee\Resources\MyMeetingResource\Pages;
use App\Models\Meeting;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class MyMeetingResource extends Resource
{
    protected static ?string $model = Meeting::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';

    protected static ?string $navigationLabel = 'My Meetings';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('user_id', Auth::id());
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('company_id')
                ->relationship('company', 'company_name', fn (Builder $query) => $query->where('owner_id', Auth::id()))
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
                Tables\Columns\TextColumn::make('company.company_name')->label('Company')->searchable()->wrap(),
                Tables\Columns\TextColumn::make('type')->badge(),
                Tables\Columns\TextColumn::make('outcome')->badge()->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('duration_minutes')->label('Minutes')->sortable()->toggleable(isToggledHiddenByDefault: true),
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
            'index' => Pages\ListMyMeetings::route('/'),
            'create' => Pages\CreateMyMeeting::route('/create'),
            'edit' => Pages\EditMyMeeting::route('/{record}/edit'),
        ];
    }
}
