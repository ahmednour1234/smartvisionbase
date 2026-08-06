<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MeetingResource\Pages;
use App\Models\Meeting;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class MeetingResource extends Resource
{
    protected static ?string $model = Meeting::class;

    protected static ?string $navigationIcon = 'heroicon-o-phone';

    protected static ?int $navigationSort = 20;

    public static function getNavigationLabel(): string
    {
        return __('sv.nav.meetings');
    }

    
    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()?->can('meetings.manage') ?? false;
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->can('meetings.manage') ?? false;
    }

    public static function form(Form $form): Form
    {
        $isAdmin = fn () => auth()->user()?->isAdmin() ?? false;

        return $form->schema([
            Forms\Components\Select::make('lead_id')
                ->relationship('lead', 'company_name')
                ->searchable()
                ->preload(),
            Forms\Components\DatePicker::make('meeting_date')->required()->label(__('sv.fields.meeting_date')),
            Forms\Components\Select::make('meeting_type')
                ->required()
                ->label(__('sv.fields.meeting_type'))
                ->options([
                    'call' => __('sv.meeting_type.call'),
                    'online' => __('sv.meeting_type.online'),
                    'in_person' => __('sv.meeting_type.in_person'),
                ])
                ->default('call'),
            Forms\Components\TextInput::make('duration_minutes')->numeric()->default(0)->label(__('sv.fields.duration_minutes')),
            Forms\Components\TextInput::make('notes')->maxLength(255)->label(__('sv.fields.notes')),
            Forms\Components\Select::make('user_id')
                ->label(__('sv.fields.sales_rep'))
                ->options(User::query()->orderBy('name')->pluck('name','id'))
                ->default(fn () => auth()->id())
                ->required()
                ->disabled(fn () => ! $isAdmin()),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('meeting_date')->date()->sortable()->label(__('sv.fields.meeting_date')),
                Tables\Columns\TextColumn::make('meeting_type')->badge()->label(__('sv.fields.meeting_type')),
                Tables\Columns\TextColumn::make('lead.company_name')->searchable()->label(__('sv.fields.company_name')),
                Tables\Columns\TextColumn::make('user.name')->label(__('sv.fields.sales_rep')),
                Tables\Columns\TextColumn::make('notes')->wrap()->toggleable(),
            ])
            ->defaultSort('meeting_date', 'desc')
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()->visible(fn () => auth()->user()?->isAdmin() ?? false),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageMeetings::route('/'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $q = parent::getEloquentQuery();
        $user = auth()->user();
        if ($user && method_exists($user,'isAdmin') && ! $user->isAdmin()) {
            $q->where('user_id', $user->id);
        }
        return $q;
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->can('meetings.manage') ?? false;
    }

    public static function canEdit($record): bool
    {
        return auth()->user()?->can('meetings.manage') ?? false;
    }

    public static function canDelete($record): bool
    {
        return auth()->user()?->can('meetings.manage') ?? false;
    }
}
