<?php

namespace App\Filament\Resources\LeadResource\RelationManagers;

use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class MeetingsRelationManager extends RelationManager
{
    protected static string $relationship = 'meetings';

    public static function getTitle(\Illuminate\Database\Eloquent\Model $ownerRecord, string $pageClass): string
    {
        return __('sv.nav.meetings');
    }

    public function form(Form $form): Form
    {
        $user = Auth::user();
        $isAdmin = $user && method_exists($user, 'hasRole') ? $user->hasRole('admin') : ($user?->role === 'admin');

        return $form
            ->schema([
                Forms\Components\DatePicker::make('meeting_date')
                    ->label(__('sv.fields.meeting_date'))
                    ->required(),

                Forms\Components\Select::make('meeting_type')
                    ->label(__('sv.fields.meeting_type'))
                    ->options([
                        'call' => __('sv.meeting_type.call'),
                        'online' => __('sv.meeting_type.online'),
                        'in_person' => __('sv.meeting_type.in_person'),
                    ])
                    ->required()
                    ->default('call'),

                Forms\Components\TextInput::make('duration_minutes')
                    ->label(__('sv.fields.duration_minutes'))
                    ->numeric()
                    ->minValue(0)
                    ->default(0),

                Forms\Components\TextInput::make('notes')
                    ->label(__('sv.fields.notes'))
                    ->maxLength(255)
                    ->nullable(),

                Forms\Components\Select::make('user_id')
                    ->label(__('sv.fields.sales_rep'))
                    ->options(User::query()->active()->orderBy('name')->pluck('name', 'id'))
                    ->searchable()
                    ->preload()
                    ->default(fn () => $user?->id)
                    ->disabled(! $isAdmin)
                    ->dehydrated(true)
                    ->required(),
            ])
            ->columns(2);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('meeting_date')
                    ->label(__('sv.fields.meeting_date'))
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('meeting_type')
                    ->label(__('sv.fields.meeting_type'))
                    ->formatStateUsing(fn (?string $state) => $state ? __('sv.meeting_type.' . $state) : $state)
                    ->badge(),

                Tables\Columns\TextColumn::make('duration_minutes')
                    ->label(__('sv.fields.duration_minutes'))
                    ->sortable(),

                Tables\Columns\TextColumn::make('notes')
                    ->label(__('sv.fields.notes'))
                    ->wrap(),

                Tables\Columns\TextColumn::make('user.name')
                    ->label(__('sv.fields.sales_rep'))
                    ->toggleable(),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['lead_id'] = $this->getOwnerRecord()->id;
        if (empty($data['user_id'])) {
            $data['user_id'] = Auth::id();
        }
        return $data;
    }
}
